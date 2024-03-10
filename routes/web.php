<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoryController;



Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware('auth')->group(function () {
    // Redirect authenticated users to tasks page after login
    Route::get('/home', [TaskController::class, 'index'])->name('home');

    Route::post('/add-collaborator', [UserController::class, 'addCollaborator'])->name('users.addCollaborator');
    Route::post('/remove-collaborator', [UserController::class, 'removeCollaborator'])->name('users.removeCollaborator');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/add-task', [TaskController::class, 'addTask'])->name('tasks.add');
    Route::post('/delete-task', [TaskController::class, 'deleteTask'])->name('tasks.delete');
    Route::put('/update-task/{task}', [TaskController::class, 'updateTask'])->name('tasks.update');

    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    Route::post('/update-collaborative-mode', [SettingsController::class, 'updatecollaborativeMode'])->name('update.collaborative.mode');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/settings/email', [SettingsController::class, 'editEmail'])->name('settings.email.edit');
    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email.update');
    Route::get('/settings/password', [SettingsController::class, 'editPassword'])->name('settings.password.edit');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/save-preferences', [SettingsController::class, 'saveNotifPreferences'])->name('settings.saveNotifPreferences');
    Route::get('/settings', [SettingsController::class, 'showNotifSettings'])->name('settings');
    Route::post('/settings/add-task-category', [SettingsController::class, 'addTaskCategory'])->name('settings.addTaskCategory');
    Route::post('/settings/remove-task-category', [SettingsController::class, 'removeTaskCategory'])->name('settings.removeTaskCategory');
    Route::post('/settings/update-category-colors', [SettingsController::class, 'updateCategoryColors'])->name('settings.updateCategoryColors');

    
    
});

Auth::routes();
