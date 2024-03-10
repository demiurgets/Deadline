<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use Illuminate\Support\Facades\Log; 


class SettingsController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $user->task_categories = $user->task_categories ?? ['work', 'school', 'personal'];
    $user->save();

    return view('settings.index', ['user' => $user]);
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
    public function saveNotifPreferences(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'notification_preference' => 'required|integer|between:0,3', 
        ]);
        
        $user->notification_preference = $request->input('notification_preference');
        $user->save();
        
        return redirect()->back()->with('success', 'Notification preferences saved successfully.');
    }

    public function showNotifSettings()
    {
        $user = auth()->user();
        $notificationPreference = $user->notification_preference;
        
        return view('settings/index', compact('notificationPreference'));
    }
    public function addTaskCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $category = Category::firstOrCreate(
            ['name' => strtolower($request->category), 'user_id' => Auth::id()], 
            ['name' => $request->category, 'color' => '#FFFFFF', 'user_id' => Auth::id()]
        );

        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function removeTaskCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);
    
        $category = Category::find($request->category_id);
    
        if ($category && $category->user_id === Auth::id()) {
            $category->delete();
            return redirect()->back()->with('success', 'Category removed successfully.');
        }
    
        return redirect()->back()->with('error', 'Unable to remove category.');
    }
    


    public function updateCategoryColors(Request $request)
    {
        $user = Auth::user();

        foreach ($request->category_colors as $categoryId => $color) {
            $category = Category::where('id', $categoryId)
                ->where('user_id', $user->id)
                ->first();

            if ($category) {
                $category->color = $color;
                $category->save();
            }
        }

        return redirect()->back()->with('success', 'Category colors updated successfully.');
    }



}
