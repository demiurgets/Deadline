<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 

class UserController extends Controller
{
    public function updateCollaborationMode(Request $request)
{
    echo "running";
    $user = Auth::user();
    $user->collaboration_mode = $request->input('collaboration_mode');
    $user->save();

    return response()->json(['message' => 'Collaboration mode updated successfully']);
}
}
