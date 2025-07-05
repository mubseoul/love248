@extends('layouts.guest')
@section('content')
<section class="sign-in-page"  style="background: url({{ asset('dashboard/images/login/login.jpg') }}) no-repeat scroll 0 0;">
    <div class="container">
       <div class="row justify-content-center align-items-center height-self-center">
          <div lg="5" md="12" class="col col-lg-5 col-md-12 align-self-center form-padding">
             <div class="sign-user_card">
                <div class="sign-in-page-data">
                   <div class="sign-in-from w-100 m-auto">
                      <h2 class="mb-2">Two Factor -Verification</h2>
                      <p>Enter your email address and we’ll send you an email with instructions to reset your password
                      </p>

                      <form class="mt-4" action="/auth-login">
                         <div class="form-group floating-label">
                            <label for="email" class="form-label">Phone Number</label>
                            <input id="email" type="email" class="form-control" aria-describedby="email"
                               placeholder="+1 123456789"></input>
                         </div>
                         <button class="btn btn-primary mt-2">Reset</button>

                         <div class="form-group mt-3">
                            <label for="email" class="form-label">Enter the OTP you recieved to veify your device</label>
                            <input type="number" class="form-control" aria-describedby="email"
                               placeholder="0000"></input>
                            </b-form-group>
                            <button class="btn btn-primary mt-2">Verify</button>
                      </form>
                   </div>
                </div>
                <div class="mt-2">
                   <div class="d-flex justify-content-center links">Don't have an account? <a
                         href="{{route('dashboard.register')}}" class="text-primary ms-2">Sign Up</a></div>
                   <div class="d-flex justify-content-center links">
                      <a href="{{route('dashboard.reset-password')}}" class="f-link">Forgot your password?</a>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </section>
@endsection
