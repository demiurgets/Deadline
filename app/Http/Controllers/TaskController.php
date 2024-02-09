<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        // Create the task
        $task = Auth::user()->tasks()->create([
            'name' => $validatedData['task'],
            'category' => $validatedData['category'],
            'due_date' => $validatedData['due_date'],
            'note' => $validatedData['note'], 
        ]);

        // Initialize message variables
        $successMessage = 'Task created successfully.';
        $errorMessage = 'An error occurred while adding collaborators.';

        // Add collaborators if collaborative mode is enabled
        if (Auth::user()->collaborative_mode) {
            // Parse emails from input string
            $emails = explode(',', $request->input('collaborators'));
            $failedEmails = [];

            foreach ($emails as $email) {
                $email = trim($email);
                $user = User::where('email', $email)->first();

                if ($user && $user->collaborative_mode) {
                    $task->collaborators()->attach($user->id);
                } else {
                    // Add email to list of failed emails
                    $failedEmails[] = $email;
                }
            }

            if (count($failedEmails) > 0) {
                $errorMessage .= ' Failed emails: ' . implode(', ', $failedEmails);
                return redirect('/')->with('error', $errorMessage);
            }
        }

        return redirect('/')->with('success', $successMessage);
    }

    

    public function deleteTask(Request $request)
    {
        $taskToDelete = $request->input('taskToDelete');

        Auth::user()->tasks()->where('name', $taskToDelete)->delete();

        return redirect('/');
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
