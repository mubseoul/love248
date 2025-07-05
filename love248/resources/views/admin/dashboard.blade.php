@extends('admin.base')

@push('adminExtraCSS')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/morris.css') }}" /> -->
@endpush
@section('extra_top')
<div class="row">
    
    <div class="col-lg-8">
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0 font-size-14">
                                @if ($allUsers == 1)
                                {{ __('message.user') }}
                                @else
                                {{ __('message.users') }}
                                @endif
                                </p>
                            </div>
                            <div class="icon iq-icon-box-top bg-success rounded-circle d-flex align-items-center justify-content-center" style="width:25px;height:25px;">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.9849 15.3462C8.11731 15.3462 4.81445 15.931 4.81445 18.2729C4.81445 20.6148 8.09636 21.2205 11.9849 21.2205C15.8525 21.2205 19.1545 20.6348 19.1545 18.2938C19.1545 15.9529 15.8735 15.3462 11.9849 15.3462Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.9849 12.0059C14.523 12.0059 16.5801 9.94779 16.5801 7.40969C16.5801 4.8716 14.523 2.81445 11.9849 2.81445C9.44679 2.81445 7.3887 4.8716 7.3887 7.40969C7.38013 9.93922 9.42394 11.9973 11.9525 12.0059H11.9849Z"
                                        stroke="currentColor" stroke-width="1.42857"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{ $allUsers }}</h4>
                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0"> {{ __('message.streamers') }} </p>
                            </div>
                            <div class="icon iq-icon-box-top rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:25px;height:25px;">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.1614 12.0531C15.1614 13.7991 13.7454 15.2141 11.9994 15.2141C10.2534 15.2141 8.83838 13.7991 8.83838 12.0531C8.83838 10.3061 10.2534 8.89111 11.9994 8.89111C13.7454 8.89111 15.1614 10.3061 15.1614 12.0531Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.998 19.355C15.806 19.355 19.289 16.617 21.25 12.053C19.289 7.48898 15.806 4.75098 11.998 4.75098H12.002C8.194 4.75098 4.711 7.48898 2.75 12.053C4.711 16.617 8.194 19.355 12.002 19.355H11.998Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{ $allStreamers }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0 font-size-14">{{ __('Tokens Sold') }}</p>
                            </div>
                            <div class="icon iq-icon-box-top rounded-circle bg-warning">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M13.1043 4.17701L14.9317 7.82776C15.1108 8.18616 15.4565 8.43467 15.8573 8.49218L19.9453 9.08062C20.9554 9.22644 21.3573 10.4505 20.6263 11.1519L17.6702 13.9924C17.3797 14.2718 17.2474 14.6733 17.3162 15.0676L18.0138 19.0778C18.1856 20.0698 17.1298 20.8267 16.227 20.3574L12.5732 18.4627C12.215 18.2768 11.786 18.2768 11.4268 18.4627L7.773 20.3574C6.87023 20.8267 5.81439 20.0698 5.98724 19.0778L6.68385 15.0676C6.75257 14.6733 6.62033 14.2718 6.32982 13.9924L3.37368 11.1519C2.64272 10.4505 3.04464 9.22644 4.05466 9.08062L8.14265 8.49218C8.54354 8.43467 8.89028 8.18616 9.06937 7.82776L10.8957 4.17701C11.3477 3.27433 12.6523 3.27433 13.1043 4.17701Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{ $tokensSold }}</h4>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0 font-size-14">{{ __('message.earnings') }}</p>
                            </div>
                            <div class="icon iq-icon-box-top rounded-circle bg-info d-flex align-items-center justify-content-center" style="width:25px;height:25px;">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M2.75 12C2.75 17.108 6.891 21.25 12 21.25C17.108 21.25 21.25 17.108 21.25 12C21.25 6.892 17.108 2.75 12 2.75C6.891 2.75 2.75 6.892 2.75 12Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M8.52832 10.5576L11.9993 14.0436L15.4703 10.5576"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{ opt('payment-settings.currency_symbol') . $tokensAmount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0 font-size-14">{{ __('message.streaming') }}</p>
                            </div>
                            <div class="icon iq-icon-box-top rounded-circle bg-success d-flex align-items-center justify-content-center" style="width:25px;height:25px;">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.9849 15.3462C8.11731 15.3462 4.81445 15.931 4.81445 18.2729C4.81445 20.6148 8.09636 21.2205 11.9849 21.2205C15.8525 21.2205 19.1545 20.6348 19.1545 18.2938C19.1545 15.9529 15.8735 15.3462 11.9849 15.3462Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.9849 12.0059C14.523 12.0059 16.5801 9.94779 16.5801 7.40969C16.5801 4.8716 14.523 2.81445 11.9849 2.81445C9.44679 2.81445 7.3887 4.8716 7.3887 7.40969C7.38013 9.93922 9.42394 11.9973 11.9525 12.0059H11.9849Z"
                                        stroke="currentColor" stroke-width="1.42857"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{$privateStreaming }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0"> {{ __('message.videos') }} </p>
                            </div>
                            <div class="icon iq-icon-box-top rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:25px;height:25px;">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.1614 12.0531C15.1614 13.7991 13.7454 15.2141 11.9994 15.2141C10.2534 15.2141 8.83838 13.7991 8.83838 12.0531C8.83838 10.3061 10.2534 8.89111 11.9994 8.89111C13.7454 8.89111 15.1614 10.3061 15.1614 12.0531Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.998 19.355C15.806 19.355 19.289 16.617 21.25 12.053C19.289 7.48898 15.806 4.75098 11.998 4.75098H12.002C8.194 4.75098 4.711 7.48898 2.75 12.053C4.711 16.617 8.194 19.355 12.002 19.355H11.998Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{ $buyVideos }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="iq-cart-text text-capitalize">
                                <p class="mb-0 font-size-14">{{ __('message.gallery') }}</p>
                            </div>
                            <div class="icon iq-icon-box-top rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width:25px;height:25px;">
                                <svg fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="16" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M13.1043 4.17701L14.9317 7.82776C15.1108 8.18616 15.4565 8.43467 15.8573 8.49218L19.9453 9.08062C20.9554 9.22644 21.3573 10.4505 20.6263 11.1519L17.6702 13.9924C17.3797 14.2718 17.2474 14.6733 17.3162 15.0676L18.0138 19.0778C18.1856 20.0698 17.1298 20.8267 16.227 20.3574L12.5732 18.4627C12.215 18.2768 11.786 18.2768 11.4268 18.4627L7.773 20.3574C6.87023 20.8267 5.81439 20.0698 5.98724 19.0778L6.68385 15.0676C6.75257 14.6733 6.62033 14.2718 6.32982 13.9924L3.37368 11.1519C2.64272 10.4505 3.04464 9.22644 4.05466 9.08062L8.14265 8.49218C8.54354 8.43467 8.89028 8.18616 9.06937 7.82776L10.8957 4.17701C11.3477 3.27433 12.6523 3.27433 13.1043 4.17701Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <h4 class=" mb-0">{{ $buyGallery }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center top-rated-slider">
                <div class="iq-header-title ">
                    <h4 class="card-title text-capitalize">{{__('message.top_rated_items')}} </h4>
                </div>
                <div class="top-swiper-arrow d-flex align-items-center">
                    <div class="swiper-button-prev me-2"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
            <div class="card-body ">
                <div class="swiper pt-2 pt-md-4 pt-lg-4 overflow-hidden" data-swiper="top-slider">
                    <ul class="swiper-wrapper list-inline p-0 m-0">
                    @foreach($topratedvideos as $v)
                        <li class="iq-rated-box swiper-slide">
                            <div class="iq-card mb-0">
                                <div class="iq-card-body p-0">
                                    <div class="iq-thumb">
                                        <a href="javascript:void(0)">
                                            <img src="{{$v->thumbnail}}" class="w-100 img-border-radius" alt="{{$v->slug}}">
                                        </a>
                                    </div>
                                    <div class="iq-feature-list mt-3">
                                        <h6 class="font-weight-600 mb-0">{{$v->title}}</h6>
                                        <p class="mb-0 mt-2">{{$v->category->category}}</p>
                                        <div class="d-flex align-items-center my-2 iq-ltr-direction">
                                            <p class="mb-0 me-2"><i class="fa-regular fa-eye me-1"></i>{{$v->views}}</p>
                                            <!-- <p class="mb-0 "><i class="fa-solid fa-download ms-2 me-1"></i>30k</p> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card card-block card-stretch card-height">
            <div class="card-header">
                <div class="iq-header-title">
                    <h4 class="card-title text-center text-capitalize">{{__('message.users_of_product')}}</h4>
                </div>
            </div>
            <div class="card-body pb-0">
                <div id="view-chart-01">
                </div>
                <div class="row mt-1 align-items-stretch">
                    <div class="col-sm-6 col-md-3 col-lg-6 iq-user-list">
                        <div class="card border-0">
                            <div class="card-body bg-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="iq-user-box bg-primary"></div>
                                    <div class=" ">
                                        <p class="mb-0 font-size-14 line-height text-capitalize">{{__('message.new_customer')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-6 iq-user-list">
                        <div class="card border-0">
                            <div class="card-body bg-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="iq-user-box bg-warning"></div>
                                    <div class=" ">
                                        <p class="mb-0 font-size-14 line-height text-capitalize">{{__('message.exsisting_subscribers')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-6 iq-user-list">
                        <div class="card border-0">
                            <div class="card-body bg-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="iq-user-box bg-info"></div>
                                    <div class=" ">
                                        <p class="mb-0 font-size-14 line-height">{{__('message.daily_visitors')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-6 iq-user-list">
                        <div class="card border-0">
                            <div class="card-body bg-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="iq-user-box bg-danger"></div>
                                    <div class=" ">
                                        <p class="mb-0 font-size-14 line-height">Extented <br>
                                            Subscriber's
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12  col-lg-4">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title text-capitalize">{{__('message.categories')}}</h4>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="view-chart-03"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title text-capitalize">{{__('message.top_category')}}</h4>
                </div>
                <!-- <div class="card-header-toolbar d-flex align-items-center seasons">
                    <div class="iq-custom-select d-inline-block sea-epi s-margin">
                        <select name="cars" class="form-control season-select">
                            <option value="season1">Today</option>
                            <option value="season2">This Week</option>
                            <option value="season2">This Month</option>
                        </select>
                    </div>
                </div> -->
            </div>
            <div class="card-body row align-items-center">
                <div class="col-lg-7">
                    <div class="row list-unstyled mb-0 pb-0">
                        @foreach($videocategories as $c)
                            <div class="col-sm-6 col-md-4 col-lg-6 mb-3">
                                <div class="iq-progress-bar progress-bar-vertical iq-bg-secondary">
                                    <span class="bg-secondary" data-percent="100"
                                        style="transition: height 2s ease 0s; width: 100%; height: 70%;"></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="iq-icon-box-view rounded me-3 text-secondary">
                                        <i class="fa-solid fa-masks-theater font-size-32"></i>
                                    </div>
                                    <div class=" ">
                                        <p class="mb-0 font-size-14 line-height">{{$c->category}}</p>
                                        <!-- <small class="text-secondary mb-0">+44%</small> -->
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    <div id="view-chart-02" class="view-cahrt-02"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    <h4>Recently Viewed Items</h4>
                </div>
                <div class="card-header-toolbar d-flex align-items-center seasons">
                    <div class="iq-custom-select d-inline-block sea-epi s-margin">
                        <select name="cars" class="form-control season-select">
                            <option value="season1">Most Likely</option>
                            <option value="season2">Unlikely</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table-view table-space">
                    <table class="data-tables table custom-table movie_table" style="width:100%"
                        data-toggle="data-table">
                        <thead>
                            <tr>
                                <th style="width:20%;">Movie</th>
                                <th style="width:10%;">Rating</th>
                                <th style="width:20%;">Category</th>
                                <th style="width:10%;">Download/Views</th>
                                <th style="width:10%;">User</th>
                                <th style="width:20%;">Date</th>
                                <th style="width:10%;"><i class="fa-solid fa-heart"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="iq-movie">
                                            <a href="javascript:void(0);"><img
                                                    src="../assets/images/movie-thumb/07.jpg"
                                                    class="img-border-radius avatar-40 img-fluid"
                                                    alt="recentlyImg-01"></a>
                                        </div>
                                        <div class="text-start ms-3">
                                            <p class="mb-0">Boop Bitty</p>
                                            <small></small>
                                        </div>
                                    </div>
                                </td>
                                <td><i class="lar la-star me-2"></i>8.2</td>
                                <td>Thriller</td>
                                <td>
                                    <i class="fa-regular fa-eye"></i>
                                </td>
                                <td>Unsubcriber</td>
                                <td>23 July,2020</td>
                                <td><i class="fa-solid fa-heart text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="iq-movie">
                                            <a href="javascript:void(0);"><img
                                                    src="../assets/images/movie-thumb/01.jpg"
                                                    class="img-border-radius avatar-40 img-fluid"
                                                    alt="recentlyImg-02"></a>
                                        </div>
                                        <div class="text-start ms-3">
                                            <p class="mb-0">Chamions</p>
                                            <small>1h 40min</small>
                                        </div>
                                    </div>
                                </td>
                                <td><i class="lar la-star me-2"></i>9.2</td>
                                <td>Horror</td>
                                <td>
                                    <i class="fa-regular fa-eye"></i>
                                </td>
                                <td>Unsubcriber</td>
                                <td>21 July,2020</td>
                                <td><i class="fa-solid fa-heart text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="iq-movie">
                                            <a href="javascript:void(0);"><img
                                                    src="../assets/images/movie-thumb/05.jpg"
                                                    class="img-border-radius avatar-40 img-fluid"
                                                    alt="recentlyImg-03"></a>
                                        </div>
                                        <div class="text-start ms-3">
                                            <p class="mb-0">Last Race</p>
                                            <small></small>
                                        </div>
                                    </div>
                                </td>
                                <td><i class="lar la-star me-2"></i>7.2</td>
                                <td>Horror</td>
                                <td>
                                    <i class="fa-regular fa-eye"></i>
                                </td>
                                <td>Subcriber</td>
                                <td>22 July,2020</td>
                                <td><i class="fa-solid fa-heart text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="iq-movie">
                                            <a href="javascript:void(0);"><img
                                                    src="../assets/images/movie-thumb/10.jpg"
                                                    class="img-border-radius avatar-40 img-fluid"
                                                    alt="recentlyImg-04"></a>
                                        </div>
                                        <div class="text-start ms-3">
                                            <p class="mb-0">Dino Land</p>
                                            <small></small>
                                        </div>
                                    </div>
                                </td>
                                <td><i class="lar la-star me-2"></i>8.5</td>
                                <td>Action</td>
                                <td>
                                    <i class="fa-regular fa-eye"></i>
                                </td>
                                <td>Unsubcriber</td>
                                <td>24 July,2020</td>
                                <td><i class="fa-solid fa-heart text-primary"></i></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="iq-movie">
                                            <a href="javascript:void(0);"><img
                                                    src="../assets/images/movie-thumb/04.jpg"
                                                    class="img-border-radius avatar-40 img-fluid"
                                                    alt="recentlyImg-05"></a>
                                        </div>
                                        <div class="text-start ms-3">
                                            <p class="mb-0">The Last Breath</p>
                                            <small></small>
                                        </div>
                                    </div>
                                </td>
                                <td><i class="lar la-star me-2"></i>8.9</td>
                                <td>Horror</td>
                                <td>
                                    <i class="fa-regular fa-eye"></i>
                                </td>
                                <td>Subcriber</td>
                                <td>25 July,2020</td>
                                <td><i class="fa-solid fa-heart text-primary"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
</div>

<!-- LINE CHART -->
<!-- <div class="w-full bg-white mt-10 rounded p-3">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border"><strong>{{ __('Past 30 Days') }}</strong></div>
            <div class="box-body">
                <div class="chart-responsive">
                    <div class="chart" id="past-30-days"></div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- subscription earnings -->
@endsection

    @section('section_title')
    <strong>{{ __('Dashboard Stats') }}</strong>
    @endsection

    @section('section_body')
    @endsection

    @push('adminExtraJS')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/morris.min.js') }}"></script>
    <script src="{{ asset('js/raphael-min.js') }}"></script>

    {{-- attention, this is dynamically appended as it contains data from database --}}
    <!-- <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            new Morris.Line({
                element: 'past-30-days',
                data: [
                    @if (isset($earnings) and count($earnings))
                        @foreach ($earnings as $d)
                            {
                                date: '{{ $d['date'] }}',
                                amount: '{{ $d['amount'] }}',
                                tokens: '{{ $d['tokens'] }}',
                            },
                        @endforeach
                    @else
                        {
                            date: '{{ date('jS F Y') }}',
                            amount: 0,
                            tokens: 0,
                        }
                    @endif
                ],
                xkey: 'date',
                ykeys: ['tokens', 'amount'],
                labels: ['{{ __("Amount")  }}', '{{__("Tokens")  }}']
            });
        });
    </script> -->
    @endpush