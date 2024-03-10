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
    <!-- Name font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Honk:MORF@13&display=swap" rel="stylesheet">
</head>
<body>
    @include('layouts.navbar')
    @php
        use App\Category;
    @endphp

    <div class="container mt-5">

        <div class="row">
            <div class="col-md-8">
                @if ($tasks instanceof Illuminate\Support\Collection && $tasks->isEmpty())
                    <h2>Looks like you have no deadlines yet! </h2>
                    </br>
                    </br>
                    <h2>Create your first one over here     ------ > <h2>
                @else
                    <h2>Your Deadlines:</h2>
                    <ul class="list-group">
                        @foreach ($tasks as $task)
                            @php
                                
                                date_default_timezone_set('America/Chicago');

                                // Convert the due date to the desired format
                                $dueDate = date('M d', strtotime($task->due_date));
                                $dueDateText = 'Due ' . date('M d', strtotime($task->due_date));

                                // Calculate the due date color class
                                $dueDateClass = '';
                                
                                // Get today's date using DateTime
                                $today = new DateTime();
                                $today->setTime(0, 0, 0);
                                $todayString = $today->format('Y-m-d');
                                
                                // Get tomorrow's date
                                $tomorrow = new DateTime('tomorrow');
                                $tomorrowString = $tomorrow->format('Y-m-d');
                                
                                if ($task->due_date < $todayString) {
                                    $dueDateClass = 'due-date-red';
                                    $dueDateText = 'Overdue';
                                } elseif ($task->due_date == $todayString) {
                                    $dueDateClass = 'due-date-orange';
                                    $dueDateText = 'Due Today!';
                                } elseif ($task->due_date == $tomorrowString) {
                                    $dueDateClass = 'due-date-yellow';
                                    $dueDateText = 'Tomorrow';
                                } else {
                                    $dueDateClass = 'due-date-green';
                                }
                                // Convert the due date to the desired format
                                $dueDate = date('M d', strtotime($task->due_date));

                                // Retrieve category color from database
                                $category = App\Models\Category::where('name', $task->category)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->first();
                                if ($category) {
                                    $categoryColor = $category->color;

                                } else {
                                    // Default to white if category not found
                                    $categoryColor = '#FFFFFF'; // white color
                                }
                            @endphp
                            <li class="list-group-item" style="background-color: {{ $categoryColor }};">
                            <span style="font-weight: bold; color: black">{{ $task->name }}</span>
                            <div class="task-details">
                                @if ($task->category)
                                    <span class="badge" style="background-color: {{ $categoryColor }}; font-weight: bold; color: black;">{{ $task->category }}</span>
                                @endif
                                    @if ($task->due_date)
                                        <span class="badge {{ $dueDateClass }}" style="margin-right: 8px; justify: right"> {{ $dueDateText }}</span>
                                    @endif
                                    <button type="button" class="btn btn-secondary btn-sm" style="margin-right: 8px;" data-bs-toggle="modal" data-bs-target="#taskModal{{ $task->id }}">
                                        Details
                                    </button>
                                    <!-- Details Modal -->
                                    <div class="modal fade"  id="taskModal{{ $task->id }}" tabindex="-1" aria-labelledby="taskModalLabel{{ $task->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content " style="background-color: {{ $categoryColor }};">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="taskModalLabel{{ $task->id }}">{{ $task->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Created: {{ $task->created_at->format('M d, Y') }}</p>
                                                    <p>{{ $task->category }}</p>
                                                    <p>Due: {{ $dueDate }}</p>
                                                    <p>Note: {{ $task->note }}</p>

                                                    <p>Collaborators:</p>
                                                    <ul>
                                                        @foreach ($task->collaborators as $collaborator)
                                                            <li style="text-align: left">{{ $collaborator->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">Edit</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Modal for editing -->
                                    <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1" aria-labelledby="editTaskModalLabel{{ $task->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content " style="background-color: {{ $categoryColor }};">
                                                <form action="{{ route('tasks.update', ['task' => $task->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editTaskModalLabel{{ $task->id }}">Edit Task</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <label for="editTaskName" class="col-sm-3 col-form-label">Task Name:</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="editTaskName" name="name" value="{{ $task->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <label for="editTaskCategory" class="col-sm-3 col-form-label">Category:</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-select" id="editTaskCategory" name="category">
                                                                @foreach(Auth::user()->categories as $category)
                                                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <label for="editTaskDueDate" class="col-sm-3 col-form-label">Due Date:</label>
                                                        <div class="col-sm-9">
                                                            <input type="date" class="form-control" id="editTaskDueDate" name="due_date" value="{{ $task->due_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <label for="editTaskNote" class="col-sm-3 col-form-label">Note:</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="editTaskNote" name="note" rows="3">{{ $task->note }}</textarea>
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
                                    @if ($dueDateText == 'Overdue') 
                                    <form action="{{ route('tasks.delete') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="taskToDelete" value="{{ $task->name }}">
                                        <button type="submit" class="btn btn-danger btn-sm">_Dismiss_</button>
                                    </form>
                                
                                @else 
                                <form action="{{ route('tasks.delete') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="taskToDelete" value="{{ $task->name }}">
                                    <button type="submit" class="btn btn-success btn-sm">Complete</button>
                                </form>
                                @endif
                            </li>
                        @endforeach
                            </br>
                    <!-- Sorting     -->
                        <div class="dropdown mb-3">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Sort By: {{ ucwords(request()->query('sort', 'due_date')) }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('home', ['sort' => 'due_date']) }}">Due Date</a></li>
                                <li><a class="dropdown-item" href="{{ route('home', ['sort' => 'category']) }}">Category</a></li>
                            </ul>
                        </div>

                    </ul>
                @endif
            </div>
            <div class="col-md-4">
                <h2>New Deadline</h2>
                <form action="{{ route('tasks.add') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="task" class="form-control" required placeholder="Title" autocomplete="off" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            @foreach(Auth::user()->categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="mb-3">
                        <label>Finish by:</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Note: (optional)</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>
                    @if(Auth::user()->collaborative_mode)
                        <div class="mb-3">
                            <label>Collaborators:</label>
                            <ul class="list-group">
                                @foreach(Auth::user()->collaborators()->where('collaborative_mode', true)->get() as $collaborator)
                                    <li>
                                        <input type="checkbox" name="collaborators[]" value="{{ $collaborator->id }}">
                                        {{ $collaborator->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
