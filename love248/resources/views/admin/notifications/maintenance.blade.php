@extends('admin.base')

@section('section_title', 'Maintenance Notification')

@section('section_body')
<div>
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">
                    <span><i class="fa-solid fa-tools me-2"></i>Maintenance Notification</span>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </h4>
            </div>

            <!-- Maintenance Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-calendar-alt me-2"></i>
                                Schedule Maintenance Alert
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

                            <form action="{{ route('admin.notifications.send-maintenance') }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="scheduled_time" class="form-label">
                                        <i class="fa-solid fa-clock me-1"></i>
                                        Maintenance Time <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('scheduled_time') is-invalid @enderror" 
                                           id="scheduled_time" 
                                           name="scheduled_time" 
                                           value="{{ old('scheduled_time') }}"
                                           placeholder="e.g., Sunday, January 15th at 2:00 AM UTC">
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Specify when the maintenance will occur. Be as clear as possible about date, time, and timezone.
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Quick Time Templates</label>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-sm btn-info" onclick="setTime('in 1 hour')">
                                                    <i class="fa-solid fa-clock me-2"></i>In 1 Hour
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" onclick="setTime('tomorrow at 2:00 AM UTC')">
                                                    <i class="fa-solid fa-calendar-day me-2"></i>Tomorrow 2:00 AM UTC
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" onclick="setTime('this Sunday at 3:00 AM UTC')">
                                                    <i class="fa-solid fa-calendar-week me-2"></i>This Sunday 3:00 AM UTC
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Current Times</label>
                                            <div class="border rounded p-3">
                                                <div class="small">
                                                    <strong>Server Time:</strong><br>
                                                    <span id="serverTime">{{ now()->format('Y-m-d H:i:s T') }}</span>
                                                </div>
                                                <div class="small mt-2">
                                                    <strong>UTC Time:</strong><br>
                                                    <span id="utcTime">{{ now('UTC')->format('Y-m-d H:i:s T') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        Maintenance Alert Preview
                                    </h6>
                                    <p class="mb-1"><strong>Title:</strong> Scheduled Maintenance</p>
                                    <p class="mb-0">
                                        <strong>Message:</strong> 
                                        <span id="messagePreview">
                                            We'll be performing maintenance on <em>[your specified time]</em>. Service may be briefly interrupted.
                                        </span>
                                    </p>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-sm btn-info" onclick="previewMaintenanceAlert()">
                                        <i class="fa-solid fa-eye me-2"></i>Preview Alert
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Send Alert
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info & Guidelines -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Maintenance Best Practices
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Give at least 24 hours notice
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Schedule during low-traffic hours
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Be specific about timing and timezone
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Estimate duration if possible
                                </li>
                                <li class="mb-0">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Send follow-up when maintenance is complete
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fa-solid fa-clock me-2"></i>
                                Timing Examples
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Good:</strong>
                                <div class="small text-muted">
                                    "Sunday, January 15th from 2:00 AM to 4:00 AM UTC"
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Better:</strong>
                                <div class="small text-muted">
                                    "This weekend (Jan 15-16) starting at 2:00 AM UTC (approximately 2 hours)"
                                </div>
                            </div>
                            <div class="mb-0">
                                <strong>Best:</strong>
                                <div class="small text-muted">
                                    "Sunday, Jan 15th, 2:00-4:00 AM UTC (Saturday 9-11 PM EST, Sunday 3-5 AM CET)"
                                </div>
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
function setTime(timeString) {
    document.getElementById('scheduled_time').value = timeString;
    updatePreview();
}

function updatePreview() {
    const timeInput = document.getElementById('scheduled_time');
    const preview = document.getElementById('messagePreview');
    
    if (timeInput.value) {
        preview.innerHTML = `We'll be performing maintenance on <strong>${timeInput.value}</strong>. Service may be briefly interrupted.`;
    } else {
        preview.innerHTML = 'We\'ll be performing maintenance on <em>[your specified time]</em>. Service may be briefly interrupted.';
    }
}

function previewMaintenanceAlert() {
    const timeValue = document.getElementById('scheduled_time').value;
    
    if (!timeValue) {
        alert('Please specify a maintenance time first.');
        return;
    }
    
    const title = 'Scheduled Maintenance';
    const message = `We'll be performing maintenance on ${timeValue}. Service may be briefly interrupted.`;
    
    // Create preview notification
    const preview = document.createElement('div');
    preview.className = 'alert alert-warning alert-dismissible fade show position-fixed';
    preview.style.top = '20px';
    preview.style.right = '20px';
    preview.style.zIndex = '9999';
    preview.style.maxWidth = '400px';
    preview.innerHTML = `
        <div class="d-flex align-items-start">
            <i class="fa-solid fa-tools me-2 mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">${title}</h6>
                <p class="mb-0">${message}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(preview);
    
    // Auto remove after 8 seconds (longer for maintenance alerts)
    setTimeout(() => {
        if (preview.parentElement) {
            preview.remove();
        }
    }, 8000);
}

// Update preview when time input changes
document.addEventListener('DOMContentLoaded', function() {
    const timeInput = document.getElementById('scheduled_time');
    if (timeInput) {
        timeInput.addEventListener('input', updatePreview);
    }
    
    // Update current times every minute
    setInterval(updateCurrentTimes, 60000);
});

function updateCurrentTimes() {
    const now = new Date();
    const utcTime = new Date(now.getTime() + (now.getTimezoneOffset() * 60000));
    
    document.getElementById('serverTime').textContent = now.toLocaleString() + ' (Local)';
    document.getElementById('utcTime').textContent = utcTime.toLocaleString() + ' UTC';
}
</script>
@endsection 