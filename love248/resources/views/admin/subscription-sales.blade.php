@extends('admin.base')

@section('section_title')
<strong>{{ __('message.subscription_sales') }}</strong>
@endsection

@section('section_body')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0 text-capitalize">@yield('section_title', '')</h4>
                </div>
            </div>
            <div class="card-body">
                @if ($sales->isNotEmpty())
                <div class="table-view table-responsive table-space">
                    <!-- dataTable -->
                    <table id="commentTable" class="table border-collapse w-full bg-white text-stone-600 " data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('User') }}</x-th>
                                <x-th>{{ __('Subscription Plan') }}</x-th>
                                <x-th>{{ __('Price') }}</x-th>
                                <x-th>{{ __('Status') }}</x-th>
                                <x-th>{{ __('Gateway') }}</x-th>
                                <x-th>{{ __('Start Date') }}</x-th>
                                <x-th>{{ __('Expire Date') }}</x-th>
                                <x-th>{{ __('Actions') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID') }}</x-slot>
                                    {{ $sale->id }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('User') }}</x-slot>
                                    <div class="d-flex align-items-center">
                                        @if($sale->user->profile_picture)
                                            <img src="{{ asset($sale->user->profile_picture) }}" class="rounded-circle me-2" width="30" height="30" alt="Profile">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                <span class="text-white small">{{ substr($sale->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                                                                 <div>
                                             <div class="fw-bold">{{ $sale->user->name }}</div>
                                             <small class="text-muted">{{ '@' . $sale->user->username }}</small>
                                         </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Subscription Plan') }}</x-slot>
                                    <div class="fw-bold text-primary">{{ $sale->subscription_plan }}</div>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Price') }}</x-slot>
                                    <span class="badge border border-success text-success">
                                        {{ opt('payment-settings.currency_symbol') . number_format($sale->price, 2) }}
                                    </span>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Status') }}</x-slot>
                                    @php
                                        $isActive = $sale->status === 'active' && $sale->expire_date >= now();
                                        $isExpired = $sale->expire_date < now();
                                        $isCancelled = $sale->status === 'cancelled';
                                    @endphp
                                    <span class="badge bg-{{ $isActive ? 'success' : ($isCancelled ? 'warning' : 'danger') }}">
                                        {{ $isActive ? __('Active') : ($isCancelled ? __('Cancelled') : __('Expired')) }}
                                    </span>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Gateway') }}</x-slot>
                                    {{ $sale->gateway ?? 'N/A' }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Start Date') }}</x-slot>
                                    {{ $sale->created_at->format('M d, Y') }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Expire Date') }}</x-slot>
                                    <span class="{{ $sale->expire_date < now() ? 'text-danger' : 'text-success' }}">
                                        {{ $sale->expire_date->format('M d, Y') }}
                                    </span>
                                    @if($sale->expire_date >= now())
                                        <small class="d-block text-muted">
                                            ({{ now()->diffInDays($sale->expire_date) }} days left)
                                        </small>
                                    @endif
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Actions') }}</x-slot>
                                                                         <div class="d-flex align-items-center list-user-action">
                                         <a href="mailto:{{ $sale->user->email }}" class="btn btn-sm btn-icon btn-info rounded me-1" data-bs-toggle="tooltip" title="{{ __('Contact User') }}">
                                             <i class="fa-solid fa-envelope"></i>
                                         </a>
                                         @if($sale->status === 'active' && $sale->expire_date >= now())
                                             @can('subscription-plan-sell-delete')
                                                 <a onclick="return confirm('{{ __('Are you sure you want to cancel this subscription?') }}')" 
                                                    class="btn btn-sm btn-icon btn-warning rounded" 
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ __('Cancel Subscription') }}" 
                                                    href="/admin/subscription-sells?remove={{ $sale->id }}">
                                                     <i class="fa-solid fa-ban"></i>
                                                 </a>
                                             @endcan
                                         @endif
                                     </div>
                                </x-td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Simple Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-success">{{ $sales->filter(function($sale) { return $sale->status === 'active' && $sale->expire_date >= now(); })->count() }}</h5>
                                <p class="card-text">{{ __('Active Subscriptions') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-warning">{{ $sales->where('status', 'cancelled')->count() }}</h5>
                                <p class="card-text">{{ __('Cancelled') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-danger">{{ $sales->filter(function($sale) { return $sale->expire_date < now(); })->count() }}</h5>
                                <p class="card-text">{{ __('Expired') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ opt('payment-settings.currency_symbol') }}{{ number_format($sales->sum('price'), 2) }}</h5>
                                <p class="card-text">{{ __('Total Revenue') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <div>{{ __('No Subscription Plans sold yet') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra_bottom')
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection

@push('adminExtraJS')
<!-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
<script>
    $('.dataTable').dataTable({ordering:false});
</script> -->
@endpush