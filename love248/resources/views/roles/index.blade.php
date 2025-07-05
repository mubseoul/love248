@extends('admin.base')

@section('section_title')
{{ __('message.role_management') }}
@endsection

@section('section_body')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                    @can('role-create')
                        <a class="btn btn-primary btn-sm" href="{{ route('roles.create') }}"> {{__('message.create_new_role')}}</a>
                    @endcan
                </div>
                
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p class="m-0">{{ $message }}</p>
                    </div>
                @endif
                <div class="table-view table-responsive table-space">
                    <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID') }}</x-th>
                                <x-th>{{ __('message.name') }}</x-th>
                                <x-th>{{ __('message.actions') }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a class="btn btn-info btn-sm me-2" href="{{ route('roles.show',$role->id) }}"><i class="fa-solid fa-eye"></i></a>
                                            @can('role-edit')
                                                    <a class="btn btn-primary btn-sm me-2" href="{{ route('roles.edit',$role->id) }}"><i class="fa-solid fa-pen"></i></a>
                                            @endcan
                                            @can('role-delete')
                                                <form method="" action={{route("roles.destroy", [$role->id])}}>
                                                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                    {!! $roles->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection