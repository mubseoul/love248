@php
    use Carbon\Carbon;
@endphp

@extends('admin.base')

@section('section_title')
<strong>{{ __('Transactions for') }} {{ $user->name ?? 'User' }}</strong>
@endsection


@section('section_body')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100 iq-button">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                    <a href="/admin/user-transactions-pdf/{{ request()->route('id') }}" class="btn btn-primary btn-sm text-capitalize">{{__('message.export_csv')}}</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Transaction Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="bg-primary text-white p-3 rounded">
                            <h5 class="mb-0">{{ $transactions->total() }}</h5>
                            <small>{{ __('Total Transactions') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-secondary text-white p-3 rounded">
                            <h5 class="mb-0">{{ $payments->count() }}</h5>
                            <small>{{ __('Legacy Payments') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-success text-white p-3 rounded">
                            <h5 class="mb-0">{{ $streamerTransactions->count() }}</h5>
                            <small>{{ __('Content Sales') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-info text-white p-3 rounded">
                            <h5 class="mb-0">${{ number_format($transactions->sum('amount'), 2) }}</h5>
                            <small>{{ __('Total Amount') }}</small>
                        </div>
                    </div>
                </div>

                @if($transactions->count() > 0)
                <!-- Modern Transactions -->
                <h5 class="mb-3">{{ __('Transactions') }}</h5>
                <div class="table-view table-responsive table-space mb-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Payment Method') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->description ?: 'N/A' }}</td>
                                <td>
                                    <strong>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</strong>
                                </td>
                                <td>{{ $transaction->payment_method ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.transaction.pdf', $transaction->id) }}" 
                                       class="btn btn-sm btn-info" title="Download PDF">
                                        <i class="fa fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $transactions->links() }}
                </div>
                @endif
                @endif

                @if($streamerTransactions->count() > 0)
                <!-- Content Sales for Streamers -->
                <h5 class="mb-3">{{ __('Content Sales (As Streamer)') }}</h5>
                <div class="table-view table-responsive table-space mb-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($streamerTransactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->description ?: 'N/A' }}</td>
                                <td>
                                    <strong>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($payments->count() > 0)
                <!-- Legacy Payments -->
                <h5 class="mb-3">{{ __('Legacy Payments') }}</h5>
                <div class="table-view table-responsive table-space">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Sr.No') }}</th>
                                <th>{{ __('Payment Gateway') }}</th>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Purchase Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->type }}</td>
                                <td>{{ $p->item_type }}</td>
                                <td>{{ $p->status }}</td>
                                <td>{{ Carbon::parse($p->created_at)->format('F j, Y') }}</td>
                                <td>
                                    @if ($p->data!==null)
                                       {{ json_decode($p->data)->additional_info->items[0]->unit_price ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-success" 
                                       href="/admin/user-invoice-pdf/{{ $p->id }}" 
                                       title="Download Invoice PDF">
                                        <i class="fa fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($transactions->count() == 0 && $payments->count() == 0 && $streamerTransactions->count() == 0)
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('No transactions found for this user') }}</h5>
                    <p class="text-muted">{{ __('This user has not made any transactions yet.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection