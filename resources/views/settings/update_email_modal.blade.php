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
