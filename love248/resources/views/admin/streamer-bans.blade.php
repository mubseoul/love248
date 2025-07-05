@extends('admin.base')

@section('section_title')
<strong>{{ __('Streamer Bans')  }}</strong>
@endsection

@section('section_body')

<div class="alert alert-info d-flex align-items-center" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
        <use xlink:href="#info-fill" />
    </svg>
    <div>
        {{ __('Here you will find an overview of the bans given to users by streamers.')  }}
    </div>
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
                <div class="table-view table-responsive table-space">
                    @if (count($streamerBans))
                    <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID')  }}</x-th>
                                <x-th>{{ __('Banned User')  }}</x-th>
                                <x-th>{{ __('Streamer Live')  }}</x-th>
                                <x-th>{{ __('IP')  }}</x-th>
                                <x-th>{{ __('Date')  }}</x-th>
                                <x-th>{{ __('Delete')  }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($streamerBans as $t)
                            @if (is_null($t->user) or is_null($t->streamer))
                            @else
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID')  }}</x-slot>
                                    {{ $t->id }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('User')  }}</x-slot>
                                    {{ $t->user->name }}
                                    <br>
                                    {{ '@' . $t->user->username }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Streamer')  }}</x-slot>
                                    {{ $t->streamer->name }}
                                    <br>
                                    <a href="/channel/{{ $t->streamer->username }}" target="_blank">
                                        {{ '@'.$t->streamer->username }}
                                    </a>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Tip Amount')  }}</x-slot>
                                    {{$t->ip}}
                    
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Date')  }}</x-slot>
                                    {{ $t->created_at->format('jS F Y') }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Delete')  }}</x-slot>
                                    <a href="/admin/streamer-bans?delete={{ $t->id }}" onclick="return confirm('{{ __('Are you sure you want to lift this ban?')  }}')" class="btn btn-sm btn-icon btn-danger bg-danger border-0 delete-btn rounded text-red-400">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </x-td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                            <use xlink:href="#info-fill" />
                        </svg>
                        <div>{{ __('No bans given by streamers to users.')  }}</div>
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
{{-- attention, dynamic because only needed on this page to save resources  --}}
<script>
    $('.dataTable').dataTable({
        ordering: false
    });
</script>
@endpush
