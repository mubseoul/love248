@extends('admin.base')

@section('section_title')
<strong>{{ __('Users Subscribed to Streamers') }}</strong>
@endsection

@section('section_body')

<div class="alert alert-info d-flex align-items-center" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
        <use xlink:href="#info-fill" />
    </svg>
    <div> {{ __('Here you will find an overview of the paid membership tiers that Streamers sold to their Fans.') }} </div>
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
                @if (!count($subscriptions))
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                        <use xlink:href="#info-fill" />
                    </svg>
                    <div>{{ __('No users have subscribed to any of the creators yet. ') }}</div>
                </div>
                @else
                <div class="table-view table-responsive table-space">
                    <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('Subscriber') }}</x-th>
                                <x-th>{{ __('Streamer') }}</x-th>
                                <x-th>{{ __('Started') }}</x-th>
                                <x-th>{{ __('Expires') }}</x-th>
                                <x-th>{{ __('Price') }}</x-th>
                                <x-th>{{ __('Actions') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subscriptions as $t)
                            @if (is_null($t->streamer) or is_null($t->subscriber))
                            @else
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID') }}</x-slot>
                                    {{ $t->id }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Subscriber') }}</x-slot>
                                    <div class="flex items-center w-full flex-col">
                                        <div class="w-16 h-16">
                                            <img src="{{ $t->subscriber->profile_picture }}" alt="" class="w-16 h-16 rounded-full" />
                                        </div>
                                        <div>
                                            {{ $t->subscriber->name }}
                                            <br>
                                            {{ '@' . $t->subscriber->username }}
                                        </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Streamer') }}</x-slot>
                    
                                    <div class="flex items-center w-full flex-col">
                                        <div class="w-16 h-16">
                                            <img src="{{ $t->streamer->profile_picture }}" alt="" class="w-16 h-16 rounded-full" />
                                        </div>
                                        <div>
                                            {{ $t->streamer->name }}
                                            <br>
                                            <a href="/channel/{{ $t->streamer->username }}">
                                                {{ $t->streamer->username }}
                                            </a>
                                        </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Date Subscribed') }}</x-slot>
                                    {{ $t->subscription_date->format('jS F Y') }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Expires') }}</x-slot>
                                    {{ $t->subscription_expires->format('jS F Y') }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Price') }}</x-slot>
                                    <span class="inline-flex px-2 py-1 bg-indigo-50 text-indigo-700 rounded-lg">
                                        {{ $t->subscription_tokens }} {{ __("tokens") }}
                                    </span>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Actions') }}</x-slot>
                                    <a href="/admin/edit-subscription/{{$t->id}}">
                                        <i class="fa-solid fa-pencil mr-2 text-teal-600"></i>
                                    </a>
                                    <a href="/admin/subscriptions?delete={{$t->id}}" class="text-red-400 hover:underline"
                                        onclick="return confirm('{{ __('Are you sure you want to delete this subscription?')  }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </x-td>
                            </tr>
                            @endif
                            @empty
                            {{ __('No users have subscribed to any creators') }}
                            @endforelse
                        </tbody>
                    </table>
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
<!-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
{{-- attention, dynamic because only needed on this page to save resources --}}
<script>
    $('.dataTable').dataTable({ordering:false});
</script> -->
@endpush