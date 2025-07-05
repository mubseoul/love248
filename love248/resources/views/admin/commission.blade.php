@extends('admin.base')

@section('section_title')
<strong class="text-capitalize">{{ __('Commission List') }}</strong>
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
                <div class="table-view table-responsive table-space">
                    <table class="text-stone-600 table border-collapse w-full bg-white dataTable">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('message.username') }}</x-th>
                                <x-th>{{ __('message.commission_type') }}</x-th>
                                <x-th>{{ __('Amount') }}</x-th>
                                <x-th>{{ __('Currency') }}</x-th>
                                <x-th>{{ __('Commission Rate') }}</x-th>
                                <x-th>{{ __('Date') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commData as $userData)
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID') }}</x-slot>
                                    {{ $userData->id ?? '' }}
                                </x-td>
                        
                                @php
                                    $users = App\Models\User::where('id',$userData->streamer_id)->first();
                                @endphp
                                <x-td>
                                    <x-slot name="field">{{ __('Username') }}</x-slot>
                                    {{ $users->username ?? '' }}
                                </x-td>
                        
                                <x-td>
                                    <x-slot name="field">{{ __('Commission Type') }}</x-slot>
                                    {{ $userData->type ?? '' }}
                                </x-td>
                                
                                <x-td>
                                    <x-slot name="field">{{ __('Amount') }}</x-slot>
                                    @if ($userData->type === 'Private Streaming')
                                        {{ number_format($userData->tokens, 0) }} {{ __('Tokens') }}
                                    @else
                                        {{ opt('payment-settings.currency_symbol', 'R$') }}{{ number_format($userData->tokens, 2) }}
                                    @endif
                                </x-td>
                                
                                <x-td>
                                    <x-slot name="field">{{ __('Currency') }}</x-slot>
                                    @if ($userData->type === 'Private Streaming')
                                        {{ __('Tokens') }}
                                    @else
                                        {{ opt('payment-settings.currency_code', 'BRL') }}
                                    @endif
                                </x-td>
                                
                                <x-td>
                                    <x-slot name="field">{{ __('Commission Rate') }}</x-slot>
                                    @if ($userData->type === 'Buy Videos')
                                        {{ opt('admin_commission_videos', 0) }}%
                                    @elseif ($userData->type === 'Private Streaming')
                                        25% {{ __('(Admin)') }}
                                    @elseif ($userData->type === 'Buy Gallery')
                                        {{ opt('admin_commission_photos', 0) }}%
                                    @endif
                                </x-td>
                                
                                <x-td>
                                    <x-slot name="field">{{ __('Date') }}</x-slot>
                                    {{ $userData->created_at ? $userData->created_at->format('M d, Y H:i') : '' }}
                                </x-td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
<script>
    $(document).ready(function() {
        $('.dataTable').dataTable({ordering:false});
    });
</script>
@endpush