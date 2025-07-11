@extends('layouts.guest')
@section('content')
    <section class="sign-in-page"  style="background: url({{ asset('dashboard/images/login/login.jpg') }}) no-repeat scroll 0 0;">
        <div class="container">
           <div class="justify-content-center align-items-center height-self-center row">
              <div class="align-self-center col-lg-7 col-md-12">
                 <div class="sign-user_card ">
                    <div class="sign-in-page-data">
                       <div class="sign-in-from w-100 m-auto">
                          <form action="/" class="">
                             <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3"><label class="form-label">Username</label><input
                                         placeholder="Enter Full Name" autocomplete="off" required="" type="text"
                                         id="exampleInputEmail2" class="mb-0 form-control"></div>
                                </div>
                                <div class="col-md-6">
                                   <div class="mb-3"><label class="form-label">E-mail</label><input placeholder="Enter email"
                                         autocomplete="off" required="" type="email" id="exampleInputEmail3"
                                         class="mb-0 form-control"></div>
                                </div>
                                <div class="col-md-6">
                                   <div class="mb-3"><label class="form-label">First Name</label><input
                                         placeholder="First Name" autocomplete="off" required="" type="text"
                                         id="exampleInputEmail4" class="mb-0 form-control"></div>
                                </div>
                                <div class="col-md-6">
                                   <div class="mb-3"><label class="form-label">Last Name</label><input
                                         placeholder="Last Name" autocomplete="off" required="" type="email"
                                         id="exampleInputEmail5" class="mb-0 form-control"></div>
                                </div>
                                <div class="col-md-6">
                                   <div class="mb-3"><label class="form-label">Password</label><input placeholder="Password"
                                         required="" type="password" id="exampleInputPassword6" class="mb-0 form-control">
                                   </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="mb-3"><label class="form-label">Repeat Password</label><input
                                         placeholder="Password" required="" type="password" id="exampleInputPassword7"
                                         class="mb-0 form-control"></div>
                                </div>
                             </div>
                             <div class="form-check my-2"><input type="radio" id="customRadio1" name="customRadio"
                                   class="form-check-input"><label class="form-check-label" for="customRadio1">Premium-$39 /
                                   3 Months with a 5 day free trial</label></div>
                             <div class="form-check"><input type="radio" id="customRadio2" name="customRadio"
                                   class="form-check-input"><label class="form-check-label" for="customRadio2"> Basic- $19 /
                                   1 Month</label></div>
                             <div class="form-check"><input type="radio" id="customRadio3" name="customRadio"
                                   class="form-check-input"><label class="form-check-label"
                                   for="customRadio3">Free-Free</label></div><button type="button"
                                class="btn btn-btn btn-primary my-2">Sign Up</button>
                          </form>
                       </div>
                    </div>
                    <div class="mt-3">
                       <div class="d-flex justify-content-center links">Already have an account? <a class="text-primary ms-2"
                             href="{{route('dashboard.login')}}">Sign In</a> </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </section>
@endsection
