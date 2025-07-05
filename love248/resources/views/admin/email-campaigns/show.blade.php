@extends('admin.base')

@section('section_title')
{{ __('Email Campaign Details') }}
@endsection

@section('section_body')
<div class="row">
    <!-- Campaign Information -->
    <div class="col-md-8">
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
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Campaign ID:') }}</label>
                            <div class="text-muted">#{{ $campaign->id }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Subject:') }}</label>
                            <div class="text-dark">{{ $campaign->subject ?? 'No Subject' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Sender:') }}</label>
                            <div class="text-muted">{{ $campaign->send_email }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Total Recipients:') }}</label>
                            <div class="text-dark">
                                <span class="badge bg-info fs-6">{{ $campaign->formatted_recipient_count }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Sent Date:') }}</label>
                            <div class="text-muted">{{ $campaign->formatted_date }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Status:') }}</label>
                            <div>
                                <span class="badge bg-{{ $campaign->status_color }}">
                                    {{ ucfirst($campaign->status ?? 'sent') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Email Message:') }}</label>
                    <div class="border p-3 rounded bg-light">
                        {!! nl2br(e($campaign->message)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipients List -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="mb-0">{{ __('Recipients') }} ({{ count($recipients) }})</h5>
            </div>
            <div class="card-body">
                @if(count($recipients) > 0)
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @foreach($recipients as $email)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-user-circle text-muted me-2"></i>
                                    <small class="text-dark">{{ $email }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted text-center py-3">
                        <i class="fa-solid fa-users-slash fs-1 mb-2"></i>
                        <div>{{ __('No recipients found') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i>
                            {{ __('Back to Campaigns') }}
                        </a>
                    </div>
                    <div>
                        @can('send-mails-delete')
                        <a href="{{ route('admin.email-campaigns.destroy', $campaign->id) }}" 
                           onclick="return confirm('{{ __('Are you sure you want to delete this email campaign? This action cannot be undone.') }}')" 
                           class="btn btn-danger">
                            <i class="fa-solid fa-trash me-1"></i>
                            {{ __('Delete Campaign') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra_bottom')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection

@push('adminExtraJS')
<script>
    // Auto-hide success alerts after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut();
    }, 5000);
</script>
@endpush 