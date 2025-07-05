@extends('admin.base')

@section('section_title')
{{ __('message.edit_role') }}
@endsection

@section('section_body')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                </div>
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
            </div>
            <div class="card-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul class="m-0">
                            @foreach ($errors->all() as $error)
                                <li class="m-0">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('roles.update', ['id'=>$role->id]) }}">
                    {{ csrf_field() }}
                    <div class="form-group mb-3">
                        <label>Name</label>
                        <input type="text" name="name" value="{{$role->name}}"
                        placeholder="Name" class="form-control"/>
                    </div>
                    <div class="row">
                        @foreach($permission as $value)
                        
                        <label class="col-md-4 col-sm-6 mb-3 text-capitalize">
                            <input type="checkbox" name="permission[]" value="{{$value->name}}" @if(in_array($value->id, $rolePermissions)) checked @endif  class="me-2"/>{{ str_replace('-', ' ',$value->name) }}
                        </label>
                        @endforeach
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 iq-button">
                        <button type="submit" class="btn btn-primary btn-sm">{{__('message.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
            
