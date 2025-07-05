@extends('layouts.guest')

@section('content')
    <section class="sign-in-page"  style="background: url({{ asset('dashboard/images/login/login.jpg') }}) no-repeat scroll 0 0;">
        <div class="h-100 container">
           <div class="justify-content-center align-items-center h-100 row">
              <div class="col-12  col-md-6 col-sm-12">
                 <div class="sign-user_card ">
                    <div class="sign-in-page-data">
                       <div class="sign-in-from w-100 m-auto"><img src="{{asset('dashboard/images/dashboard/login/mail.png')}}" width="80" alt="" loading="lazy">
                          <h3 class="mt-3 mb-0">Success !</h3>
                          <p class="text-white">A email has been send to <a role="button" tabindex="0"
                                href="/cdn-cgi/l/email-protection"
                                data-cfemail="5f26302a2d3a323e36331f3b30323e3631713c303271"
                                class="__cf_email__ bg-dark border-0 p-0 btn btn-primary">[email&nbsp;protected]</a> Please
                             check for an email from company and click on the included link to reset your password.</p>
                          <div class="d-inline-block w-100"><a class="btn btn-primary mt-3" href="/">Back to Home</a></div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </section>
@endsection
