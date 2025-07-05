@extends('layouts.guest')
@section('content')
<section class="sign-in-page"  style="background: url({{ asset('dashboard/images/login/login.jpg') }}) no-repeat scroll 0 0;">
    <div class="h-100 container">
       <div class="justify-content-center align-items-center h-100 row">
          <div class="col-12 col-md-6 col-sm-12">
             <div class="sign-user_card ">
                <div class="sign-in-page-data">
                   <div class="sign-in-from w-100 m-auto">
                      <h4 class="mt-3 text-white mb-0 text-center">Hi ! Michael Smith</h4>
                      <p class="text-white text-center">Enter your password to access the admin.</p>
                      <form class="mt-4">
                         <div class="mb-3"><input placeholder="Password" autocomplete="off" required="" type="email"
                               id="exampleInputEmail2" class="mb-0 form-control"></div>
                         <div class="d-inline-block w-100"><a class="btn btn-primary float-right"
                               href="/auth/sign-in">Log In</a></div>
                      </form>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </section>
 @endsection
