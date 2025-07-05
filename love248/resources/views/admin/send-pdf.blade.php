@extends('admin.base')

@section('section_title')
<strong class="text-capitalize">{{ __('message.send_transaction_report') }}</strong>
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3">
        <div class="card-title">
            <h4>@yield('section_title', '')</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.user-pdf') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="col-sm-8 col-sm-6 mx-auto">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="form-group">
                    <label class="form-label">{{ __("message.user_email") }}</label>
                    <input type="hidden" class="form-control" name="id" value="{{$user->id}}"></input>
                    <input class="form-control" name="email" readonly value="{{$user->email}}" style="background:transparent !important"></input>
                    @if ($errors->has('email'))
                        <span class="text-red-600">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __("message.select_transaction_pdf") }}</label>
                    <input class="form-control" type="file" name="pdf"></input>
                    @if ($errors->has('pdf'))
                        <span class="text-red-600">{{ $errors->first('pdf') }}</span>
                    @endif
                </div>
                <div class="form-group iq-button">
                    <button class="btn btn-sm text-uppercase">{{ __('message.save') }}</button>
                </div>
            </div>
        </form>
    </div>
<div>
@endsection
