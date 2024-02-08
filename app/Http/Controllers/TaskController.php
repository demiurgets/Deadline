<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Auth::user()->tasks();

        if ($request->sort == 'due_date') {
            $tasks->orderBy('due_date');
        } elseif ($request->sort == 'category') {
            $tasks->orderBy('category');
        }

        $tasks = $tasks->get();

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

        Auth::user()->tasks()->create([
            'name' => $validatedData['task'],
            'category' => $validatedData['category'],
            'due_date' => $validatedData['due_date'],
            'note' => $validatedData['note'], 
        ]);

        return redirect('/');
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
