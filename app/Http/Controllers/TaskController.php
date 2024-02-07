<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve tasks associated with the authenticated user
        $tasks = Auth::user()->tasks();

        // Sort tasks if requested
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
        ]);

        // Create task associated with the authenticated user
        Auth::user()->tasks()->create([
            'name' => $validatedData['task'],
            'category' => $validatedData['category'],
            'due_date' => $validatedData['due_date'],
        ]);

        return redirect('/');
    }

    public function deleteTask(Request $request)
    {
        $taskToDelete = $request->input('taskToDelete');

        // Delete task associated with the authenticated user
        Auth::user()->tasks()->where('name', $taskToDelete)->delete();

        return redirect('/');
    }
}
