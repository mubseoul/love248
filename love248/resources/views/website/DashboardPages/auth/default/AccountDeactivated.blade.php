@extends('layouts.guest')
@section('content')
<section class="sign-in-page" style="background-image: url('{{ asset('dashboard/images/login/login.jpg') }}')">
    <div class="container">
       <div class="row justify-content-center align-items-center height-self-center">
          <div class="col col-lg-5 col-md-12 align-self-center form-padding">
             <div class="sign-user_card">
                <div class="sign-in-page-data">
                   <div class="sign-in-from w-100 m-auto">
                      <h2 class="mb-2">Account De-activate</h2>
                      <p class="">Enter your details to de-activate your account</p>

                      <form class="mt-4" action="">
                         <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" id="email" type="email" aria-describedby="email"
                               placeholder="xyz@example.com"></input>
                         </div>
                         <div class="form-group mt-3">
                            <label for="name">Username</label>
                            <input class="form-control" id="name" type="text" aria-describedby="name" placeholder="XYZ">
                            </input>
                         </div>
                         <button class="btn btn-primary mt-2">De-activate</button>
                      </form>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </section>
 @endsection
