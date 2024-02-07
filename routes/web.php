<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware('auth')->group(function () {
    // Redirect authenticated users to tasks page after login
    Route::get('/home', [TaskController::class, 'index'])->name('home');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/add-task', [TaskController::class, 'addTask'])->name('tasks.add');
    Route::post('/delete-task', [TaskController::class, 'deleteTask'])->name('tasks.delete');
});

Auth::routes();
