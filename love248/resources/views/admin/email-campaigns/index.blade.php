@extends('admin.base')

@section('section_title')
{{ __('Email Campaigns') }}
@endsection

@section('section_body')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                    @can('send-mails-create')
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.email-campaigns.create') }}">
                            <i class="fa-solid fa-plus me-1"></i>{{ __('Create Campaign') }}
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @if ($campaigns->count() > 0)
                    <div class="table-view table-responsive table-space">
                        <table id="campaignsTable" class="table border-collapse w-full bg-white text-stone-600" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <x-th>{{ __('ID') }}</x-th>
                                    <x-th>{{ __('Subject') }}</x-th>
                                    <x-th>{{ __('Sender') }}</x-th>
                                    <x-th>{{ __('Recipients') }}</x-th>
                                    <x-th>{{ __('Status') }}</x-th>
                                    <x-th>{{ __('Sent Date') }}</x-th>
                                    <x-th>{{ __('Actions') }}</x-th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campaigns as $campaign)
                                <tr>
                                    <x-td>
                                        <x-slot name="field">{{ __('ID') }}</x-slot>
                                        {{ $campaign->id }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Subject') }}</x-slot>
                                        <div class="fw-bold">{{ $campaign->subject ?? 'No Subject' }}</div>
                                        <small class="text-muted">{{ $campaign->truncated_message }}</small>
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Sender') }}</x-slot>
                                        {{ $campaign->send_email }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Recipients') }}</x-slot>
                                        <span class="badge bg-info">{{ $campaign->formatted_recipient_count }}</span>
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Status') }}</x-slot>
                                        <span class="badge bg-{{ $campaign->status_color }}">
                                            {{ ucfirst($campaign->status ?? 'sent') }}
                                        </span>
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Sent Date') }}</x-slot>
                                        {{ $campaign->formatted_date }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Actions') }}</x-slot>
                                        <div class="d-flex align-items-center list-user-action justify-content-center">
                                            <a class="btn btn-sm btn-icon btn-info rounded me-2" 
                                               href="{{ route('admin.email-campaigns.show', $campaign->id) }}" 
                                               title="View Details">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @can('send-mails-delete')
                                            <a href="{{ route('admin.email-campaigns.destroy', $campaign->id) }}" 
                                               onclick="return confirm('{{ __('Are you sure you want to delete this email campaign?') }}')" 
                                               class="btn btn-sm btn-icon btn-danger bg-danger border-0 delete-btn rounded"
                                               title="Delete Campaign">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </x-td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($campaigns->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $campaigns->links() }}
                    </div>
                    @endif
                @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                        <use xlink:href="#exclamation-triangle-fill01" />
                    </svg>
                    <div>{{ __('No email campaigns created yet.') }}</div>
                </div>
                @endif
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