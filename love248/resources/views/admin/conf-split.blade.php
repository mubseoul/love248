@extends('admin.base')

@section('section_title')
    <strong>{{ __('message.general_config') }}</strong>
@endsection

@section('section_body')
    @include('admin.configuration-navi')
    <div class="card">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-warning">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('msg'))
                <div class="alert alert-success">
                    {{ session('msg') }}
                </div>
            @endif
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('message.streamer_comm_private_room') }}</label>
                            <input type="text" name="streamers_commission_private_room" value="{{ opt('streamers_commission_private_room') }}" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.streamer_comm_photos') }}</label>
                            <input type="text" name="streamers_commission_videos" value="{{ opt('streamers_commission_videos') }}" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.streamer_comm_videos') }}</label>
                            <input type="text" name="streamers_commission_photos" value="{{ opt('streamers_commission_photos') }}" class="form-control" />
                        </div>
                        
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('message.admin_comm_private_room') }}</label>
                            <input type="text" name="admin_commission_private_room" value="{{ opt('admin_commission_private_room') }}" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.admin_comm_photos') }}</label>
                            <input type="text" name="admin_commission_videos" value="{{ opt('admin_commission_videos') }}" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.admin_comm_videos') }}</label>
                            <input type="text" name="admin_commission_photos" value="{{ opt('admin_commission_photos') }}" class="form-control" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12">
                        <hr>
                        <h5 class="mb-3">{{ __('Private Room Settings') }}</h5>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Room Rental Fee (Tokens per Minute)') }}</label>
                            <input type="number" name="private_room_rental_tokens_per_minute" value="{{ opt('private_room_rental_tokens_per_minute', 5) }}" class="form-control" min="1" step="1" />
                            <small class="form-text text-muted">{{ __('This fee is charged per minute for private room rental and goes to the platform.') }}</small>
                        </div>
                    </div>
                    {{-- <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Admin Client ID') }}</label>
                            <input type="text" name="admin_client_id" value="{{ opt('admin_client_id') }}" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Admin Client Secret') }}</label>
                            <input type="text" name="admin_client_secret" value="{{ opt('admin_client_secret') }}" class="form-control" />
                        </div>
                    </div> --}}
                </div>

                <div class="form-group iq-button">
                    <button type="submit" class="btn btn-sm text-uppercase">{{ __('message.save_settings') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection