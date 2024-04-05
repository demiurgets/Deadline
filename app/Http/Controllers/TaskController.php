<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;


class TaskController extends Controller
{
    public function index(Request $request)
    {
    $userTasks = Auth::user()->tasks();

    if ($request->sort == 'category') {
        $userTasks->orderBy('category');
    } else {
        $userTasks->orderBy('due_date');
    }

    $tasks = $userTasks->get();

    return view('welcome', ['tasks' => $tasks]);
    }

    public function addTask(Request $request)
    {
        $validatedData = $request->validate([
            'task' => 'required|string',
            'category' => 'nullable|string',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string', 
        ]);
    
        $task = Auth::user()->tasks()->create([
            'name' => $validatedData['task'],
            'category' => $validatedData['category'],
            'due_date' => $validatedData['due_date'],
            'note' => $validatedData['note'], 
        ]);
    
        if ($request->has('collaborators')) {
            $collaborators = $request->input('collaborators');
            $task->collaborators()->attach($collaborators);
        }
    
        return redirect('/')->with('success', 'Task created successfully.');
    }


    public function addTaskAi(Request $request)
    {
        $maxRetries = 5; 
        $retryCount = 0;
    
        while ($retryCount < $maxRetries) {
            try {
                $validatedData = $request->validate([
                    'ainl_task' => 'required|string', 
                ]);
            
                $apiEndpoint = 'https://api.openai.com/v1/assistants';
                $apiKey = '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';
                $assistantId = 'asst_BCKhrQ64OIXkH6wDf81lCkGn'; 
        
                date_default_timezone_set('America/Chicago');
                $today = date('Y-m-d');
                $todayString =  "Today is " . date('Y-m-d, l', strtotime($today)) . ". ";
                $staticContext = "You are an assistant who reads prompts in natural language, then decides on a set of attributes for a task object. Always, no matter what, return a JSON object with the exact following attributes: 'name', 'category', 'due_date', 'note'. For the name, give a short and descriptive title under 20 chars long. For the categories, check if the topic belongs to any of these: Work, School, Personal . If not, just set the category to Personal. Due date is formatted as YYYY-MM-DD. {{ $todayString }} For the note, include any important details mentioned and generate a small note for yourself.";
                
                $updateAssistantContextResponse = OpenAI::assistants()->modify($assistantId, [
                    'instructions' => $staticContext,
                ]);
        
                $CreateThreadResponse = OpenAI::threads()->createAndRun([
                    'assistant_id' => $assistantId,
                    'thread' => [
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $request->ainl_task,
                            ],
                        ],
                    ],
                ]);
                
                // Wait for a short time to allow the AI to process the request
                sleep(3);
                
                $getThreadMessages = OpenAI::threads()->messages()->list($CreateThreadResponse->threadId, [
                    'limit' => 10,
                ]);
                $messageString = $getThreadMessages->data[0]->content[0]->text->value;
                $messageData = json_decode($messageString, true);

                // Extract task attributes from the AI response
                $newName = $messageData['name'];
                logger()->info("Name extracted");
                $newCategory = $messageData['category'];
                logger()->info("Cat extracted");
                $newDueDate = $messageData['due_date'];
                logger()->info("Date extracted");
                $newNote = $messageData['note'];
                logger()->info("Note extracted");

        

                // Create the task
                $task = Auth::user()->tasks()->create([
                    'name' => $newName,
                    'category' => $newCategory,
                    'due_date' => $newDueDate,
                    'note' => $newNote, 
                ]);
        
                if ($request->has('collaborators')) {
                    $collaborators = $request->input('collaborators');
                    $task->collaborators()->attach($collaborators);
                }
                //dd($getThreadMessages);
                
                return redirect('/')->with('success', 'Task created successfully.');
            } catch (\Exception $e) {

                $retryCount++;
        
                logger()->error('Error occurred. Retrying attempt #' . $retryCount . ': ' . $e->getMessage());
        
                sleep(1); 
            }
        }
        
        if ($retryCount === $maxRetries) {
            return redirect()->back()->with('error', 'Maximum number of retries reached.');
        }
    }
    

    
    public function deleteComplete(Request $request)
    {
        $this->deleteTask($request, true);
        return redirect('/');
    }
    public function deleteOverdue(Request $request)
    {
        $this->deleteTask($request, false);
        return redirect('/');
    }
    public function deleteTask(Request $request, bool $completed)
    {
        $taskId = $request->input('taskId');

        $task = Task::find($taskId);
        $userId = Auth::id();


        if ($task) {
            
            History::create([
                'user_id' => $userId,
                'name' => $task->name,
                'category' => $task->category,
                'due_date' => $task->due_date,
                'note' => $task->note,
                'completed' => $completed, 
            ]);

            $task->delete();

            return redirect('/');
        } else {
            return redirect('/')->with('error', 'Task not found.');
        }
    }
    public function updateTask(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category' => 'nullable|string',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string', 
        ]);

        $task->update([
            'name' => $validatedData['name'],
            'category' => $validatedData['category'],
            'due_date' => $validatedData['due_date'],
            'note' => $validatedData['note'], 
        ]);

        return redirect('/?sort=due_date');
    }
}
