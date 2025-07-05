@extends('layouts.app', ['module_title' => 'FontAwesome Icons'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="fw-bold">Icons</h4>
                    </div>
                    <div class="border-bottom mt-3"></div>
                    <div class="row">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa-solid fa-address-book',
                                'name' => 'address-book',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-envelope-open',
                                'name' => 'envelope-open',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-id-card',
                                'name' => 'id-card',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-telegram',
                                'name' => 'telegram',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-user-circle',
                                'name' => 'user-circle',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-area-chart',
                                'name' => 'area-chart',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-asterisk',
                                'name' => 'asterisk',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-car',
                                'name' => 'car',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-bars',
                                'name' => 'bars',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-battery-full',
                                'name' => 'battery-full',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-bluetooth',
                                'name' => 'bluetooth',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-book',
                                'name' => 'book',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-bug',
                                'name' => 'bug',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-building',
                                'name' => 'building',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-calculator',
                                'name' => 'calculator',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-calendar',
                                'name' => 'calendar',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-camera',
                                'name' => 'camera',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-commenting',
                                'name' => 'commenting',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-crop',
                                'name' => 'crop',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-download',
                                'name' => 'download',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-folder',
                                'name' => 'folder',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-gift',
                                'name' => 'gift',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-users',
                                'name' => 'users',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-hashtag',
                                'name' => 'hashtag',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-home',
                                'name' => 'home',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-lock',
                                'name' => 'lock',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-graduation-cap',
                                'name' => 'graduation-cap',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-paper-plane',
                                'name' => 'paper-plane',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-star',
                                'name' => 'star',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-tag',
                                'name' => 'tag',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-trash',
                                'name' => 'trash',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-upload',
                                'name' => 'upload',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-university',
                                'name' => 'university',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-wifi',
                                'name' => 'wifi',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-thumbs-up',
                                'name' => 'thumbs-up',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-train',
                                'name' => 'train',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-file',
                                'name' => 'file',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-snapchat',
                                'name' => 'snapchat',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-twitter',
                                'name' => 'twitter',
                            ])
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            @include('components/widget/IconComponent', [
                                'className' => 'fa fa-wordpress',
                                'name' => 'wordpress',
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
