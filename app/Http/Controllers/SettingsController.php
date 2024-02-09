<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        // Logic for handling the settings page
        return view('settings.index'); // Assuming your settings view is named "index.blade.php"
    }
    
    public function editEmail()
    {
        return view('settings.edit_email');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,'.Auth::id(),
        ]);

        Auth::user()->update(['email' => $request->email]);

        return redirect()->back()->with('success', 'Email updated successfully.');
    }

    public function editPassword()
    {
        return view('settings.edit_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function updatecollaborativeMode(Request $request)
    {
        $user = Auth::user();
        $user->collaborative_mode = $request->input('collaborative_mode');

        $user->save();

        return response()->json(['message' => 'Collaboration mode updated successfully']);
    }
}
