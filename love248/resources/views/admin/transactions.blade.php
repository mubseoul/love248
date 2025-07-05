@php
    use Carbon\Carbon;
@endphp

@extends('admin.base')

@section('section_title')
    <strong>{{ __('Transactions') }}</strong>
@endsection

@section('section_body')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                    <div class="card-title d-flex w-100 iq-button">
                        <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                        <a href="{{ route('admin.transactions.export') }}"
                            class="btn btn-primary btn-sm text-capitalize">{{ __('message.export_csv') }}</a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-view table-responsive table-space">
                        <table id="transactionTable" class="table custom-table movie_table"
                            style="width:100%" data-datatable-disable="true">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment Method') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('message.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>
                                            #{{ $transaction->id }}
                                        </td>
                                        <td>
                                            @if ($transaction->user)
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $transaction->user->profile_picture }}"
                                                        alt="{{ $transaction->user->name }}"
                                                        class="bg-soft-primary rounded img-fluid avatar-40 me-3" />
                                                    <div>
                                                        <div class="fw-bold">{{ $transaction->user->name }}</div>
                                                        <small
                                                            class="text-muted">{{ '@' . $transaction->user->username }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('Deleted User') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $typeClass = match ($transaction->transaction_type) {
                                                    'video_purchase', 'gallery_purchase', 'token_purchase' => 'border-primary text-primary',
                                                    'admin_commission' => 'border-success text-success',
                                                    'streamer_earning' => 'border-warning text-warning',
                                                    default => 'border-info text-info',
                                                };
                                            @endphp
                                            <span class="mt-2 badge border {{ $typeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $transaction->description ?: __('No description') }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $transaction->payment_method ?: __('N/A') }}
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
                                            <small
                                                class="text-muted">{{ Carbon::parse($transaction->created_at)->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-icon btn-info rounded" data-bs-toggle="modal"
                                                data-bs-target="#transactionDetailsModal{{ $transaction->id }}"
                                                data-bs-placement="top" title="{{ __('View Transaction Details') }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="/admin/transaction-pdf/{{ $transaction->id }}"
                                                class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="{{ __('Download PDF') }}">
                                                <i class="fa fa-file-pdf"></i>
                                            </a>
                                            @if ($transaction->payment_method && $transaction->payment_data)
                                                <button class="btn btn-sm btn-icon btn-warning rounded"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailsModal{{ $transaction->id }}"
                                                    data-bs-placement="top" title="{{ __('View Payment Data') }}">
                                                    <i class="fa fa-code"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
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

    <!-- Minimal Transaction Details Modals -->
    @foreach ($transactions as $transaction)
        <div class="modal fade" id="transactionDetailsModal{{ $transaction->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-receipt me-2"></i>
                            {{ __('Transaction') }} #{{ $transaction->id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- User Info -->
                        @if ($transaction->user)
                            <div class="mb-4 p-3 rounded mt-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $transaction->user->profile_picture }}"
                                        alt="{{ $transaction->user->name }}" class="rounded-circle me-3"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">{{ $transaction->user->name }}</h6>
                                        <small class="text-muted">{{ $transaction->user->email }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Transaction Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Type') }}</label>
                                    <div>
                                        <span class="badge bg-primary">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                        </span>
                                    </div>
                                </div>

                                @if ($transaction->description)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('Description') }}</label>
                                        <p class="mb-0">{{ $transaction->description }}</p>
                                    </div>
                                @endif

                                @if ($transaction->reference_id)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('Reference ID') }}</label>
                                        <p class="mb-0"><code>{{ $transaction->reference_id }}</code></p>
                                    </div>
                                @endif

                                @if ($transaction->payment_method)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('Payment Method') }}</label>
                                        <p class="mb-0">{{ $transaction->payment_method }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Amount') }}</label>
                                    <h4 class="text-primary mb-0">
                                        {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                                    </h4>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Status') }}</label>
                                    <div>
                                        @php
                                            $statusClass = match ($transaction->status) {
                                                'completed' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'failed' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Date') }}</label>
                                    <p class="mb-0">{{ Carbon::parse($transaction->created_at)->format('M d, Y g:i A') }}
                                    </p>
                                </div>

                                @if ($transaction->gateway_response)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('Gateway Response') }}</label>
                                        <p class="mb-0 text-muted small">{{ $transaction->gateway_response }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($transaction->metadata)
                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Metadata') }}</label>
                                <pre class="bg-light p-3 rounded small">{{ json_encode(json_decode($transaction->metadata), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <a href="/admin/transaction-pdf/{{ $transaction->id }}" class="btn btn-primary">
                            <i class="fa fa-download me-1"></i>{{ __('Download PDF') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Payment Data Modals -->
    @foreach ($transactions as $transaction)
        @if ($transaction->payment_data)
            <div class="modal fade" id="detailsModal{{ $transaction->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Payment Gateway Data') }} #{{ $transaction->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <pre class="bg-light p-3 rounded">{{ json_encode(json_decode($transaction->payment_data), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('adminExtraJS')
<script>
$(document).ready(function() {
    // Debug script to check table structure
    
    var table = $('#transactionTable');
    var headerCols = table.find('thead tr th').length;
    var firstRowCols = table.find('tbody tr:first td').length;
    var emptyRowCols = table.find('tbody tr:last td').length;
    
    // Check if any rows have different column counts
    table.find('tbody tr').each(function(index) {
        var cols = $(this).find('td').length;
        console.log('Row ' + index + ' has ' + cols + ' columns');
    });
    
});
</script>
@endpush
