@extends('admin.base')

@section('section_title')
<strong>{{ __('message.live_chat_config') }}</strong>
@endsection

@section('section_body')

@include('admin.configuration-navi')

<div class="alert alert-success d-flex align-items-center flex-wrap" role="alert">
    {{ __('message.pusher_text_f') }} 
    <a href="https://dashboard.pusher.com/apps" target="blank" class="text-indigo-600 hover:underline font-semibold"> &nbsp;https://dashboard.pusher.com/apps &nbsp;</a>
    {{ __('message.pusher_text_s') }}
</div>

<div class="card">
	<div class="card-body">
    <form method="POST">
        @csrf
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">{{ __('message.pusher_app_id') }}</label>
                    <x-input type="text" name="PUSHER_APP_ID" value="{{ opt('PUSHER_APP_ID') }}" class="form-control" />
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('message.pusher_app_key') }}</label>
                    <x-input type="text" name="PUSHER_APP_KEY" value="{{ opt('PUSHER_APP_KEY') }}" class="form-control" />
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('message.pusher_secret_key') }}</label>
                    <x-input type="text" name="PUSHER_APP_SECRET" value="{{ opt('PUSHER_APP_SECRET') }}" class="form-control" />
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('message.pusher_reason') }}</label>
                    <x-input type="text" name="PUSHER_APP_CLUSTER" value="{{ opt('PUSHER_APP_CLUSTER') }}" class="form-control" />
                </div>
                <div class="form-group iq-button">
                <button type="submit" class="btn btn-sm text-uppercase">{{ __('message.save_settings') }} </button>
                </div>
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
                    <dt class="font-semibold text-stone-600">{{ __('Pusher APP_ID') }}</dt>
                    <dd>
                        <x-input type="text" name="PUSHER_APP_ID" value="{{ env('PUSHER_APP_ID') }}"
                            class="md:w-2/3 w-full" />
                    </dd>
                    <br>
                    <dt class="font-semibold text-stone-600">{{ __('Pusher APP_KEY') }}</dt>
                    <dd>
                        <x-input type="text" name="PUSHER_APP_KEY" value="{{ env('PUSHER_APP_KEY') }}"
                            class="md:w-2/3 w-full" />
                    </dd>
                    <br>
                    <dt class="font-semibold text-stone-600">{{ __('Pusher APP_SECRET') }}</dt>
                    <dd>
                        <x-input type="text" name="PUSHER_APP_SECRET" value="{{ env('PUSHER_APP_SECRET') }}"
                            class="md:w-2/3 w-full" />
                    </dd>

                    <br>
                    <dt class="font-semibold text-stone-600">{{ __('Pusher Region (Cluster)') }}</dt>
                    <dd>
                        <x-input type="text" name="PUSHER_APP_CLUSTER" value="{{ env('PUSHER_APP_CLUSTER') }}"
                            class="md:w-2/3 w-full" />
                    </dd>
                    <br>
                </dl>
            </div>


        </div>

        <div class="flex w-full my-3">
            <x-button>{{ __('Save Settings') }}</x-button>
        </div>
    </form>


</div> -->

<!-- ./row -->
@endsection