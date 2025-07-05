@extends('admin.base')

@section('section_title')
{{ __("Update Token Package")}}
<br />
<a href="/admin/token-packs" class="text-indigo-700 font-semibold hover:underline">&raquo; {{ __('Back to Packs') }}</a>
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
        <div class="card-title d-flex w-100">
            <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/update-token-pack/{{ $tokenPack->id }}">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __("Package Name")}}</label>
                        <input id="name" class="form-control" type="text" value="{{ $tokenPack->name }}" name="name" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("Tokens")}}</label>
                        <input id="tokens" class="form-control" type="number" value="{{ $tokenPack->tokens }}" name="tokens" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("Price") . " - " . opt('payment-settings.currency_symbol')}}</label>
                        <input id="price" class="form-control" type="number" value="{{ $tokenPack->price }}" name="price"
                        required />
                    </div>
                    <button class="btn btn-primary">{{ __('Update Pack') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection