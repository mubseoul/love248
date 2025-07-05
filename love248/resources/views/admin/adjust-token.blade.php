@extends('admin.base')

@section('section_title')
{{ __('Adjust Token Balance of :handle', ['handle' => $user->username]) }}
<br />
@if($user->is_streamer === 'yes')
<a href="/admin/streamers" class="text-indigo-700 hover:underline font-semibold">&raquo; {{ __('Back to Streamers')
    }}</a>
@else
<a href="/admin/users" class="text-indigo-700 hover:underline font-semibold">&raquo; {{ __('Back to Users') }}</a>
@endif
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
                    <form method="POST" action="/admin/save-token-balance/{{ $user->id }}" class="max-w-md">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">{{ __("New :username token balance", ['username' => $user->username]) }}</label>
                            <input id="balance" class="form-control" type="numeric" value="{{ $user->tokens }}" name="balance" required />
                        </div>
                        <div class="iq-button"><button class="btn btn-primary btn-sm text-uppercase">{{ __('Adjust Balance') }}</button></div>
                    </form>
            </div>
        </div>
    </div>
</div>

@endsection