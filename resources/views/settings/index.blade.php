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
                <!-- Account Settings -->
                <div class="card">
                    <div class="card-header">Account Settings</div>
                    <div class="card-body">
                        <ul class="list-group ">
                            <li class="list-group-item card-body-custom">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateEmailModal">Update Email</button>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">Update Password</button>
                            </li>
                            <li class="list-group-item card-body-custom">
                                <div class="mb-3 form-check form-switch">
                                    <label class="form-check-label" for="collaborativeSwitch">Collaborative Mode</label>
                                    <input class="form-check-input" type="checkbox" id="collaborativeSwitch" {{ Auth::user()->collaborative_mode ? 'checked' : '' }}>
                                </div>
                                <span class="tooltip-text" style="font-size: 20px;" data-bs-toggle="tooltip" title="Collaborative mode will allow you to share your own deadlines with other users, and receive shared deadlines from them.">?</span>
                            </li>
                        </ul>
                    </div>
                </div>


            </div>
            <div class="col-md-6">
                <!-- Other Settings -->
                <div class="card">
                    <div class="card-header">Future Settings</div>
                    <div class="card-body">
                        <!-- Add other settings options here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Update Email Modal -->
    <div class="modal fade" id="updateEmailModal" tabindex="-1" aria-labelledby="updateEmailModalLabel" aria-hidden="true">
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
    <div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
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
        });
    });
</script>

</body>
</html>
