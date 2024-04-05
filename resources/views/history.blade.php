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
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #completionRateChart {
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')
    @php
        use App\Category;
        use App\History;
    @endphp

    <div class="container mt-5">
    <div class="mt-5" style="justify-left: 400">
            <canvas id="completionRateChart"></canvas>
    </br>
    </br>
        </div>
        <div class="row">
        <div class="col-md-6">
    <!-- Left Section -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Missed Deadlines</h5>
                    @foreach ($missedDeadlines as $task)
                            @php
                                
                                date_default_timezone_set('America/Chicago');

                                // Convert the due date to the desired format
                                $dueDate = date('M d', strtotime($task->due_date));
                                $dueDateText = 'Due ' . date('M d', strtotime($task->due_date));

                                // Calculate the due date color class
                                $dueDateClass = '';
                                
                                $today = new DateTime();
                                $today->setTime(0, 0, 0);
                                $todayString = $today->format('Y-m-d');
                                
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
                                $dueDate = date('M d', strtotime($task->due_date));

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
                            <li class="list-group-item" style="background-color: {{ $categoryColor }};  padding-top: 8px; padding-left: 8px;">
                            <span style="font-weight: bold; color: black">{{ $task->name }}</span>
                            <div class="task-details">
                                @if ($task->category)
                                    <span class="badge" style="background-color: {{ $categoryColor }}; font-weight: bold; color: black;">{{ $task->category }}</span>
                                @endif
                                    <button type="button" class="btn btn-secondary btn-sm" style="margin-right: 8px;" data-bs-toggle="modal" data-bs-target="#taskModal{{ $task->id }}">
                                        Details
                                    </button>
                                    <!-- details Modal -->
                                    <div class="modal fade"  id="taskModal{{ $task->id }}" tabindex="-1" aria-labelledby="taskModalLabel{{ $task->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content " style="background-color: {{ $categoryColor }};">
                                                <form action="{{ route('history.move.complete', ['task' => $task->id]) }}" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="taskModalLabel{{ $task->id }}">{{ $task->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Created: {{ $task->created_at->format('M d, Y') }}</p>
                                                        <p>{{ $task->category }}</p>
                                                        <p>Due: {{ $dueDate }}</p>
                                                        <p>Note: {{ $task->note }}</p>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal" id="moveToCompletedBtn">Mark as Completed</button>
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">Edit</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- edit modal -->
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
                                                            <select class="form-select" id="editTaskCategory" name="category" selected="{{ $task->category }}">
                                                                @foreach(Auth::user()->categories as $category)
                                                                    <option value="{{ $category->name }}" {{ $task->category == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
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

                            </li>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Right Section -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Completed Deadlines</h5>
                    <ul>
                    @foreach ($completedDeadlines as $task)
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
                            <li class="list-group-item" style="background-color: {{ $categoryColor }}; padding-top: 8px; padding-left: 8px">
                            <span style="font-weight: bold; color: black">{{ $task->name }}</span>
                                <div class="task-details">
                                    @if ($task->category)
                                        <span class="badge" style="background-color: {{ $categoryColor }}; font-weight: bold; color: black;">{{ $task->category }}</span>
                                    @endif
                                        <button type="button" class="btn btn-secondary btn-sm" style="margin-right: 8px;" data-bs-toggle="modal" data-bs-target="#taskModal{{ $task->id }}">
                                            Details
                                        </button>
                                        <!-- details Modal -->
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

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>

        </div>
        
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var completedTasksCount = {{ count($completedDeadlines) }};
        var missedTasksCount = {{ count($missedDeadlines) }};

        var completionRateData = {
            labels: ["Completed", "Missed"],
            datasets: [{
                label: '',
                data: [completedTasksCount, missedTasksCount], 
                backgroundColor: [
                    'rgba(12, 120, 33, 0.8)', // Blue for completed
                    'rgba(157, 23, 23, 1)'  // Red for missed
                ],
                borderColor: [
                    'rgba(0, 0, 0, 1)',
                    'rgba(0, 0, 0, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Draw completion rate chart
        var completionRateChart = new Chart(document.getElementById('completionRateChart'), {
            type: 'doughnut',
            data: completionRateData,
            options: {
                responsive: false,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'bottom'
                },
                title: {
                    display: false,
                    text: 'Completion Rate'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    </script>

</body>
</html>
