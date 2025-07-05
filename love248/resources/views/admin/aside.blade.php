<aside class="sidebar sidebar-base sidebar-white sidebar-default navs-rounded-all " id="first-tour"
    data-toggle="main-sidebar" data-sidebar="responsive">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ route('home') }}" class="navbar-brand">
            <!--Logo start-->
            <img class="logo-normal" src="{{ asset(opt('favicon', 'favicon.png')) }}" alt="#">
            <img class="logo-normal logo-white" src="{{ asset(opt('site_logo')) }}" alt="#">
            <img class="logo-full" src="{{ asset(opt('site_logo')) }}" alt="#">
            <img class="logo-full logo-full-white" src="{{ asset(opt('site_logo')) }}" alt="#">
            <!--logo End--> </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="chevron-right">
                <svg xmlns="http://www.w3.org/2000/svg" height="1.2rem" viewBox="0 0 512 512" fill="white">
                    <path
                        d="M470.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 256 265.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L210.7 256 73.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z" />
                </svg>
            </i>
            <i class="chevron-left">
                <svg xmlns="http://www.w3.org/2000/svg" height="1.2rem" viewBox="0 0 512 512" fill="white"
                    transform="rotate(180)">
                    <path
                        d="M470.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 256 265.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L210.7 256 73.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z" />
                </svg>
            </i>
        </div>
    </div>
    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <!-- Sidebar Menu Start -->
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" aria-current="page" href="/admin">
                        <i class="icon" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Dashboard"
                            data-bs-original-title="Dashboard">
                            <svg width="20" class="icon-20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4"
                                    d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                    fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                    fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">{{ __('message.dashboard') }}</span>
                    </a>
                </li>
                @can('streamer-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/streamers') ? 'active' : '' }}" aria-current="page"
                            href="/admin/streamers">
                            <i class="fa-solid fa-headset"></i>
                            <span class="item-name">{{ __('message.streamers') }}</span>
                        </a>
                    </li>
                @endcan
                @can('user-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}" aria-current="page"
                            href="/admin/users">
                            <i class="fa-solid fa-users"></i>
                            <span class="item-name">{{ __('message.users') }}</span>
                        </a>
                    </li>
                @endcan
                @can('role-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/role-list') ? 'active' : '' }}" aria-current="page"
                            href="/admin/role-list">
                            <i class="fa-solid fa-users"></i>
                            <span class="item-name">{{ __('message.roles') }}</span>
                        </a>
                    </li>
                @endcan
                @can('subscription-plan-sell-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/subscription-sells') ? 'active' : '' }}"
                            href="/admin/subscription-sells">
                            <i class="fa-solid fa-bank"></i>
                            <span class="item-name">{{ __('message.subscriptionplansell') }}</span>
                        </a>
                    </li>
                @endcan
                @can('subscription-plan-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/subscription-plans') ? 'active' : '' }}"
                            href="/admin/subscription-plans">
                            <i class="fa-solid fa-box-open"></i>
                            <span class="item-name">{{ __('message.subscriptionplan') }}</span>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/token-sales') ? 'active' : '' }}" href="/admin/token-sales">
                            <i class="fa-solid fa-bank"></i>
                            <span class="item-name">Token Sales</span>
                        </a>
                    </li> 

                 <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/token-packs') ? 'active' : '' }}" href="/admin/token-packs">
                            <i class="fa-solid fa-box-open"></i>
                            <span class="item-name">Token Packages</span>
                        </a>
                    </li> 

                <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/payout-requests') ? 'active' : '' }}" href="/admin/payout-requests">
                            <i class="fa-solid fa-shop"></i>
                            <span class="item-name">Payout Requests</span>
                        </a>
                    </li>
                @can('videos-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/videos') ? 'active' : '' }}" href="/admin/videos">
                            <i class="fa-solid fa-film"></i>
                            <span class="item-name">{{ __('message.videos') }}</span>
                        </a>
                    </li>
                @endcan
                {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/subscriptions') ? 'active' : '' }}" href="/admin/subscriptions">
                            <i class="fa-solid fa-film"></i>
                            <span class="item-name">Subscribers</span>
                        </a>
                    </li> --}}
                @can('commission-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/commission-list') ? 'active' : '' }}"
                            href="/admin/commission-list">
                            <i class="fa-solid fa-user-check"></i>
                            <span class="item-name">{{ __('message.commission') }}</span>
                        </a>
                    </li>
                @endcan
                 <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/streamer-bans') ? 'active' : '' }}" href="/admin/streamer-bans">
                        <i class="fa-solid fa-ban"></i>
                            <span class="item-name">Streamer Bans</span>
                        </a>
                    </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/streams*') ? 'active' : '' }}" href="/admin/streams">
                        <i class="fa-solid fa-video"></i>
                        <span class="item-name">{{ __('Stream Management') }}</span>
                    </a>
                </li>
                @can('streamer-catgory-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/categories') ? 'active' : '' }}"
                            href="/admin/categories">
                            <i class="fa-solid fa-tag"></i>
                            <span class="item-name">{{ __('message.streamer_categories') }}</span>
                        </a>
                    </li>
                @endcan
                @can('video-catgory-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/video-categories') ? 'active' : '' }}"
                            href="/admin/video-categories">
                            <i class="fa-solid fa-tag"></i>
                            <span class="item-name">{{ __('message.video_categories') }}</span>
                        </a>
                    </li>
                @endcan
                @can('stream-earning-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/streamer-earning') ? 'active' : '' }}"
                            href="/admin/streamer-earning">
                            <i class="fa-solid fa-money-bill"></i>
                            <span class="item-name">{{ __('message.stream_earning') }}</span>
                        </a>
                    </li>
                @endcan
                @can('video-sales-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/videos-sales') ? 'active' : '' }}"
                            href="/admin/videos-sales">
                            <i class="fa-solid fa-money-bill"></i>
                            <span class="item-name">{{ __('message.video_sales') }}</span>
                        </a>
                    </li>
                @endcan
                @can('gallery-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/galleries') ? 'active' : '' }}"
                            href="/admin/galleries">
                            <i class="fa-solid fa-money-bill"></i>
                            <span class="item-name">{{ __('message.gallery') }}</span>
                        </a>
                    </li>
                @endcan
                @can('gallery-sales-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/gallery-sales') ? 'active' : '' }}"
                            href="/admin/gallery-sales">
                            <i class="fa-solid fa-money-bill"></i>
                            <span class="item-name">{{ __('message.gallery_sales') }}</span>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/transactions') ? 'active' : '' }}"
                        href="/admin/transactions">
                        <i class="fa-solid fa-receipt"></i>
                        <span class="item-name">{{ __('Transactions') }}</span>
                    </a>
                </li>
                {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/tag-pixels') ? 'active' : '' }}" href="{{route('admin.tag-pixels.index')}}">
                            <i class="fa-solid fa-code"></i>
                            <span class="item-name">TAG PIXELS ADS</span>
                        </a>
                    </li> --}}
                @can('pages-manger-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/cms') ? 'active' : '' }}" href="/admin/cms">
                            <i class="fa-solid fa-bookmark"></i>
                            <span class="item-name">{{ __('message.pages_manager') }}</span>
                        </a>
                    </li>
                @endcan
                @can('app-config')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/configuration') ? 'active' : '' }}"
                            href="/admin/configuration">
                            <i class="fa pull-right hidden-xs showopacity fa-cog"></i>
                            <span class="item-name">{{ __('message.app_configuration') }}</span>
                        </a>
                    </li>
                @endcan

                @can('send-mails-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/email-campaigns*') ? 'active' : '' }}"
                            href="{{ route('admin.email-campaigns.index') }}">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <span class="item-name">{{ __('Email Campaigns') }}</span>
                        </a>
                    </li>
                @endcan
                @can('mail-configuration')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/mailconfiguration') ? 'active' : '' }}"
                            href="/admin/mailconfiguration">
                            <i class="fa-solid fa-at"></i>
                            <span class="item-name">{{ __('message.mail_server') }}</span>
                        </a>
                    </li>
                @endcan
                @can('notification-management')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/notifications*') ? 'active' : '' }}"
                            href="{{ route('admin.notifications.index') }}">
                            <i class="fa-solid fa-bell"></i>
                            <span class="item-name">{{ __('SNS Notifications') }}</span>
                        </a>
                    </li>
                @endcan
                @can('cloud-storage')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/cloud') ? 'active' : '' }}" href="/admin/cloud">
                            <i class="fa-solid fa-cloud"></i>
                            <span class="item-name">{{ __('message.cloud_storage') }}</span>
                        </a>
                    </li>
                @endcan
                @can('report-users')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/report/user') ? 'active' : '' }}"
                            href="/admin/report/user">
                            <i class="fa fa-flag"></i>
                            <span class="item-name">{{ __('message.report_users') }}</span>
                        </a>
                    </li>
                @endcan
                @can('report-stream')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/report/stream') ? 'active' : '' }}"
                            href="/admin/report/stream">
                            <i class="fa fa-flag"></i>
                            <span class="item-name">{{ __('message.report_stream') }}</span>
                        </a>
                    </li>
                @endcan
                @can('report-content')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/report/content') ? 'active' : '' }}"
                            href="/admin/report/content">
                            <i class="fa fa-flag"></i>
                            <span class="item-name">{{ __('message.report_content') }}</span>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/config-logins') ? 'active' : '' }}"
                        href="/admin/config-logins">
                        <i class="fa-solid fa-user-tag"></i>
                        <span class="item-name">{{ __('message.admin_configuration') }}</span>
                    </a>
                </li>
            </ul>
            <!-- Sidebar Menu End -->
        </div>
    </div>
    <div class="sidebar-footer"></div>
</aside>
