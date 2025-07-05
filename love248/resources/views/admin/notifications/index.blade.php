@extends('admin.base')

@section('section_title', 'SNS Notification Management')

@push('adminExtraCSS')
<style>
    .notification-card {
        background: #141314;
        border: 1px solid #141314;
        border-radius: 8px;
        padding: 1.5rem;
        color: #e2e8f0;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: block;
        height: 100%;
    }
    
    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        background: #141314
        color: #f7fafc;
        text-decoration: none;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .status-success { background: #22543d; color: #9ae6b4; border: 1px solid #38a169; }
    .status-warning { background: #744210; color: #fbd38d; border: 1px solid #d69e2e; }
    .status-error { background: #742a2a; color: #feb2b2; border: 1px solid #e53e3e; }
    
    .stats-card {
        background: #141314;
        border: 1px solid #141314;
        border-radius: 6px;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(5, 4, 4, 0.1);
        border-left: 3px solid #141314;
    }
    
    .quick-action {
        background: #141314;
        border: 1px solid #141314;
        border-radius: 6px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
        display: block;
        text-decoration: none;
        color: #a0aec0;
    }
    
    .quick-action:hover {
        border-color: #4a5568;
        background: #141314;
        color: #e2e8f0;
        text-decoration: none;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('section_body')
<div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">
                    <span><i class="fa-solid fa-bell me-2"></i>SNS Notification Management</span>
                    <button class="btn btn-sm btn-primary" onclick="testConnection()">
                        <i class="fa-solid fa-plug me-2"></i>Test Connection
                    </button>
                </h4>
            </div>

            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    {{ $error }}
                </div>
            @endif

            <!-- Connection Status Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">AWS Credentials</h6>
                                <div class="d-flex align-items-center">
                                    @if($hasCredentials)
                                        <span class="status-badge status-success me-2">Connected</span>
                                        <i class="fa-solid fa-check-circle text-success"></i>
                                    @else
                                        <span class="status-badge status-error me-2">Not Set</span>
                                        <i class="fa-solid fa-times-circle text-danger"></i>
                                    @endif
                                </div>
                            </div>
                            <i class="fa-solid fa-key fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Topics Configured</h6>
                                <div class="d-flex align-items-center">
                                    @if($hasTopics)
                                        <span class="status-badge status-success me-2">Ready</span>
                                        <i class="fa-solid fa-check-circle text-success"></i>
                                    @else
                                        <span class="status-badge status-warning me-2">Pending</span>
                                        <i class="fa-solid fa-exclamation-circle text-warning"></i>
                                    @endif
                                </div>
                            </div>
                            <i class="fa-solid fa-list fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Available Topics</h6>
                                <div class="d-flex align-items-center">
                                    <span class="h4 mb-0 me-2">{{ count($topics) }}</span>
                                    <small class="text-muted">Active</small>
                                </div>
                            </div>
                            <i class="fa-solid fa-broadcast-tower fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3">Quick Actions</h5>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <a href="{{ route('admin.notifications.send-broadcast') }}" class="quick-action">
                        <i class="fa-solid fa-broadcast-tower fa-2x mb-2"></i>
                        <h6 class="mb-0">Send Broadcast</h6>
                        <small>Notify all users</small>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <a href="{{ route('admin.notifications.maintenance') }}" class="quick-action">
                        <i class="fa-solid fa-tools fa-2x mb-2"></i>
                        <h6 class="mb-0">Maintenance Alert</h6>
                        <small>Schedule maintenance</small>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <a href="{{ route('admin.notifications.security') }}" class="quick-action">
                        <i class="fa-solid fa-shield-alt fa-2x mb-2"></i>
                        <h6 class="mb-0">Security Alert</h6>
                        <small>Send security notice</small>
                    </a>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="{{ route('admin.notifications.topics') }}" class="notification-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <i class="fa-solid fa-list-ul fa-2x"></i>
                            <span class="badge bg-light text-dark">{{ count($topics) }}</span>
                        </div>
                        <h5 class="mb-2">Topic Management</h5>
                        <p class="mb-0 opacity-75">Create and manage SNS topics for different notification types</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="{{ route('admin.notifications.configuration') }}" class="notification-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <i class="fa-solid fa-cogs fa-2x"></i>
                            <span class="badge bg-light text-dark">Config</span>
                        </div>
                        <h5 class="mb-2">SNS Configuration</h5>
                        <p class="mb-0 opacity-75">View and manage AWS SNS credentials and settings</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="notification-card" style="background: #1a202c; border: 1px solid #2d3748; opacity: 0.7;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <i class="fa-solid fa-chart-line fa-2x"></i>
                            <span class="badge bg-secondary text-light">Soon</span>
                        </div>
                        <h5 class="mb-2">Analytics</h5>
                        <p class="mb-0 opacity-75">Track notification delivery rates and engagement metrics</p>
                    </div>
                </div>
            </div>

            @if(!$hasCredentials || !$hasTopics)
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Setup Required
                    </h6>
                    <p class="mb-0">
                        @if(!$hasCredentials)
                            Please configure your AWS credentials in the 
                            <a href="{{ route('admin.notifications.configuration') }}" class="alert-link">configuration section</a>.
                        @endif
                        @if(!$hasTopics)
                            @if(!$hasCredentials) Also, @endif
                            Set up your SNS topics in the 
                            <a href="{{ route('admin.notifications.topics') }}" class="alert-link">topic management section</a>.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Connection Test Modal -->
<div class="modal fade" id="connectionTestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SNS Connection Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="testResult"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('adminExtraJS')
<script>
function testConnection() {
    const modal = new bootstrap.Modal(document.getElementById('connectionTestModal'));
    const resultDiv = document.getElementById('testResult');
    
    modal.show();
    resultDiv.innerHTML = '<div class="text-center"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p class="mt-2">Testing connection...</p></div>';
    
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
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fa-solid fa-check-circle me-2"></i>
                    <strong>Success!</strong> ${data.message}
                    ${data.topics_count ? `<br><small>Found ${data.topics_count} topics</small>` : ''}
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa-solid fa-times-circle me-2"></i>
                    <strong>Error!</strong> ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa-solid fa-times-circle me-2"></i>
                <strong>Error!</strong> Failed to test connection
            </div>
        `;
    });
}
</script>
@endpush 