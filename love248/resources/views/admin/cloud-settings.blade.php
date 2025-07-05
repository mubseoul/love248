@extends('admin.base')

@section('section_title')
<strong>{{ __('message.cloud_setting') }}</strong>
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
        <div class="card-title d-flex w-100">
            <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/save-cloud-settings">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('message.which_storage') }}</label>
                        <select name="FILESYSTEM_DISK" class="form-select">
                            <option value="public" @if (env('FILESYSTEM_DISK', 'public' )=='public' ) selected @endif>{{
                                __('Default: No cloud, just use my server') }}</option>
                            <option value="wasabi" @if (env('FILESYSTEM_DISK', 'public' )=='wasabi' ) selected @endif>{{
                                __('Wasabi S3 Storage (Better Cost than AWS)') }}</option>
                            <option value="s3" @if (env('FILESYSTEM_DISK', 'public' )=='s3' ) selected @endif>{{ __('Amazon
                                AWS S3 (Traditional Option)') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-capitalize">{{ __('message.wasabi_settings') }}</label>
                        <p><a href="https://wasabi.com/cloud-storage-pricing/" target="_blank">https://wasabi.com/cloud-storage-pricing/</a></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.was_key') }}</label>
                        <input type="text" name="WAS_ACCESS_KEY_ID" value="{{ env('WAS_ACCESS_KEY_ID') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.was_secret') }}</label>
                        <input type="text" name="WAS_SECRET_ACCESS_KEY" value="{{ env('WAS_SECRET_ACCESS_KEY') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.was_reason') }}</label>
                        <input type="text" name="WAS_DEFAULT_REGION" value="{{ env('WAS_DEFAULT_REGION') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.was_bucket') }}</label>
                        <input type="text" name="WAS_BUCKET" value="{{ env('WAS_BUCKET') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="iq-button">
                        <button class="btn btn-sm text-uppercase">{{ __('message.save_wassabi') }}</button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('Amazon AWS S3') }}</label>
                        <p><a href="https://aws.amazon.com/s3/" target="_blank">https://aws.amazon.com/s3/</a></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.amazon_access_key') }}</label>
                        <input type="text" name="AWS_ACCESS_KEY_ID" value="{{ env('AWS_ACCESS_KEY_ID') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.amazon_secret_key') }}</label>
                        <input type="text" name="AWS_SECRET_ACCESS_KEY" value="{{ env('AWS_SECRET_ACCESS_KEY') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.amazon_reason') }}</label>
                        <input type="text" name="AWS_DEFAULT_REGION" value="{{ env('AWS_DEFAULT_REGION') }}" class="form-control" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.aws_bucket') }}</label>
                        <x-input type="text" name="AWS_BUCKET" value="{{ env('AWS_BUCKET') }}" class="form-control" placeholder="" />
                    </div>
        
                    <div class="form-group iq-button">
                        <button class="btn btn-sm text-uppercase">{{ __('message.save_aws') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection