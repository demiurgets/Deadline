<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\History;


class HistoryController extends Controller
{
    

    public function index(Request $request)
    {
        // Get the ID of the currently authenticated user Auth::user()
        $userId = auth()->id();

        // Fetch completed tasks for the current user
        $completedDeadlines = History::where('user_id', $userId)
                                ->where('completed', true)
                                ->get();
        $missedDeadlines = History::where('user_id', $userId)
            ->where('completed', false)
            ->get();



        return view('history', ['completedDeadlines' => $completedDeadlines, 'missedDeadlines' => $missedDeadlines]);

    }
    public function moveComplete(Request $request, Task $task)
    {
        $task = $request->task;

    }
    
}
