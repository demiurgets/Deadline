<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SettingsController;


Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware('auth')->group(function () {
    // Redirect authenticated users to tasks page after login
    Route::get('/home', [TaskController::class, 'index'])->name('home');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/add-task', [TaskController::class, 'addTask'])->name('tasks.add');
    Route::post('/delete-task', [TaskController::class, 'deleteTask'])->name('tasks.delete');
    Route::put('/update-task/{task}', [TaskController::class, 'updateTask'])->name('tasks.update');

    Route::post('/update-collaborative-mode', [SettingsController::class, 'updatecollaborativeMode'])->name('update.collaborative.mode');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/settings/email', [SettingsController::class, 'editEmail'])->name('settings.email.edit');
    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email.update');
    Route::get('/settings/password', [SettingsController::class, 'editPassword'])->name('settings.password.edit');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    
});

Auth::routes();
