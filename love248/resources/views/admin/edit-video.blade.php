@extends('admin.base')

@section('section_title')
{{ __('message.edit_video') }}
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
            <div class="card-title d-flex w-100">
                <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                <a href="/admin/videos" class="btn btn-sm btn-primary text-white">&laquo; {{ __('message.back') }}</a>
            </div>
        </div>
    <div class="card-body">
        <form method="POST" action="/admin/videos/save/{{ $video->id }}">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __("message.title") }}</label>
                        <input id="title" class="form-control" type="text" value="{{ $video->title }}" name="title" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("message.category") }}</label>
                        <select name="category_id" class="form-select">
                            @foreach($video_categories as $c)
                            <option value="{{ $c->id }}" @if($video->category_id == $c->id) selected @endif>{{ $c->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("message.free_for_subs") }}</label>
                        <select name="free_for_subs" class="form-control">
                            <option value="yes" @if($video->free_for_subs == "yes") selected @endif>{{ __("Yes") }}</option>
                            <option value="no" @if($video->free_for_subs == "no") selected @endif>{{ __("No") }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("messsage.price") }}</label>
                        <input id="price" class="form-control" type="text" value="{{ $video->price }}" name="price" required />
                    </div>
                    <div class="form-group iq-button">
                        <button class="btn btn-sm text-uppercase">{{ __('message.save') }}</button>
                    </div>
                </div>
            </div>            
        </form>
    </div>
</div>
@endsection