<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/colors.css') }}" rel="stylesheet">
</head>
<body>
    @include('layouts.navbar')

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <!-- Sorting dropdown -->
                <div class="dropdown mb-3">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort By: {{ ucwords(request()->query('sort', 'due_date')) }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="{{ route('home', ['sort' => 'due_date']) }}">Due Date</a></li>
                        <li><a class="dropdown-item" href="{{ route('home', ['sort' => 'category']) }}">Category</a></li>
                    </ul>
                </div>
                <h2>Your Deadlines:</h2>
                <ul class="list-group">
                    <!-- Task list items -->
                    @foreach ($tasks as $task)
                        @php
                            // Convert the due date to the desired format
                            $dueDate = date('M d', strtotime($task['due_date']));
                            
                            $categoryColor = '';
                            $alpha = 0.2;
                            switch ($task['category']) {
                                case 'Work':
                                    $categoryColor = 'category-work'; 
                                    break;
                                case 'Personal':
                                    $categoryColor = 'category-personal'; 
                                    break;
                                case 'School':
                                    $categoryColor = 'category-school';
                                    break;
                                default:
                                    $categoryColor = 'category-gray';
                                    break;
                            }
                        @endphp
                        <li class="list-group-item {{ $categoryColor }}">
                            <!-- Task name -->
                            <span style="font-weight: bold; color: black">{{ $task['name'] }}</span>
                            <div class="task-details">
                                <!-- Category badge -->
                                @if ($task['category'])
                                    <span class="badge {{ $categoryColor }}" style="font-weight: bold; color: black; justify: right">{{ $task['category'] }}</span>
                                @endif
                                <!-- Due date badge -->
                                @if ($task['due_date'])
                                    <span class="badge bg-info" style="margin-right: 8px; justify: right">Due: {{ $dueDate }}</span>
                                @endif
                            </div>
                            <!-- Complete task button -->
                            <form action="/delete-task" method="POST">
                                @csrf
                                <input type="hidden" name="taskToDelete" value="{{ $task['name'] }}">
                                <button type="submit" class="btn btn-success btn-sm">Complete</button>
                            </form>
                        </li>
                    @endforeach

                </ul>
            </div>
            <div class="col-md-4">
                <h2>Create new deadline</h2>
                <form action="/add-task" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="task" class="form-control" required placeholder="Task" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="Work">Work</option>
                            <option value="Personal">Personal</option>
                            <option value="School">School</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Finish by:</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
