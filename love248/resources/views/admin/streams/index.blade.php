@php
    use Carbon\Carbon;
@endphp

@extends('admin.base')



@section('section_title')
<strong>{{ __('Stream Management') }}</strong>
@endsection

@section('section_body')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa fa-video fa-2x mb-3 text-primary"></i>
                <h3 class="mb-1">{{ number_format($stats['total_streams']) }}</h3>
                <p class="mb-0 text-muted">Total Streams</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa fa-play-circle fa-2x mb-3 text-success"></i>
                <h3 class="mb-1">{{ number_format($stats['active_streams']) }}</h3>
                <p class="mb-0 text-muted">Active Now</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa fa-dollar-sign fa-2x mb-3 text-info"></i>
                <h3 class="mb-1">${{ number_format($stats['revenue_today'], 2) }}</h3>
                <p class="mb-0 text-muted">Today's Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fa fa-exclamation-triangle fa-2x mb-3 text-warning"></i>
                <h3 class="mb-1">{{ number_format($stats['pending_disputes']) }}</h3>
                <p class="mb-0 text-muted">Pending Disputes</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.streams.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="interrupted" {{ request('status') == 'interrupted' ? 'selected' : '' }}>Interrupted</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Streamer</label>
                        <input type="text" name="streamer" class="form-control form-control-sm" 
                               placeholder="Search streamer..." value="{{ request('streamer') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <input type="text" name="user" class="form-control form-control-sm" 
                               placeholder="Search user..." value="{{ request('user') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Disputes</label>
                        <select name="has_disputes" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1" {{ request('has_disputes') == '1' ? 'selected' : '' }}>With Disputes</option>
                            <option value="0" {{ request('has_disputes') == '0' ? 'selected' : '' }}>No Disputes</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.streams.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-times me-1"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Streams Table -->
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100 iq-button">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">
                        @yield('section_title', '')
                        <small class="text-muted text-sm mr-3">({{ $streams->total() }} streams found)</small>
                    </h4>
                    <a href="{{ route('admin.streams.export', request()->query()) }}" class="ml-3 btn btn-primary btn-sm text-capitalize">{{ __('message.export_csv') }}</a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger mb-3">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-view table-responsive table-space">
                    <table class="table custom-table movie_table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Streamer') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Scheduled') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Disputes') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($streams as $stream)
                                <tr>
                                    <td>
                                        <strong>#{{ $stream->id }}</strong>
                                    </td>
                                    <td>
                                        @if ($stream->streamer)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $stream->streamer->profile_picture }}"
                                                     alt="{{ $stream->streamer->name }}"
                                                     class="bg-soft-primary rounded img-fluid avatar-40 me-3" />
                                                <div>
                                                    <div class="fw-bold">{{ $stream->streamer->name }}</div>
                                                    <small class="text-muted">{{ '@' . $stream->streamer->username }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('Deleted User') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($stream->user)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $stream->user->profile_picture }}"
                                                     alt="{{ $stream->user->name }}"
                                                     class="bg-soft-primary rounded img-fluid avatar-40 me-3" />
                                                <div>
                                                    <div class="fw-bold">{{ $stream->user->name }}</div>
                                                    <small class="text-muted">{{ '@' . $stream->user->username }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('Deleted User') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match ($stream->status) {
                                                'pending' => 'border-warning text-warning',
                                                'accepted' => 'border-info text-info',
                                                'in_progress' => 'border-success text-success',
                                                'completed' => 'border-primary text-primary',
                                                'cancelled' => 'border-danger text-danger',
                                                'interrupted' => 'border-secondary text-secondary',
                                                default => 'border-secondary text-secondary',
                                            };
                                        @endphp
                                        <span class="badge border status-badge {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $stream->status)) }}
                                        </span>
                                        @if ($stream->status === 'in_progress')
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fa fa-circle fa-xs"></i> Live Now
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">${{ number_format($stream->streamer_fee, 2) }}</span>
                                        @if ($stream->transactions->count() > 0)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    {{ $stream->transactions->count() }} transaction(s)
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $stream->duration_minutes }} min</span>
                                        @if ($stream->actual_start_time && $stream->actual_end_time)
                                            @php
                                                $actualDuration = Carbon::parse($stream->actual_start_time)
                                                    ->diffInMinutes(Carbon::parse($stream->actual_end_time));
                                            @endphp
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    Actual: {{ $actualDuration }} min
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            {{ Carbon::parse($stream->requested_date)->format('M j, Y') }}
                                        </div>
                                        <small class="text-muted">
                                        @php
                                            try {
                                                // Try parsing as time only first
                                                $time = Carbon::createFromFormat('H:i:s', $stream->requested_time);
                                                echo $time->format('g:i A');
                                            } catch (Exception $e) {
                                                try {
                                                    // Try parsing as datetime
                                                    $time = Carbon::parse($stream->requested_time);
                                                    echo $time->format('g:i A');
                                                } catch (Exception $e2) {
                                                    echo $stream->requested_time;
                                                }
                                            }
                                        @endphp
                                        </small>
                                    </td>
                                    <td>
                                        @if ($stream->released_at)
                                            <span class="badge border border-success text-success status-badge">
                                                <i class="fa fa-check"></i> Released
                                            </span>
                                        @elseif ($stream->status === 'completed')
                                            <span class="badge border border-warning text-warning status-badge">
                                                <i class="fa fa-clock"></i> Pending
                                            </span>
                                        @else
                                            <span class="badge border border-secondary text-secondary status-badge">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($stream->has_dispute)
                                            <span class="badge border border-danger text-danger status-badge">
                                                <i class="fa fa-exclamation-triangle"></i> Disputed
                                            </span>
                                        @else
                                            <span class="badge border border-success text-success status-badge">
                                                <i class="fa fa-check"></i> Clean
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.streams.show', $stream->id) }}" 
                                           class="btn btn-sm btn-icon btn-info rounded me-1" 
                                           data-bs-toggle="tooltip" 
                                           title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        
                                        @if (in_array($stream->status, ['pending', 'accepted', 'in_progress']))
                                            <button class="btn btn-sm btn-icon btn-warning rounded me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#cancelModal{{ $stream->id }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Cancel Stream">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        @if ($stream->status === 'in_progress')
                                            <button class="btn btn-sm btn-icon btn-danger rounded me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#interruptModal{{ $stream->id }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Interrupt Stream">
                                                <i class="fa fa-stop"></i>
                                            </button>
                                        @endif
                                        
                                        @if ($stream->status === 'completed' && !$stream->released_at)
                                            <form method="POST" action="{{ route('admin.streams.release-payment', $stream->id) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-success rounded me-1" 
                                                        onclick="return confirm('Release payment to streamer?')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Release Payment">
                                                    <i class="fa fa-dollar-sign"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Cancel Modal -->
                                <div class="modal fade" id="cancelModal{{ $stream->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.streams.cancel', $stream->id) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cancel Stream #{{ $stream->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Cancellation Reason</label>
                                                        <textarea name="cancellation_reason" class="form-control" rows="3" 
                                                                  placeholder="Enter reason for cancellation..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Cancel Stream</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Interrupt Modal -->
                                <div class="modal fade" id="interruptModal{{ $stream->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.streams.interrupt', $stream->id) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Interrupt Stream #{{ $stream->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Interruption Reason</label>
                                                        <textarea name="interruption_reason" class="form-control" rows="3" 
                                                                  placeholder="Enter reason for interruption..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Interrupt Stream</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-video fa-3x mb-3"></i>
                                            <p>{{ __('No streams found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($streams->hasPages())
                    <div class="mt-4">
                        {{ $streams->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('adminExtraJS')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush 