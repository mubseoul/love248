@php
    use Carbon\Carbon;
@endphp

@extends('admin.base')

@section('section_title')
<strong>{{ __('Stream Details') }} #{{ $stream->id }}</strong>
@endsection

@section('section_body')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center m-0">
                        <a href="{{ route('admin.streams.index') }}" class="btn  btn-sm me-3 btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
                        </a>
                        {{ __('Stream') }} #{{ $stream->id }}
                    </h4>
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

                <!-- Live Stream Video Player -->
                @if($stream->status === 'in_progress')
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-dark">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-broadcast-tower me-2"></i>
                                    <span class="badge bg-danger me-2">LIVE</span>
                                    Stream #{{ $stream->id }} - Currently Broadcasting
                                    @if($stream->actual_start_time)
                                        @php
                                            $duration = \Carbon\Carbon::parse($stream->actual_start_time)->diffInMinutes(now());
                                        @endphp
                                        <small class="ms-2">({{ $duration }} minutes running)</small>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body p-0 position-relative">
                                <!-- Video Player Container -->
                                <div class="ratio ratio-16x9 bg-dark">
                                    <video id="videoPlayer" 
                                           controls 
                                           autoplay 
                                           muted 
                                           playsinline
                                           style="width: 100%; height: 100%; object-fit: contain; background: #000;">
                                        <source src="{{ env('HLS_URL') }}/{{ $stream->stream_key }}.m3u8" type="application/x-mpegURL">
                                        <div class="d-flex align-items-center justify-content-center text-white h-100">
                                            <div class="text-center">
                                                <i class="fa fa-video fa-3x mb-3 text-muted"></i>
                                                <h5>Live Stream</h5>
                                                <p class="text-muted">Stream Key: {{ $stream->stream_key }}</p>
                                                <small class="text-warning">Video format not supported by your browser</small>
                                            </div>
                                        </div>
                                    </video>
                                </div>
                                
                                <!-- Stream Controls Overlay -->
                                <div class="position-absolute top-0 end-0 p-3">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#interruptModal"
                                                title="Interrupt Stream">
                                            <i class="fa fa-stop me-1"></i>Interrupt
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="toggleFullscreen()" title="Fullscreen">
                                            <i class="fa fa-expand"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary" onclick="toggleMute()" title="Mute/Unmute">
                                            <i class="fa fa-volume-up" id="muteIcon"></i>
                                        </button>
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interrupt Modal -->
                <div class="modal fade" id="interruptModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Interrupt Live Stream #{{ $stream->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('admin.streams.interrupt', $stream->id) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle me-2"></i>
                                        This will immediately end the live stream session.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Reason for interruption</label>
                                        <textarea name="reason" class="form-control" rows="3" 
                                                  placeholder="Please provide a reason for interrupting this stream..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-warning">Interrupt Stream</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Stream Information Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td class="fw-bold bg-footer" style="width: 200px;">{{ __('Stream ID') }}</td>
                                <td>#{{ $stream->id }}</td>
                                <td class="fw-bold bg-footer" style="width: 200px;">{{ __('Status') }}</td>
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
                                    <span class="badge border {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $stream->status)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Amount') }}</td>
                                <td>${{ number_format($stream->streamer_fee, 2) }}</td>
                                <td class="fw-bold bg-footer">{{ __('Duration') }}</td>
                                <td>{{ $stream->duration_minutes }} {{ __('minutes') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Scheduled Date') }}</td>
                                <td>{{ Carbon::parse($stream->requested_date)->format('M j, Y') }}</td>
                                <td class="fw-bold bg-footer">{{ __('Scheduled Time') }}</td>
                                <td>
                                    @php
                                        try {
                                            $time = Carbon::createFromFormat('H:i:s', $stream->requested_time);
                                            echo $time->format('g:i A');
                                        } catch (Exception $e) {
                                            try {
                                                $time = Carbon::parse($stream->requested_time);
                                                echo $time->format('g:i A');
                                            } catch (Exception $e2) {
                                                echo $stream->requested_time;
                                            }
                                        }
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Actual Start Time') }}</td>
                                <td>
                                    @if ($stream->actual_start_time)
                                        {{ Carbon::parse($stream->actual_start_time)->format('M j, Y g:i A') }}
                                    @else
                                        <span class="text-muted">{{ __('Not started') }}</span>
                                    @endif
                                </td>
                                <td class="fw-bold bg-footer">{{ __('End Time') }}</td>
                                <td>
                                    @if ($stream->stream_ended_at)
                                        {{ Carbon::parse($stream->stream_ended_at)->format('M j, Y g:i A') }}
                                    @else
                                        <span class="text-muted">{{ __('Not ended') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Created Date') }}</td>
                                <td>{{ Carbon::parse($stream->created_at)->format('M j, Y g:i A') }}</td>
                                <td class="fw-bold bg-footer">{{ __('Payment Status') }}</td>
                                <td>
                                    @if ($stream->released_at)
                                        <span class="badge border border-success text-success">{{ __('Released') }}</span>
                                        <small class="text-muted d-block">{{ Carbon::parse($stream->released_at)->format('M j, Y g:i A') }}</small>
                                        @if ($stream->release_reason)
                                            <small class="text-muted d-block">{{ __('Reason:') }} {{ $stream->release_reason }}</small>
                                        @endif
                                    @elseif ($stream->status === 'completed')
                                        <span class="badge border border-warning text-warning">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge border border-secondary text-secondary">{{ __('N/A') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($stream->message)
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Special Requests') }}</td>
                                <td colspan="3">{{ $stream->message }}</td>
                            </tr>
                            @endif
                            @if ($stream->cancellation_reason)
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Cancellation Reason') }}</td>
                                <td colspan="3">{{ $stream->cancellation_reason }}</td>
                            </tr>
                            @endif
                            @if ($stream->interruption_reason)
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Interruption Reason') }}</td>
                                <td colspan="3">{{ $stream->interruption_reason }}</td>
                            </tr>
                            @endif
                            @if ($stream->refunded_at)
                            <tr>
                                <td class="fw-bold bg-footer">{{ __('Refund Status') }}</td>
                                <td colspan="3">
                                    <span class="badge border border-info text-info">{{ __('Refunded') }}</span>
                                    <span class="fw-bold text-success">${{ number_format($stream->refund_amount, 2) }}</span>
                                    <small class="text-muted d-block">{{ __('Refunded on:') }} {{ Carbon::parse($stream->refunded_at)->format('M j, Y g:i A') }}</small>
                                    @if ($stream->refund_reason)
                                        <small class="text-muted d-block">{{ __('Reason:') }} {{ $stream->refund_reason }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Participants Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>{{ __('Streamer Information') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    @if ($stream->streamer)
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Name') }}</td>
                                            <td>{{ $stream->streamer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Username') }}</td>
                                            <td>{{ '@' . $stream->streamer->username }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Email') }}</td>
                                            <td>{{ $stream->streamer->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Profile') }}</td>
                                            <td>
                                                <img src="{{ $stream->streamer->profile_picture }}" 
                                                     alt="{{ $stream->streamer->name }}" 
                                                     class="rounded img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-muted text-center">{{ __('Streamer account deleted') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>{{ __('Customer Information') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    @if ($stream->user)
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Name') }}</td>
                                            <td>{{ $stream->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Username') }}</td>
                                            <td>{{ '@' . $stream->user->username }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Email') }}</td>
                                            <td>{{ $stream->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-footer">{{ __('Profile') }}</td>
                                            <td>
                                                <img src="{{ $stream->user->profile_picture }}" 
                                                     alt="{{ $stream->user->name }}" 
                                                     class="rounded img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-muted text-center">{{ __('Customer account deleted') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                <div class="mb-4">
                    <h5>{{ __('Admin Actions') }}</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        @if (in_array($stream->status, ['pending', 'accepted', 'in_progress']))
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fa fa-times me-1"></i> {{ __('Cancel Stream') }}
                            </button>
                        @endif
                        
                        @if ($stream->status === 'in_progress')
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#interruptModal">
                                <i class="fa fa-stop me-1"></i> {{ __('Interrupt Stream') }}
                            </button>
                        @endif
                        
                        @if ($stream->status === 'completed' && !$stream->released_at)
                            <form method="POST" action="{{ route('admin.streams.release-payment', $stream->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" 
                                        onclick="return confirm('{{ __('Release payment to streamer?') }}')">
                                    <i class="fa fa-dollar-sign me-1"></i> {{ __('Release Payment') }}
                                </button>
                            </form>
                        @endif



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transactions -->
@if ($stream->transactions->count() > 0)
<div class="row mt-4">
    <div class="col-sm-12">
        <div class="card p-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Transactions') }} ({{ $stream->transactions->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-view table-responsive table-space">
                    <table class="table custom-table movie_table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stream->transactions as $transaction)
                                <tr>
                                    <td>#{{ $transaction->id }}</td>
                                    <td>
                                        @if ($transaction->user)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $transaction->user->profile_picture }}"
                                                    alt="{{ $transaction->user->name }}"
                                                    class="bg-soft-primary rounded img-fluid avatar-40 me-3" />
                                                <div>
                                                    <div class="fw-bold">{{ $transaction->user->name }}</div>
                                                    <small class="text-muted">{{ '@' . $transaction->user->username }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('Deleted User') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $typeClass = match ($transaction->transaction_type) {
                                                'private_stream_earning' => 'border-success text-success',
                                                'private_stream_fee' => 'border-primary text-primary',
                                                'room_rental' => 'border-warning text-warning',
                                                default => 'border-info text-info',
                                            };
                                        @endphp
                                        <span class="mt-2 badge border {{ $typeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match ($transaction->status) {
                                                'completed' => 'border-success text-success',
                                                'pending' => 'border-warning text-warning',
                                                'failed' => 'border-danger text-danger',
                                                default => 'border-secondary text-secondary',
                                            };
                                        @endphp
                                        <span class="mt-2 badge border {{ $statusClass }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ Carbon::parse($transaction->created_at)->format('jS F Y') }}</div>
                                        <small class="text-muted">{{ Carbon::parse($transaction->created_at)->format('g:i A') }}</small>
                                    </td>
                                    <td>{{ $transaction->description ?: __('No description') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-receipt fa-3x mb-3"></i>
                                            <p>{{ __('No transactions found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Feedback & Reviews -->
@if ($stream->feedbacks->count() > 0)
<div class="row mt-4">
    <div class="col-sm-12">
        <div class="card p-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Feedback & Reviews') }} ({{ $stream->feedbacks->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-view table-responsive table-space">
                    <table class="table custom-table movie_table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Rating') }}</th>
                                <th>{{ __('Comment') }}</th>
                                <th>{{ __('Issues') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stream->feedbacks as $feedback)
                                <tr>
                                    <td>#{{ $feedback->id }}</td>
                                    <td>
                                        @if ($feedback->user)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $feedback->user->profile_picture }}"
                                                     alt="{{ $feedback->user->name }}"
                                                     class="bg-soft-primary rounded img-fluid avatar-40 me-3" />
                                                <div>
                                                    <div class="fw-bold">{{ $feedback->user->name }}</div>
                                                    <small class="text-muted">{{ '@' . $feedback->user->username }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('Deleted User') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $typeClass = match ($feedback->feedback_type) {
                                                'user' => 'border-primary text-primary',
                                                'streamer' => 'border-success text-success',
                                                default => 'border-info text-info',
                                            };
                                        @endphp
                                        <span class="mt-2 badge border {{ $typeClass }}">
                                            {{ ucfirst($feedback->feedback_type ?? 'User') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($feedback->rating)
                                            <div class="d-flex align-items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                <span class="ms-2 fw-bold">{{ $feedback->rating }}/5</span>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($feedback->comment)
                                            <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" 
                                                 title="{{ $feedback->comment }}">
                                                {{ $feedback->comment }}
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('No comment') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($feedback->hasIssues())
                                            <span class="mt-2 badge border border-danger text-danger">{{ __('Issues Reported') }}</span>
                                        @else
                                            <span class="mt-2 badge border border-success text-success">{{ __('No Issues') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ Carbon::parse($feedback->created_at)->format('jS F Y') }}</div>
                                        <small class="text-muted">{{ Carbon::parse($feedback->created_at)->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#feedbackModal{{ $feedback->id }}">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-comments fa-3x mb-3"></i>
                                            <p>{{ __('No feedback found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Dispute Management -->
@if ($stream->has_dispute)
<div class="row mt-4">
    <div class="col-sm-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fa fa-exclamation-triangle me-2"></i>{{ __('Dispute Management') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>{{ __('This stream has an active dispute.') }}</strong>
                    @if ($stream->dispute_created_at)
                        <div class="mt-2">
                            <small>{{ __('Dispute created:') }} {{ Carbon::parse($stream->dispute_created_at)->format('M j, Y g:i A') }}</small>
                        </div>
                    @endif
                </div>
                
                @if (!$stream->dispute_resolved_at)
                    <form method="POST" action="{{ route('admin.streams.resolve-dispute', $stream->id) }}" class="mt-3">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Resolution Decision') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="resolution" value="favor_user" id="favorUser" required>
                                    <label class="form-check-label text-primary" for="favorUser">
                                        <strong>{{ __('Favor User') }}</strong> - Refund full amount to user
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="resolution" value="favor_streamer" id="favorStreamer" required>
                                    <label class="form-check-label text-success" for="favorStreamer">
                                        <strong>{{ __('Favor Streamer') }}</strong> - Release payment to streamer
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <small>
                                        <strong>User:</strong> {{ $stream->user->username ?? 'Deleted' }}<br>
                                        <strong>Streamer:</strong> {{ $stream->streamer->username ?? 'Deleted' }}<br>
                                        <strong>Amount:</strong> ${{ number_format($stream->streamer_fee, 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Resolution Reason') }}</label>
                            <textarea name="resolution_reason" class="form-control" rows="3" 
                                      placeholder="{{ __('Explain why you decided in favor of this party...') }}" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa fa-gavel me-1"></i> {{ __('Resolve Dispute') }}
                        </button>
                    </form>
                @else
                    <div class="alert alert-success">
                        <strong>{{ __('Dispute Resolved') }}</strong>
                        <div class="mt-2">
                            <small>{{ __('Resolved:') }} {{ Carbon::parse($stream->dispute_resolved_at)->format('M j, Y g:i A') }}</small>
                            @if ($stream->disputeResolver)
                                <br><small>{{ __('By:') }} {{ $stream->disputeResolver->name }}</small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.streams.cancel', $stream->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Cancel Stream') }} #{{ $stream->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Cancellation Reason') }}</label>
                        <textarea name="reason" class="form-control" rows="3" 
                                  placeholder="{{ __('Enter reason for cancellation...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Cancel Stream') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Interrupt Modal -->
<div class="modal fade" id="interruptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.streams.interrupt', $stream->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Interrupt Stream') }} #{{ $stream->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Interruption Reason') }}</label>
                        <textarea name="reason" class="form-control" rows="3" 
                                  placeholder="{{ __('Enter reason for interruption...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Interrupt Stream') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Feedback Detail Modals -->
@foreach($stream->feedbacks as $feedback)
<div class="modal fade" id="feedbackModal{{ $feedback->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-comment me-2"></i>{{ __('Feedback Details') }} #{{ $feedback->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- User Information -->
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-muted">{{ __('User Information') }}</h6>
                        @if ($feedback->user)
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $feedback->user->profile_picture }}"
                                     alt="{{ $feedback->user->name }}"
                                     class="bg-soft-primary rounded img-fluid me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;" />
                                <div>
                                    <div class="fw-bold">{{ $feedback->user->name }}</div>
                                    <small class="text-muted">{{ '@' . $feedback->user->username }}</small>
                                    <br><small class="text-muted">{{ $feedback->user->email }}</small>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">{{ __('User account has been deleted') }}</div>
                        @endif
                    </div>

                    <!-- Feedback Type & Rating -->
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-muted">{{ __('Feedback Details') }}</h6>
                        <div class="mb-2">
                            <strong>{{ __('Type:') }}</strong>
                            @php
                                $typeClass = match ($feedback->feedback_type) {
                                    'user' => 'border-primary text-primary',
                                    'streamer' => 'border-success text-success',
                                    default => 'border-info text-info',
                                };
                            @endphp
                            <span class="badge border {{ $typeClass }} ms-2">
                                {{ ucfirst($feedback->feedback_type ?? 'User') }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('Rating:') }}</strong>
                            @if ($feedback->rating)
                                <div class="d-inline-flex align-items-center ms-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 fw-bold">{{ $feedback->rating }}/5</span>
                                </div>
                            @else
                                <span class="text-muted ms-2">{{ __('No rating provided') }}</span>
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('Issues:') }}</strong>
                            @if ($feedback->hasIssues())
                                <span class="badge border-danger text-danger ms-2">{{ __('Issues Reported') }}</span>
                            @else
                                <span class="badge border-success text-success ms-2">{{ __('No Issues') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comment Section -->
                @if ($feedback->comment)
                    <div class="mb-3">
                        <h6 class="fw-bold text-muted">{{ __('Comment') }}</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $feedback->comment }}</p>
                        </div>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted">{{ __('Submitted') }}</h6>
                        <div>{{ Carbon::parse($feedback->created_at)->format('l, F j, Y') }}</div>
                        <small class="text-muted">{{ Carbon::parse($feedback->created_at)->format('g:i A') }}</small>
                    </div>
                    @if ($feedback->updated_at != $feedback->created_at)
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted">{{ __('Last Updated') }}</h6>
                        <div>{{ Carbon::parse($feedback->updated_at)->format('l, F j, Y') }}</div>
                        <small class="text-muted">{{ Carbon::parse($feedback->updated_at)->format('g:i A') }}</small>
                    </div>
                    @endif
                </div>

                <!-- Additional Feedback Fields (if they exist) -->
                @if (method_exists($feedback, 'getIssueDetails') && $feedback->getIssueDetails())
                    <div class="mt-3">
                        <h6 class="fw-bold text-muted">{{ __('Issue Details') }}</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $feedback->getIssueDetails() }}
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach



@endsection

@section('scripts')
<script>
let videoElement = null;
let isMuted = true; // Start muted by default

document.addEventListener('DOMContentLoaded', function() {
    videoElement = document.getElementById('videoPlayer');
    
    if (videoElement) {
        // Ensure video starts muted
        videoElement.muted = true;
        
        // Add event listeners
        videoElement.addEventListener('loadstart', () => {
            console.log('Stream loading started');
        });
        
        videoElement.addEventListener('canplay', () => {
            console.log('Stream ready to play');
        });
        
        videoElement.addEventListener('error', (e) => {
            console.error('Stream error:', e);
            console.log('Trying to load alternative stream format...');
        });
        
        videoElement.addEventListener('loadedmetadata', () => {
            console.log('Stream metadata loaded');
        });
        
        // Update mute icon on load
        updateMuteIcon();
    }
});

function toggleFullscreen() {
    const videoContainer = document.getElementById('videoPlayer').parentElement;
    
    if (!document.fullscreenElement) {
        videoContainer.requestFullscreen().catch(err => {
            console.error('Error attempting to enable fullscreen:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

function toggleMute() {
    if (!videoElement) return;
    
    isMuted = !isMuted;
    videoElement.muted = isMuted;
    updateMuteIcon();
}

function updateMuteIcon() {
    const muteIcon = document.getElementById('muteIcon');
    if (muteIcon) {
        muteIcon.className = isMuted ? 'fa fa-volume-off' : 'fa fa-volume-up';
    }
}

// Auto-refresh stream status every 30 seconds
@if($stream->status === 'in_progress')
setInterval(() => {
    // Check if stream is still live
    fetch(`/admin/streams/{{ $stream->id }}/status`)
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'in_progress') {
                // Stream ended, refresh page
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error checking stream status:', error);
        });
}, 30000);
@endif
</script>
@endsection 