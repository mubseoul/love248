@extends('layouts.guest')

@section('content')
<section class="sign-in-page"  style="background: url({{ asset('dashboard/images/login/login.jpg') }}) no-repeat scroll 0 0;">
    <div class="container">
       <div class="justify-content-center align-items-center height-self-center row">
          <div class="align-self-center col-lg-5 col-md-12">
             <div class="sign-user_card">
                <div class="sign-in-page-data">
                   <div class="sign-in-from w-100 m-auto">
                      <h3 class="mb-3 text-center">Sign in</h3>
                      <form action class="mt-4">
                         <div class="mb-3"><input placeholder="Enter email" autocomplete="off" required type="email"
                               id="exampleInputEmail1" class="mb-0 form-control" /></div>
                         <div class="mb-3"><input placeholder="Password" required type="password"
                               id="exampleInputPassword2" class="mb-0 form-control" /></div>
                         <div class="d-flex justify-content-between align-items-cente sign-info"><button type="button"
                               class="btn btn-btn btn-primary">Sign
                               in</button>
                            <div class="custom-control custom-checkbox d-inline-block"><input type="checkbox"
                                  class="form-check-input mx-2" id="customCheck" /><label class="form-check-label"
                                  for="customCheck">Remember Me</label></div>
                         </div>
                      </form>
                   </div>
                </div>
                <div class="mt-3">
                   <div class="d-flex justify-content-center links">Don't have an
                      account?
                      <a class="text-primary ms-2" href="{{route('dashboard.register')}}">Sign Up</a>
                   </div>
                   <div class="d-flex justify-content-center links"><a class="f-link"
                         href="{{route('dashboard.reset-password')}}">Forgot
                         your
                         password?</a></div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </section>
@endsection

