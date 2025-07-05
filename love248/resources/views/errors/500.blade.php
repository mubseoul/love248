@extends('errors::layout')

@section('content')
<div class="max-w-4xl mx-auto my-20 px-5">
    <div class="mt-5 py-5 px-4 text-center bg-white rounded-lg">
        <h3 class="text-3xl text-indigo-800 heading-gradient font-black mb-5 text-gray-primary">{{ __('500 - Server Error') }}</h3>

        <div class="text-2xl text-center text-stone-600 mt-10 font-bold">
            {{ __('We have encountered a server error, our developers will investigate shortly') }}
            <br /><br />
            <a href="{{route('home')}}" class="font-black text-3xl heading-gradient text-gray-primary">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('HOME') }}
            </a>
        </div>
    </div>
</div>
@endsection