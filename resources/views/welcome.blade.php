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
                            $dueDateText = 'Due ' . date('M d', strtotime($task['due_date']));

                            // Calculate the due date color class
                            $dueDateClass = '';
                            $today = date('Y-m-d');
                            $tomorrow = date('Y-m-d', strtotime('+1 day', strtotime($today)));
                            if ($task['due_date'] < $today) {
                                $dueDateClass = 'due-date-red';
                                $dueDateText = 'Overdue!!';
                            }
                            elseif ($task['due_date'] == $today) {
                                $dueDateClass = 'due-date-red';
                                $dueDateText = 'Due Today!';
                            } elseif ($task['due_date'] == $tomorrow) {
                                $dueDateClass = 'due-date-yellow';
                                $dueDateText = 'Tomorrow';
                            } else {
                                $dueDateClass = 'due-date-green';
                            }
                            // Convert the due date to the desired format
                            $dueDate = date('M d', strtotime($task['due_date']));

                            $categoryColor = '';
                            $alpha = 1;
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
                                    <span class="badge {{ $dueDateClass }}" style="margin-right: 8px; justify: right"> {{ $dueDateText }}</span>
                                @endif
                                <!-- Button to open modal -->
                                <button type="button" class="btn btn-secondary btn-sm" style="margin-right: 8px;" data-bs-toggle="modal" data-bs-target="#taskModal{{ $task['id'] }}">
                                    Details
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="taskModal{{ $task['id'] }}" tabindex="-1" aria-labelledby="taskModalLabel{{ $task['id'] }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content {{ $categoryColor }}">
                                            <!-- Modal header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="taskModalLabel{{ $task['id'] }}">{{ $task['name'] }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <!-- Task details -->
                                                <p>{{ $task['category'] }}</p>
                                                <p>Due: {{ $dueDate }}</p>
                                                <p>Note: {{ $task['note'] }}</p>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task['id'] }}">Edit Task</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal for editing -->
                                <div class="modal fade" id="editTaskModal{{ $task['id'] }}" tabindex="-1" aria-labelledby="editTaskModalLabel{{ $task['id'] }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content {{ $categoryColor }}">
                                            <form action="{{ route('tasks.update', ['task' => $task['id']]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editTaskModalLabel{{ $task['id'] }}">Edit Task</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Task name input -->
                                                <div class="row mb-3">
                                                    <label for="editTaskName" class="col-sm-3 col-form-label">Task Name</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="editTaskName" name="name" value="{{ $task['name'] }}">
                                                    </div>
                                                </div>
                                                <!-- Category dropdown -->
                                                <div class="row mb-3">
                                                    <label for="editTaskCategory" class="col-sm-3 col-form-label">Category</label>
                                                    <div class="col-sm-9">
                                                        <select class="form-select" id="editTaskCategory" name="category">
                                                            <!-- Populate options dynamically from existing categories -->
                                                            @foreach (['Work', 'Personal', 'School'] as $category)
                                                                <option value="{{ $category }}" {{ $task['category'] == $category ? 'selected' : '' }}>{{ $category }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Due date input with datepicker -->
                                                <div class="row mb-3">
                                                    <label for="editTaskDueDate" class="col-sm-3 col-form-label">Due Date</label>
                                                    <div class="col-sm-9">
                                                        <input type="date" class="form-control" id="editTaskDueDate" name="due_date" value="{{ $task['due_date'] }}">
                                                    </div>
                                                </div>
                                                <!-- Note textarea -->
                                                <div class="row mb-3">
                                                    <label for="editTaskNote" class="col-sm-3 col-form-label">Note</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control" id="editTaskNote" name="note" rows="3">{{ $task['note'] }}</textarea>
                                                    </div>
                                                </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>



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
                <h2>New Deadline</h2>
                <form action="/add-task" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="task" class="form-control" required placeholder="Title" autocomplete="off" maxlength="20">
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
                    <div class="mb-3">
                        <label>Note:</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create!</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function editTask() {
        console.log("HI")
        // Close the current modal
        $('#taskModal').modal('hide');
        
        // Open the new modal with input fields for editing
        $('#editTaskModal').modal('show');
        
        // Populate input fields with current values
        $('#taskNameInput').val($('#taskName').text());
        $('#taskCategoryInput').val($('#taskCategory').text());
        $('#taskDueDateInput').val($('#taskDueDate').text());
        $('#taskNoteInput').val($('#taskNote').text());
    }
</script>

</body>
</html>
