@extends('admin.base')

@section('section_title', 'SNS Configuration')

@section('section_body')
<div>
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">
                    <span><i class="fa-solid fa-cogs me-2"></i>SNS Configuration</span>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </h4>
            </div>

            <!-- AWS Credentials -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-key me-2"></i>
                                AWS Credentials
                            </h5>
                            <span class="badge {{ $envVars['AWS_ACCESS_KEY_ID'] ? 'bg-success' : 'bg-danger' }}">
                                {{ $envVars['AWS_ACCESS_KEY_ID'] ? 'Configured' : 'Not Set' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">AWS Access Key ID</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   value="{{ $envVars['AWS_ACCESS_KEY_ID'] ?: 'Not configured' }}" 
                                                   readonly>
                                            @if($envVars['AWS_ACCESS_KEY_ID'])
                                                <span class="input-group-text text-success">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">AWS Secret Access Key</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   value="{{ $envVars['AWS_SECRET_ACCESS_KEY'] ?: 'Not configured' }}" 
                                                   readonly>
                                            @if($envVars['AWS_SECRET_ACCESS_KEY'])
                                                <span class="input-group-text text-success">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">AWS Region</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   value="{{ $envVars['AWS_DEFAULT_REGION'] ?: 'us-east-1 (default)' }}" 
                                                   readonly>
                                            <span class="input-group-text text-info">
                                                <i class="fa-solid fa-globe"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$envVars['AWS_ACCESS_KEY_ID'] || !$envVars['AWS_SECRET_ACCESS_KEY'])
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        Credentials Required
                                    </h6>
                                    <p class="mb-0">
                                        Please add your AWS credentials to the <code>.env</code> file:
                                    </p>
                                    <pre class="mt-2 mb-0 bg-dark text-light p-2 rounded"><code>AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_DEFAULT_REGION=us-east-1</code></pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- SNS Topics Configuration -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-list me-2"></i>
                                SNS Topic Configuration
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $topics = [
                                        'general' => ['name' => 'General Notifications', 'icon' => 'fa-bell', 'color' => 'primary'],
                                        'maintenance' => ['name' => 'Maintenance Alerts', 'icon' => 'fa-tools', 'color' => 'warning'],
                                        'security' => ['name' => 'Security Alerts', 'icon' => 'fa-shield-alt', 'color' => 'danger'],
                                        'features' => ['name' => 'Feature Announcements', 'icon' => 'fa-star', 'color' => 'info'],
                                        'email' => ['name' => 'Email Notifications', 'icon' => 'fa-envelope', 'color' => 'success']
                                    ];
                                @endphp

                                @foreach($topics as $key => $topic)
                                    <div class="col-lg-6 mb-3">
                                        <div class="card border-start border-{{ $topic['color'] }} border-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa-solid {{ $topic['icon'] }} text-{{ $topic['color'] }} me-2"></i>
                                                        <h6 class="mb-0">{{ $topic['name'] }}</h6>
                                                    </div>
                                                    @if($envVars['AWS_SNS_' . strtoupper($key) . '_TOPIC_ARN'])
                                                        <span class="badge bg-success">Configured</span>
                                                    @else
                                                        <span class="badge bg-warning">Not Set</span>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Environment Variable:</small>
                                                    <code class="text-{{ $topic['color'] }}">AWS_SNS_{{ strtoupper($key) }}_TOPIC_ARN</code>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" 
                                                           class="form-control form-control-sm" 
                                                           value="{{ $envVars['AWS_SNS_' . strtoupper($key) . '_TOPIC_ARN'] ?: 'Not configured' }}" 
                                                           readonly
                                                           style="font-size: 0.8rem;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    How to Configure Topics
                                </h6>
                                <p>To set up SNS topics, you can:</p>
                                <ol class="mb-0">
                                    <li>Create topics manually in the AWS SNS Console</li>
                                    <li>Use the <a href="{{ route('admin.notifications.topics') }}" class="alert-link">Topic Management</a> section to create them programmatically</li>
                                    <li>Add the topic ARNs to your <code>.env</code> file using the environment variable names shown above</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Status -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-heartbeat me-2"></i>
                                Service Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>SNS Service</span>
                                <span id="snsStatus" class="badge bg-secondary">Unknown</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Configuration</span>
                                @if($envVars['AWS_ACCESS_KEY_ID'] && $envVars['AWS_SECRET_ACCESS_KEY'])
                                    <span class="badge bg-success">Valid</span>
                                @else
                                    <span class="badge bg-danger">Incomplete</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Topics</span>
                                @php $configuredTopics = count(array_filter([$envVars['AWS_SNS_GENERAL_TOPIC_ARN'], $envVars['AWS_SNS_MAINTENANCE_TOPIC_ARN'], $envVars['AWS_SNS_SECURITY_TOPIC_ARN'], $envVars['AWS_SNS_FEATURES_TOPIC_ARN'], $envVars['AWS_SNS_EMAIL_TOPIC_ARN']])); @endphp
                                <span class="badge {{ $configuredTopics > 0 ? 'bg-success' : 'bg-warning' }}">
                                    {{ $configuredTopics }}/5 Configured
                                </span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary w-100" onclick="checkStatus()">
                                <i class="fa-solid fa-sync me-2"></i>Check Status
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-link me-2"></i>
                                Quick Links
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.notifications.topics') }}" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-list me-2"></i>Manage Topics
                                </a>
                                <a href="https://console.aws.amazon.com/sns" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-external-link me-2"></i>AWS SNS Console
                                </a>
                                <a href="https://docs.aws.amazon.com/sns/" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-book me-2"></i>SNS Documentation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function checkStatus() {
    const statusBadge = document.getElementById('snsStatus');
    statusBadge.textContent = 'Checking...';
    statusBadge.className = 'badge bg-secondary';
    
    fetch('{{ route("admin.notifications.test-connection") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusBadge.textContent = 'Connected';
            statusBadge.className = 'badge bg-success';
        } else {
            statusBadge.textContent = 'Error';
            statusBadge.className = 'badge bg-danger';
        }
    })
    .catch(error => {
        statusBadge.textContent = 'Error';
        statusBadge.className = 'badge bg-danger';
    });
}

// Check status on page load
document.addEventListener('DOMContentLoaded', function() {
    @if($envVars['AWS_ACCESS_KEY_ID'] && $envVars['AWS_SECRET_ACCESS_KEY'])
        checkStatus();
    @else
        document.getElementById('snsStatus').textContent = 'Not Configured';
        document.getElementById('snsStatus').className = 'badge bg-warning';
    @endif
});
</script>
@endsection 