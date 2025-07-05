@extends('admin.base')

@section('section_title')
{{ __('Create Email Campaign') }}
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
        <div class="card-title d-flex w-100">
            <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
            <a class="btn btn-secondary btn-sm" href="{{ route('admin.email-campaigns.index') }}">
                <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to Campaigns') }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.email-campaigns.store') }}" id="campaignForm">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <!-- Email Subject -->
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Email Subject') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                               value="{{ old('subject') }}" placeholder="Enter email subject..." required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recipient Type -->
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Recipients') }} <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="all_users" 
                                   value="all" {{ old('recipient_type', 'all') == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label" for="all_users">
                                {{ __('All Users') }} <small class="text-muted">({{ $users->count() }} users)</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="selected_users" 
                                   value="selected" {{ old('recipient_type') == 'selected' ? 'checked' : '' }}>
                            <label class="form-check-label" for="selected_users">
                                {{ __('Selected Users') }}
                            </label>
                        </div>
                        @error('recipient_type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- User Selection (Hidden by default) -->
                    <div class="form-group mb-3" id="user_selection" style="display: none;">
                        <label class="form-label">{{ __('Select Users') }}</label>
                        <select name="selected_users[]" class="form-select select2 @error('selected_users') is-invalid @enderror" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->email }}" 
                                        {{ in_array($user->email, old('selected_users', [])) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple users</small>
                        @error('selected_users')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Message -->
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Email Message') }} <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                  rows="8" placeholder="Enter your email message here..." required>{{ old('message') }}</textarea>
                        <small class="form-text text-muted">You can use HTML formatting in your message.</small>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary me-2" id="sendButton">
                            <i class="fa-solid fa-paper-plane me-1"></i>
                            {{ __('Send Campaign') }}
                        </button>
                        <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('adminExtraCSS')
<!-- Include Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@push('adminExtraJS')
<!-- Include jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select users...',
        allowClear: true,
        width: '100%'
    });

    // Show/hide user selection based on recipient type
    $('input[name="recipient_type"]').change(function() {
        if ($(this).val() === 'selected') {
            $('#user_selection').show();
        } else {
            $('#user_selection').hide();
        }
    });

    // Form submission confirmation
    $('#campaignForm').on('submit', function(e) {
        const recipientType = $('input[name="recipient_type"]:checked').val();
        let recipientCount = 0;
        
        if (recipientType === 'all') {
            recipientCount = {{ $users->count() }};
        } else {
            recipientCount = $('.select2').val() ? $('.select2').val().length : 0;
        }

        if (!confirm(`Are you sure you want to send this email campaign to ${recipientCount} recipient(s)?`)) {
            e.preventDefault();
            return false;
        }

        // Disable submit button to prevent double submission
        $('#sendButton').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Sending...');
    });

    // Initialize form state
    if ($('input[name="recipient_type"]:checked').val() === 'selected') {
        $('#user_selection').show();
    }
});
</script>
@endpush 