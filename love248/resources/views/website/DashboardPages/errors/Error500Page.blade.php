
@extends('layouts.guest')

@section('content')
<div class="wrapper">
    <div class="pt-5 container">
        <div class="no-gutters height-self-center row">
            <div class="text-center align-self-center col-sm-12">
                <div class="iq-error position-relative">
                    <img src="{{asset('dashboard/images/error/500.png')}}" class="img-fluid iq-error-img w-50" alt="" loading="lazy" />
                    <h1 class="text-in-box">500</h1>
                    <h2 class="mb-0">Oops! This Page is Not Working.</h2>
                    <p>The requested is Internal Server Error.</p><a class="btn btn-primary mt-3" href="/"><i class="ri-home-4-line"></i>Back to Home</a>
                </div>
            </div>
        </div>
    </div>
      </div>
@endsection

