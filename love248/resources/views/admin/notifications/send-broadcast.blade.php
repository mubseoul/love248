@extends('admin.base')

@section('section_title', 'Send Broadcast Notification')

@push('adminExtraCSS')
<style>
.preview-dark {
    background: #1a1d21 !important;
    color: #e2e8f0 !important;
}

.preview-dark .text-muted {
    color: #a0aec0 !important;
}

.preview-toggle {
    position: absolute;
    top: 12px;
    right: 12px;
}

.notification-preview {
    transition: all 0.3s ease;
    position: relative;
}

.notification-preview.preview-dark {
    border-color: #2d3748 !important;
}

.notification-preview .preview-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.preview-dark .preview-icon {
    background: #2d3748;
}

.preview-light .preview-icon {
    background: #e2e8f0;
}
</style>
@endpush

@section('section_body')
<div>
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">
                    <span><i class="fa-solid fa-broadcast-tower me-2"></i>Send Broadcast Notification</span>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </h4>
            </div>

            <!-- Notification Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-edit me-2"></i>
                                Compose Notification
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="fa-solid fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.notifications.process-broadcast') }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="title" class="form-label">
                                        <i class="fa-solid fa-heading me-1"></i>
                                        Notification Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}"
                                           placeholder="Enter notification title"
                                           maxlength="255">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This will be displayed as the notification headline</div>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label">
                                        <i class="fa-solid fa-message me-1"></i>
                                        Message Content <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" 
                                              name="message" 
                                              rows="4"
                                              placeholder="Enter your notification message">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">The main content of your notification</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="icon" class="form-label">
                                                <i class="fa-solid fa-image me-1"></i>
                                                Icon URL (Optional)
                                            </label>
                                            <input type="url" 
                                                   class="form-control @error('icon') is-invalid @enderror" 
                                                   id="icon" 
                                                   name="icon" 
                                                   value="{{ old('icon') }}"
                                                   placeholder="https://example.com/icon.png">
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">URL to an icon for the notification</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="url" class="form-label">
                                                <i class="fa-solid fa-link me-1"></i>
                                                Action URL (Optional)
                                            </label>
                                            <input type="url" 
                                                   class="form-control @error('url') is-invalid @enderror" 
                                                   id="url" 
                                                   name="url" 
                                                   value="{{ old('url') }}"
                                                   placeholder="https://example.com/action">
                                            @error('url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">URL to redirect users when notification is clicked</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="require_interaction" 
                                               name="require_interaction" 
                                               value="1"
                                               {{ old('require_interaction') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_interaction">
                                            <i class="fa-solid fa-hand-pointer me-1"></i>
                                            Require User Interaction
                                        </label>
                                    </div>
                                    <div class="form-text">If checked, notification will persist until user interacts with it</div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-sm btn-info" onclick="previewNotification()">
                                        <i class="fa-solid fa-eye me-2"></i>Preview
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Send Notification
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preview & Tips -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-eye me-2"></i>
                                Live Preview
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="notificationPreview" class="notification-preview p-3 border rounded preview-light">
                                <button type="button" 
                                        class="btn btn-sm preview-toggle" 
                                        onclick="togglePreviewMode()"
                                        id="previewModeToggle">
                                    <i class="fa-solid fa-moon"></i>
                                </button>
                                <div class="d-flex align-items-start">
                                    <div class="preview-icon me-3">
                                        <i class="fa-solid fa-bell text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" id="previewTitle">Your notification title will appear here</h6>
                                        <p class="mb-0 text-muted small" id="previewMessage">Your message content will be displayed here...</p>
                                        <div class="mt-2 small text-muted" id="previewUrl"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-lightbulb me-2"></i>
                                Tips & Best Practices
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Keep titles concise (under 50 characters)
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Write clear, actionable messages
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Test with small groups first
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Include relevant action URLs
                                </li>
                                <li class="mb-0">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Consider timing and user timezones
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('adminExtraJS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const messageInput = document.getElementById('message');
    const urlInput = document.getElementById('url');
    const previewTitle = document.getElementById('previewTitle');
    const previewMessage = document.getElementById('previewMessage');
    const previewUrl = document.getElementById('previewUrl');

    function updatePreview() {
        previewTitle.textContent = titleInput.value || 'Your notification title will appear here';
        previewMessage.textContent = messageInput.value || 'Your message content will be displayed here...';
        previewUrl.textContent = urlInput.value || '';
    }

    titleInput.addEventListener('input', updatePreview);
    messageInput.addEventListener('input', updatePreview);
    urlInput.addEventListener('input', updatePreview);
});

function togglePreviewMode() {
    const preview = document.getElementById('notificationPreview');
    const toggle = document.getElementById('previewModeToggle');
    
    if (preview.classList.contains('preview-dark')) {
        preview.classList.remove('preview-dark');
        preview.classList.add('preview-light');
        toggle.innerHTML = '<i class="fa-solid fa-moon"></i>';
        toggle.classList.remove('btn-dark');
        toggle.classList.add('btn-light');
    } else {
        preview.classList.remove('preview-light');
        preview.classList.add('preview-dark');
        toggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
        toggle.classList.remove('btn-light');
        toggle.classList.add('btn-dark');
    }
}

function previewNotification() {
    const title = document.getElementById('title').value;
    const message = document.getElementById('message').value;
    const url = document.getElementById('url').value;
    const isDark = document.getElementById('notificationPreview').classList.contains('preview-dark');
    
    if (!title || !message) {
        alert('Please fill in both title and message to preview the notification.');
        return;
    }
    
    // Create a temporary notification-like element
    const preview = document.createElement('div');
    preview.className = `alert alert-dismissible fade show position-fixed ${isDark ? 'bg-dark text-light' : 'bg-light'}`;
    preview.style.top = '20px';
    preview.style.right = '20px';
    preview.style.zIndex = '9999';
    preview.style.maxWidth = '400px';
    preview.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    preview.style.border = isDark ? '1px solid #2d3748' : '1px solid #e2e8f0';
    preview.innerHTML = `
        <div class="d-flex align-items-start">
            <div class="preview-icon me-3 ${isDark ? 'bg-dark' : 'bg-light'}">
                <i class="fa-solid fa-bell text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-1">${title}</h6>
                <p class="mb-0 text-muted small">${message}</p>
                ${url ? `<div class="mt-2 small text-muted">${url}</div>` : ''}
            </div>
            <button type="button" class="btn-close ${isDark ? 'btn-close-white' : ''}" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(preview);
    
    // Remove the preview after 5 seconds
    setTimeout(() => {
        if (preview.parentElement) {
            preview.remove();
        }
    }, 5000);
}
</script>
@endpush 