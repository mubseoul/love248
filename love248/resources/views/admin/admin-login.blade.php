<x-guest-layout>
<div class="wrapper">
      <section class="sign-in-page" style="background-image:url('{{asset('images/Login-img2.png')}}')">
         <div class="container">
            <div class="justify-content-center align-items-center height-self-center row">
               <div class="align-self-center col-lg-5 col-md-12">
                  <div class="sign-user_card">
                     <div class="sign-in-page-data">
                        <div class="sign-in-from w-100 m-auto">
                           <h3 class="mb-3 text-center">Admin Login</h3>
                           <x-auth-session-status class="mb-4" :status="session('status')" />
                            <!-- Validation Errors -->
                            @if (isset($message) && !empty($message))
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                {{ $message }}
                            </div>
                            @endif
                           <form action="" method="POST" class="mt-4">
                           @csrf   
                           <div class="mb-3">
                                <input placeholder="Enter email" autocomplete="off" required
                                    type="email" id="email" class="mb-0 form-control" name="email" :value="old('email')" required
                                    autofocus />
                            </div>
                            <div class="mb-3">
                                <input placeholder="Password" required type="password" id="exampleInputPassword2" class="mb-0 form-control" name="password" />
                            </div>
                            <div class="d-flex justify-content-between align-items-cente sign-info">
                                <button class="btn btn-btn btn-primary">Sign in</button>
                                <!-- <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox"
                                       class="form-check-input mx-2" id="customCheck" />
                                       <label class="form-check-label"
                                       for="customCheck">Remember Me</label>
                                </div> -->
                              </div>
                           </form>
                        </div>
                     </div>
                     <!-- <div class="mt-3">
                        <div class="d-flex justify-content-center links">Don't have an
                           account?
                           <a class="text-primary ms-2" href="../../dashboard/auth/sign-up.html">Sign Up</a>
                        </div>
                        <div class="d-flex justify-content-center links"><a class="f-link"
                              href="../../dashboard/auth/recoverpw.html">Forgot
                              your
                              password?</a></div>
                     </div> -->
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
   </x-guest-layout>
