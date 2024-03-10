<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 

class UserController extends Controller
{
    public function updateCollaborationMode(Request $request)
    {
        $user = Auth::user();
        $user->collaborative_mode = $request->input('collaborative_mode');
        $user->save();

        return response()->json(['message' => 'Collaboration mode updated successfully']);
    }

    public function addCollaborator(Request $request)
    {
        $user = Auth::user();
        $collaboratorEmail = $request->input('collaborator_email');

        $collaborator = User::where('email', $collaboratorEmail)->first();

        if ($collaborator) {
            $user->collaborators()->attach($collaborator->id);
            return redirect()->back()->with('success', 'Collaborator added successfully');
        } else {
            return redirect()->back()->with('error', 'User with this email does not exist');
        }
    }
     public function removeCollaborator(Request $request)
    {
        $user = Auth::user();
        $collaboratorId = $request->input('collaborator_id');
        
        // Detach the collaborator from the current user
        $user->collaborators()->detach($collaboratorId);

        return redirect()->back()->with('success', 'Collaborator removed successfully');
    }
}
