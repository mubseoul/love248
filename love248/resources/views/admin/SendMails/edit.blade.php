@extends('admin.base')

@section('section_title')
<strong>{{ __('Tag Pixels Ads') }}</strong>
<br />
<a href="{{route('admin.tag-pixels.index')}}" class="text-indigo-700 font-semibold hover:underline">
    {{ __('Back') }}
</a>
@endsection

@section('section_body')
<div class="container">
    <form method="POST" action="{{ route('admin.tag-pixels.update') }}">
        {{ csrf_field() }}
        <div class=" items-center bg-white rounded p-3">
            @if (!empty($tagPixel))
            <input type="hidden" name="id" value="{{ $tagPixel->id ?? '' }}">
            @endif
    
            <x-label class="mt-5">{{ __("Select Tag") }}</x-label>
            <x-select name="type" class="w-full my-2">
                @foreach(['header', 'footer'] as $option)
                    <option value="{{ $option }}" @if(isset($tagPixel) && $tagPixel->type == $option) selected @endif>{{ ucfirst($option) }}</option>
                @endforeach
            </x-select>
            
            @if ($errors->has('type'))
                <span class="text-red-600">{{ $errors->first('type') }}</span>
            @endif
            <x-label class="mt-5">{{ __("Input Code") }}</x-label>
            <x-textarea class="my-2" name="code" class="w-full">{{$tagPixel->code ?? ''}}</x-textarea>
            @if ($errors->has('code'))
                <span class="text-red-600">{{ $errors->first('code') }}</span>
            @endif
            <div class="m-auto mt-3 iq-button">
                <x-button className="btn btn-sm text-uppercase">{{ __('Save') }}</x-button>
            </div>
        </div><!-- /.col-xs-12 col-md-6 -->
    </form>
<div>



@endsection