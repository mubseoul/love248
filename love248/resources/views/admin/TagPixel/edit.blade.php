@extends('admin.base')

@section('section_title')
<strong>{{ __('Tag Pixels Ads') }}</strong>
<br />
<a href="{{route('admin.tag-pixels.index')}}" class="text-indigo-700 font-semibold hover:underline">
    {{ __('Back') }}
</a>
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3">
        <div class="card-title">
            <h4>@yield('section_title', '')</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tag-pixels.update') }}">
            {{ csrf_field() }}
            <div class="form-group">
                @if (!empty($tagPixel))
                <input type="hidden" name="id" value="{{ $tagPixel->id ?? '' }}">
                @endif
                <label class="form-label">{{ __("Select Tag") }}</label>
                <select name="type" class="form-select">
                    @foreach(['header', 'footer'] as $option)
                        <option value="{{ $option }}" @if(isset($tagPixel) && $tagPixel->type == $option) selected @endif>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                @if ($errors->has('type'))
                    <span class="text-red-600">{{ $errors->first('type') }}</span>
                @endif
                <label class="form-label">{{ __("Input Code") }}</label>
                <textarea class="form-control" name="code" class="w-full">{{$tagPixel->code ?? ''}}</textarea>
                @if ($errors->has('code'))
                    <span class="text-red-600">{{ $errors->first('code') }}</span>
                @endif
            </div><!-- /.col-xs-12 col-md-6 -->
            <div class="form-group">
                <button class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
<div>



@endsection