@extends('admin.base')

@section('section_title')
<strong class="text-capitalize">{{ __($active==='videos'?__('message.video_sales'):__('message.users_management'), ['type' => ucfirst($active)]) }}</strong>
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
                <div class="table-view table-responsive table-space">
                    <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('message.username') }}</x-th>
                                <x-th>{{ __('message.video_sales') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stremerData as $userData)
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID') }}</x-slot>
                                    {{ $userData['user']->id }}
                                </x-td>
                        
                                <x-td>
                                    <x-slot name="field">{{ __('Username') }}</x-slot>
                                    {{ $userData['user']->username }}
                                </x-td>
                        
                                <x-td>
                                    <x-slot name="field">{{ __('Videos Sales') }}</x-slot>
                                    <span class="mt-2 badge border border-primary text-primary mt-2">
                                        {{ $userData['totalEarnings'] }}
                                    </span>
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
<!-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
{{-- attention, dynamic because only needed on this page to save resources --}}
<script>
    $(document).ready(function() {
        $('.dataTable').dataTable({ordering:false});
    });
</script> -->
@endpush