<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Account Settings</div>
                    <div class="card-body">
                        <ul class="list-group ">
                            <li class="list-group-item card-body-custom">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateEmailModal">Update Email</button>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">Update Password</button>
                            </li>
                            <li class="list-group-item card-body-custom">
                                <div class="mb-3">
                                     <label class="form-check-label" for="collabTooltip">Collaborative Mode</label>
                                     <span class="tooltip-text" id="collabTooltip" style="font-size: 20px;" data-bs-toggle="tooltip" title="Collaborative mode will allow you to share your own deadlines with other users, and receive shared deadlines from them. You can only share deadlines with users who also have Collaborative mode on">?</span>
                                </div>
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="collaborativeSwitch" {{ Auth::user()->collaborative_mode ? 'checked' : '' }}>
                                </div>
                            </li>
                            @if(Auth::user()->collaborative_mode)
                                <li class="list-group-item card-body-custom">
                                    <form action="{{ route('users.addCollaborator') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label>Add Collaborator by Email:</label>
                                            </br>
                                            <div class="input-group">
                                                <input type="email" name="collaborator_email" class="form-control" autocomplete="off">
                                                <button type="submit" class="btn btn-primary">Add</button>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                                <li class="list-group-item card-body-custom">
                                    <div class="mb-3">
                                        <label>Current Collaborators:</label>
                                        <ul class="list-group">
                                            @foreach(Auth::user()->collaborators as $collaborator)
                                                <li class="list-group-item card-body-custom">
                                                    @if($collaborator->collaborative_mode)
                                                        <span class="badge bg-success" style="margin-left: 5px;"> {{ $collaborator->name }}</span>
                                                    @else
                                                        <span class="badge bg-danger" style="margin-left: 5px;"> {{ $collaborator->name }}</span>
                                                    @endif
                                                    <form action="{{ route('users.removeCollaborator') }}" style="padding-left: 300px;" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="collaborator_id" value="{{ $collaborator->id }}">
                                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Notifications</div>
                    <div class="card-body">
                    <form action="{{ route('settings.saveNotifPreferences') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="notificationPreference">Notification Preference:</label>
                            <select name="notification_preference" class="form-select">
                                <option value="0" {{ $notificationPreference == 0 ? 'selected' : '' }}>None</option>
                                <option value="1" {{ $notificationPreference == 1 ? 'selected' : '' }}>Email</option>
                                <option value="2" {{ $notificationPreference == 2 ? 'selected' : '' }}>SMS</option>
                                <option value="3" {{ $notificationPreference == 3 ? 'selected' : '' }}>Email and SMS</option>
                            </select>

                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                    </div>
                </div>
                </br>
                <div class="md-3">
                    <div class="card">
                        <div class="card-header">Edit Categories</div>
                        <div class="card-body">
                            <!-- Display current categories -->
                            <h5>Current Categories:</h5>
                            <div class="d-flex flex-wrap">
                                @foreach(Auth::user()->categories as $category)
                                    <button type="button" class="btn btn-secondary m-1 category-btn" data-category-id="{{ $category->id }}" data-categoryName="{{ $category->name }}">{{ $category->name }}</button>
                                @endforeach
                            </div>

                            <hr>

                            <!-- Form to add new category -->
                            <form action="{{ route('settings.addTaskCategory') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="category">Add New Category:</label>
                                    <input type="text" name="category" class="form-control" autocomplete="off">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Category</button>
                            </form>
                            </br>
                            <form action="{{ route('settings.updateCategoryColors') }}" method="POST">
                            @csrf
                            @foreach(Auth::user()->categories as $category)
                                <div class="mb-3">
                                    <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                                    <input type="color" id="category_{{ $category->id }}" name="category_colors[{{ $category->id }}]" value="{{ $category->color }}">
                                </div>
                            @endforeach
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Update Email Modal -->
    <div class="modal fade" style="color:black" id="updateEmailModal" tabindex="-1" aria-labelledby="updateEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateEmailModalLabel">Update Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('settings.email.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Email input -->
                        <div class="mb-3">
                            <label for="email" class="form-label">New Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}">
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary">Update Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Password Modal -->
    <div class="modal fade" style="color:black"id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePasswordModalLabel">Update Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('settings.password.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Current Password input -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control">
                            @error('current_password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password input -->
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm New Password input -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#collaborativeSwitch').on('change', function() {
            var isChecked = $(this).is(':checked');
            $.ajax({
                type: 'POST',
                url: '{{ route("update.collaborative.mode") }}',
                data: {
                    collaborative_mode: isChecked ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('collaborative mode updated successfully');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to update collaborative mode');
                    console.error(status);
                    console.error(error)
                }
            });
            location.reload();
        });
    });
</script>
<script>
    $(document).ready(function() {
        var originalCategoryName = ""

        $('.category-btn').mouseenter(function() {
            originalCategoryName = $(this).text();
            console.log(originalCategoryName)

            $(this).removeClass('btn-secondary').addClass('btn-danger').text('Remove');
        }).mouseleave(function() {
            console.log(originalCategoryName)

            $(this).removeClass('btn-danger').addClass('btn-secondary').text(originalCategoryName);
        }).click(function() {
            if (confirm('Are you sure you want to delete this category?')) {
                // Submit a form to delete the category
                const form = document.createElement('form');
                form.setAttribute('method', 'POST');
                form.setAttribute('action', '{{ route('settings.removeTaskCategory') }}');

                const csrfToken = document.createElement('input');
                csrfToken.setAttribute('type', 'hidden');
                csrfToken.setAttribute('name', '_token');
                csrfToken.setAttribute('value', '{{ csrf_token() }}');

                const categoryId = document.createElement('input');
                categoryId.setAttribute('type', 'hidden');
                categoryId.setAttribute('name', 'category_id');
                categoryId.setAttribute('value', $(this).data('categoryId'));

                form.appendChild(csrfToken);
                form.appendChild(categoryId);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>


</body>
</html>
