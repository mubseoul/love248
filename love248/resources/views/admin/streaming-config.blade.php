@extends('admin.base')

@section('section_title')
<strong>{{ __('message.live_stream_conf') }}</strong>
@endsection

@section('section_body')

@include('admin.configuration-navi')

<div class="alert alert-success d-flex align-items-center" role="alert">
    <div>
    {{ __('message.stream_setting_url_text') }} <strong
        class="font-black">{{__('message.stream_setting_url')}}</strong>
    <br /><br />
    {{__('message.stream_vps_text')}} <a href="https://www.linode.com/lp/free-credit-100/" target="_blank" class="underline">{{__('message.stream_vps_link_f')}}</a>, <a href="https://m.do.co/c/833110c66c2c" class="underline">{{__('message.stream_vps_link_s')}}</a> {{__('message.stream_vps_text_s')}}
</div>
</div>

<div class="card">
	<div class="card-body">
        <form method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('message.rtmp_url') }}</label>
                        <x-input type="text" name="RTMP_URL" value="{{ env('RTMP_URL') }}" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <p class="mt-3 text-gray-600">
                        <strong class="font-bold block text-lg">
                            {{__('message.nginx_url')}}
                        </strong>
                        {{ route('streaming.validateKey') }}
                    </p>
                </div>
                <div class="col-sm-12 iq-button">
                <button type="submit" class="btn btn-sm text-uppercase">{{ __('message.save_settings') }} </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <div class="bg-white rounded p-3 text-stone-600">

    <form method="POST">
        @csrf

        <div class="mt-5 flex md:flex-row flex-col md:space-x-5 space-y-10 md:space-y-0">
            <div class="md:w-2/3 w-full">
                <dl>
                    <dt class="font-semibold text-stone-600">{{ __('RTMP URL') }}</dt>
                    <dd>
                        <x-input type="text" name="RTMP_URL" value="{{ env('RTMP_URL') }}" class="md:w-2/3 w-full" />
                    </dd>

                    <br>
                </dl>
            </div>

            <div class="md:w-2/3 w-full">
                <p class="mt-3 text-gray-600">
                    <strong class="font-bold block text-lg">
                        Your Nginx Publish URL:
                    </strong>
                    {{ route('streaming.validateKey') }}
                </p>
            </div>


        </div>

        <div class="flex w-full my-3">
            <x-button>{{ __('Save Settings') }}</x-button>
        </div>
    </form>


</div> -->

<!-- ./row -->
@endsection