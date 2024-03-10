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
    
        
        // Create the task and set the category name
        $task = Auth::user()->tasks()->create([
            'name' => $validatedData['task'],
            'category' => $validatedData['category'],
            'due_date' => $validatedData['due_date'],
            'note' => $validatedData['note'], 
        ]);
    
        // Associate collaborators with the task
        if ($request->has('collaborators')) {
            $collaborators = $request->input('collaborators');
            $task->collaborators()->attach($collaborators);
        }
    
        // Redirect back with success message
        return redirect('/')->with('success', 'Task created successfully.');
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
