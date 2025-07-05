@extends('admin.base')

@section('section_title')
<strong>{{ __("Payout Requests") }}</strong>
@endsection

@section('section_body')

<div class="alert alert-info d-flex align-items-center" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
        <use xlink:href="#info-fill" />
    </svg>
    <div> {{ __("When you mark a payment request as paid, you have to actually pay the user first manually to their bank
    account or
    paypal. This does NOT happen automatically.") }}</div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                </div>
            </div>
            <div class="card-body">
            @if($payoutRequests->count())
                <div class="table-view table-responsive table-space">
                    <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('Streamer') }}</x-th>
                                <x-th>{{ __('Tokens') }}</x-th>
                                <x-th>{{ __('Money') }}</x-th>
                                <x-th>{{ __('Payout Details') }}</x-th>
                                <x-th>{{ __('Date') }}</x-th>
                                <x-th>{{ __('--') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payoutRequests as $p)
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID') }}</x-slot>
                                    {{ $p->id }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Streamer') }}</x-slot>
                                    <div class="flex items-center w-full">
                                        <div>
                                            <img src="{{ $p->user->profile_picture }}" alt="" class="w-16 h-16 rounded-full" />
                                        </div>
                                        <div class="ml-2 text-left">
                                            {{ $p->user->name }}<br />
                                            {{ '@' . $p->user->username }}
                                        </div>
                                    </div>
                    
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Tokens') }}</x-slot>
                                    <span class="px-2 py-0.5 bg-cyan-600 text-white rounded-lg">
                                        {{ number_format($p->tokens, 0) }}
                                    </span>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Money') }}</x-slot>
                                    <span class="px-2 py-0.5 bg-cyan-600 text-white rounded-lg">
                                        {{ opt('payment-settings.currency_symbol') . $p->amount }}
                                    </span>
                                </x-td>
                    
                                <x-td>
                                    <x-slot name="field">{{ __('Payout Details') }}</x-slot>
                                    @if($gm = $gateway_meta->where('user_id', $p->user_id)->first())
                                    {{ $gm->meta_value }}
                                    @else
                                    {{ __("User did not set a gateway") }}
                                    @endif
                                    <br />
                                    @if($gm = $payout_meta->where('user_id', $p->user_id)->first())
                                    {{ $gm->meta_value }}
                                    @else
                                    {{ __("User did not set payout details") }}
                                    @endif
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Date') }}</x-slot>
                                    {{ $p->created_at->format('jS F Y') }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('--') }}</x-slot>
                                    <a href="/admin/payout/mark-as-paid/{{ $p->id }}" class="text-teal-600 hover:underline"
                                        onclick="return confirm('{{ __(" Are you sure you want to set this payment request as PAID?") }}')">
                                        {{ __('Mark as Paid') }}
                                    </a>
                                </x-td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                            <use xlink:href="#exclamation-triangle-fill01" />
                    </svg>
                    <div>
                    {{ __("No one requested a payout yet.") }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_bottom')
@if (count($errors) > 0)
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
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
{{-- attention, dynamic because only needed on this page to save resources --}}
<script>
    $(document).ready(function() {
        $('.dataTable').dataTable({ordering:false});
    });
</script>
@endpush