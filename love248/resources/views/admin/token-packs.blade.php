@extends('admin.base')

@section('section_title')
<strong>{{ __('Token Packages') }}</strong>

<br />
<a href="/admin/create-token-pack" class="text-indigo-700 font-semibold hover:underline">
    {{ __('+Create Token Pack') }}
</a>

@endsection

@section('section_body')


<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                </div>
            </div>
            <div class="card-body">
                @if (count($packs))
                    <div class="table-view table-responsive table-space">
                        <table id="commentTable" class="table border-collapse w-full bg-white text-stone-600" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <x-th>{{ __('ID') }}</x-th>
                                    <x-th>{{ __('Name') }}</x-th>
                                    <x-th>{{ __('Tokens') }}</x-th>
                                    <x-th>{{ __('Price') }}</x-th>
                                    <x-th>{{ __('Actions') }}</x-th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packs as $s)
                                <tr>
                                    <x-td>
                                        <x-slot name="field">{{ __('ID') }}</x-slot>
                                        {{ $s->id }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Name') }}</x-slot>
                                        {{ $s->name }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Tokens') }}</x-slot>
                                        {{ number_format($s->tokens, 0) }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Price') }}</x-slot>
                                        {{ opt('payment-settings.currency_symbol') . $s->price }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Actions') }}</x-slot>
                                        <div class="d-flex align-items-center list-user-action justify-content-center">
                                            <a class="btn btn-sm btn-icon btn-success rounded" href="/admin/edit-token-pack/{{ $s->id }}"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="/admin/token-packs?remove={{ $s->id }}" class="btn btn-sm btn-icon btn-danger bg-danger border-0 delete-btn rounded" onclick="return confirm('{{ __('Are you sure you want to remove this pack?')  }}')"><i class="fa-solid fa-trash"></i></a>
                                        </div>
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
                    <div>{{ __('No token packs created.') }}</div>
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


{{-- attention, dynamic because only needed on this page to save resources --}}
@push('adminExtraJS')
<!-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
<script>
    $('.dataTable').dataTable({ordering:false});
</script> -->
@endpush