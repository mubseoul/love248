@extends('admin.base')

@section('section_title', 'Topic Management')

@section('section_body')
<div>
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">
                    <span><i class="fa-solid fa-list-ul me-2"></i>Topic Management</span>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </h4>
            </div>

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

            @if(isset($error))
                <div class="alert alert-warning">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    {{ $error }}
                </div>
            @endif

            <!-- Create Topic Form -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-plus me-2"></i>
                                Create New Topic
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.notifications.create-topic') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Topic Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           placeholder="Enter topic name (e.g., user-notifications)"
                                           pattern="[a-zA-Z0-9-_]+"
                                           title="Only letters, numbers, hyphens and underscores allowed">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Topic names can contain letters, numbers, hyphens, and underscores only.
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-plus me-2"></i>Create Topic
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Topic Guidelines -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Topic Guidelines
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Use descriptive names (e.g., "maintenance-alerts")
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Keep names under 256 characters
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Use hyphens or underscores for spacing
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Avoid special characters
                                </li>
                                <li class="mb-0">
                                    <i class="fa-solid fa-info text-info me-2"></i>
                                    Remember to add ARN to .env file after creation
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configured Topics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-cog me-2"></i>
                                Configured Topics
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($config && array_filter($config))
                                <div class="row">
                                    @foreach($config as $key => $arn)
                                        @if($arn)
                                            <div class="col-lg-6 mb-3">
                                                <div class="card border-start border-success border-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $key) }}</h6>
                                                            <span class="badge bg-success">Active</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <small class="text-muted">ARN:</small>
                                                        </div>
                                                        <code class="text-success small">{{ $arn }}</code>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No topics configured yet. Create some topics to get started.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Topics from AWS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-cloud me-2"></i>
                                Available Topics from AWS
                            </h5>
                            <button class="btn btn-sm btn-primary" onclick="refreshTopics()">
                                <i class="fa-solid fa-sync me-2"></i>Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="topicsContainer">
                                @if(count($topics) > 0)
                                    <div class="row">
                                        @foreach($topics as $topic)
                                            <div class="col-lg-12 mb-2">
                                                <div class="card border">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ basename($topic) }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $topic }}</small>
                                                            </div>
                                                            <button class="btn btn-sm btn-info" onclick="copyToClipboard('{{ $topic }}')">
                                                                <i class="fa-solid fa-copy me-1"></i>Copy ARN
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fa-solid fa-cloud-exclamation fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">
                                            No topics found in AWS SNS. 
                                            @if(!config('aws-sns.credentials.key'))
                                                Please configure your AWS credentials first.
                                            @else
                                                Create some topics to get started.
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Success Toast -->
<div id="copyToast" class="toast position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
    <div class="toast-header">
        <i class="fa-solid fa-check-circle text-success me-2"></i>
        <strong class="me-auto">Success</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
        Topic ARN copied to clipboard!
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const toast = new bootstrap.Toast(document.getElementById('copyToast'));
        toast.show();
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const toast = new bootstrap.Toast(document.getElementById('copyToast'));
        toast.show();
    });
}

function refreshTopics() {
    const container = document.getElementById('topicsContainer');
    const refreshBtn = document.querySelector('button[onclick="refreshTopics()"]');
    
    // Show loading state
    refreshBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Reload the page to refresh topics
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    
    if (form && nameInput) {
        nameInput.addEventListener('input', function() {
            // Remove invalid characters as user types
            this.value = this.value.replace(/[^a-zA-Z0-9-_]/g, '');
        });
    }
});
</script>
@endsection 