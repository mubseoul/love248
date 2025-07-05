@extends('admin.base')

@section('section_title')
<strong>{{ __('message.streamer_categories') }}</strong>
@endsection

@section('section_body')
@can('streamer-catgory-create')
    <form method="POST" action="{{ empty($catname) ? '/admin/add_category' : '/admin/update_category' }}">
    {{ csrf_field() }}
    <div class="card p-3">

        @if (!empty($catname))
        <input type="hidden" name="catID" value="{{ $catID }}" style="background: none; border: solid 1px #d1d0cf; color: #d1d0cf;">
        @endif

        <div class="form-group d-flex mb-0 iq-button">
            <input type="text" name="catname" value="{{ $catname }}" placeholder="{{ __('message.category') }} {{__('message.name')}}" class="form-control me-2" />
            <button class="btn btn-sm text-uppercase">{{ __('message.save') }}</button>
        </div>
    </div>
    </form>
@endcan

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
                    @if ($categories)
                    <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('message.category') }}</x-th>
                                <x-th>{{ __('message.users') }}</x-th>
                                <x-th>{{ __('message.actions') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $c)
                            <tr>
                                <x-td>
                                    <x-slot name="field">{{ __('ID') }}</x-slot>
                                    {{ $c->id }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Category') }}</x-slot>
                                    {{ $c->category }}
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Users') }}</x-slot>
                                    <span class="mt-2 badge border border-primary text-primary mt-2">
                                        {{ $c->users_count }}
                                    </span>
                                </x-td>
                                <x-td>
                                    <x-slot name="field">{{ __('Actions') }}</x-slot>
                                    <div class="d-flex align-items-center list-user-action">
                                        @can('streamer-catgory-edit')
                                            <a class="btn btn-sm btn-icon btn-success rounded" href="/admin/categories?update={{ $c->id }}">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('streamer-catgory-delete')
                                            <a href="/admin/categories?remove={{ $c->id }}"
                                            onclick="return confirm('{{ __('Are you sure you want to remove this category from database?')  }}');"
                                            class="btn btn-sm btn-icon btn-primary bg-primary border-0 delete-btn rounded">
                                            <i class="fa-solid fa-trash"></i>
                                        @endcan
                                        </a>
                                    </div>
                                </x-td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                            <use xlink:href="#info-fill" />
                        </svg>
                        <div>{{ __('No categories in database.') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection