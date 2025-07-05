@extends('admin.base')

@section('section_title')
    <strong>{{ __('message.subscriptionplan') }}</strong>
@endsection

@section('section_body')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                    <div class="card-title d-flex w-100">
                        <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                        @can('subscription-plan-create')
                            <a href="/admin/create-subscription-plan" class="text-white btn btn-sm btn-primary">
                                {{ __('message.add_subscription_plan') }}
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @if (count($packs))
                        <div class="table-view table-responsive table-space">
                            <table id="commentTable" class="table border-collapse w-full bg-white text-stone-600"
                                data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <x-th>{{ __('ID') }}</x-th>
                                        <x-th>{{ __('Subscription Plan') }}</x-th>
                                        <x-th>{{ __('Level') }}</x-th>
                                        <x-th>{{ __('Price') }}</x-th>
                                        <x-th>{{ __('Days') }}</x-th>
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
                                                <x-slot name="field">{{ __('Subscription Plan') }}</x-slot>
                                                {{ $s->subscription_name }}
                                            </x-td>

                                            <x-td>
                                                <x-slot name="field">{{ __('Level') }}</x-slot>
                                                {{ $s->level_name }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Price') }}</x-slot>
                                                {{ opt('payment-settings.currency_symbol') . $s->subscription_price }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Days') }}</x-slot>
                                                {{ $s->days }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Actions') }}</x-slot>
                                                <div class="d-flex align-items-center list-user-action">
                                                    @can('subscription-plan-edit')
                                                        <a class="btn btn-sm btn-icon btn-success rounded"
                                                            href="/admin/edit-subscription-plan/{{ $s->id }}"><i
                                                                class="fa-solid fa-pencil"></i></a>
                                                    @endcan
                                                    @can('subscription-plan-delete')
                                                        <a href="/admin/subscription-plans?remove={{ $s->id }}"
                                                            onclick="return confirm('{{ __('Are you sure you want to remove this plan?') }}')"
                                                            class="btn btn-sm btn-icon btn-primary bg-primary border-0 delete-btn rounded"><i
                                                                class="fa-solid fa-trash"></i></a>
                                                    @endcan
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
                            <div>{{ __('No Subscription Plan created.') }}</div>
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
                    $('.dataTable').dataTable({
                        ordering: false
                    });
                </script> -->
@endpush
