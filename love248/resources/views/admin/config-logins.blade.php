@extends('admin.base')

@section('section_title')
    <strong class="text-capitalize">{{ __('message.admin_conf')  }}</strong>
@endsection

@section('section_body')
<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action={{ route('admin.save-logins') }}>
            {{ csrf_field() }}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('message.admin_email') }}</label>
                        <x-input type="text" name="admin_user" value="{{ auth()->user()->email }}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.admin_pass') }}</label>
                        <x-input type="password" name="admin_pass" value="" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.admin_confirm_pass') }}</label>
                        <x-input type="password" name="admin_pass_confirmation" value="" class="form-control" />
                    </div>
                    <div class="form-group iq-button">
                        <button class="mt-4 btn btn-sm text-uppercase">{{ __('message.admin_confirm_pass')  }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- <div class="bg-white p-3 rounded">
        <form method="POST" action="/admin/save-logins">
            {{ csrf_field() }}
            <dl>
                <dt class="text-stone-600 font-semibold">{{ __('Admin Login Email')  }}</dt>
                <dd>
                    <x-input type="text" name="admin_user" value="{{ auth()->user()->email }}" class="md:w-1/2 w-full" />
                </dd>
            </dl>
            <dl class="mt-3">
                <dt class="text-stone-600 font-semibold">{{ __('Admin New Password')  }}</dt>
                <dd>
                    <x-input type="password" name="admin_pass" value="" class="md:w-1/2 w-full" />
                </dd>
            </dl>
            <dl class="mt-3">
                <dt class="text-stone-600 font-semibold">{{ __('Admin Confirm New Password')  }}</dt>
                <dd>
                    <x-input type="password" name="admin_pass_confirmation" value="" class="md:w-1/2 w-full" />
                </dd>
            </dl>

            <x-button class="mt-4">{{ __('Save')  }}</x-button>
    </div> -->
@endsection
