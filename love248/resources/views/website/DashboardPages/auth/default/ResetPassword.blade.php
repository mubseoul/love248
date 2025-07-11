@extends('layouts.guest')

@section('content')
    <section class="sign-in-page"  style="background: url({{ asset('dashboard/images/login/login.jpg') }}) no-repeat scroll 0 0;">
        <div class="container">
           <div class="justify-content-center align-items-center height-self-center row">
              <div class="align-self-center col-lg-5 col-md-12">
                 <div class="sign-user_card ">
                    <div class="sign-in-page-data">
                       <div class="sign-in-from w-100 m-auto">
                          <h3 class="mb-3 text-center">Reset Password</h3>
                          <p class="text-body">Enter your email address and we'll send you an email with instructions to
                             reset your password.</p>
                          <form action="/" class="mt-4">
                             <div class="mb-3"><input placeholder="Enter email" autocomplete="off" required="" type="email"
                                   id="exampleInputEmail2" class="mb-0 form-control"></div>
                             <div class="sign-info"><button type="button" class="btn btn-btn btn-primary">Reset</button>
                             </div>
                          </form>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </section>
@endsection
