@extends('admin.base')

@section('section_title')
<strong>{{ __('message.videos_uploaded_by_streamers') }}</strong>
@endsection

@section('section_body')


<div class="alert alert-info d-flex align-items-center" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24">
        <use xlink:href="#info-fill" />
    </svg>
    <div>{{ __('message.videos_alert') }}</div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                </div>
            </div>
            <div class="card-body"> 
            @if(count($videos))
                <div class="table-view table-responsive table-space">
                    <table id="commentTable" class="data-tables table custom-table movie_table" data-toggle="data-table">
                        <thead>
                            <tr class="text-uppercase">
                                <th>{{ __('message.movie') }}</th>
                                <th>{{ __('streamer') }}</th>
                                <th>{{ __('Token') }}</th>
                                <th>{{ __('message.category') }}</th>
                                <th>{{ __('message.view') }}</th>
                                <th>{{ __('message.is_verified') }}</th>
                                <th>{{ __('message.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($videos as $v)
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <a target="_blank" href='{{route("video.page", [ "video"=> $v->id, "slug" => $v->slug ])}}'
                                            class="font-semibold dark:text-gray-100 hover:text-gray-800 text-gray-600 dark:hover:text-gray-400">
                                            <img src="{{$v->thumbnail}}" alt="image" class="rounded-2 avatar avatar-55"  /> 
                                        </a>
                                        <div class="d-flex flex-column ms-3 justify-content-center">
                                            <h6 class="text-capitalize">{{$v->title}}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a target="_blank" href='{{route("channel", ["user"=> $v->streamer->username]) }}' class="text-primary fw-bold"> {{ '@' . $v->streamer->username}} </a>
                                </td>
                                <td>{{ $v->price < 1 ? __("Free") : $v->price . __(" tokens") }}</td>
                                <td>
                                {{ $v->category->category }}
                                    <!-- <a target="_blank" href="{{ route('videos.browse', ['videocategory' => $v->category->id, 'slug' => '-' . $v->category->slug]) }}" class="text-info"> <i class="fa-solid fa-tags"></i> {{ $v->category->category }} </a> -->
                                </td>
                                <td>{{$v->views === 1
                        ? __("1 view")
                        : __(":viewsCount views", ["viewsCount" => $v->views])}}</td>
                        <td>
                            @if($v->status == 1)
                                <span class="mt-2 badge border border-success text-success mt-2">
                                    {{ __('Yes') }}
                                </span>
                                @else
                                <span class="mt-2 badge border border-primary text-primary mt-2">{{ __('No') }}</span>
                                <br />
                                <a href="/admin/video-approve?id={{ $v->id }}" class="text-xs text-primary fw-bold">{{ __("Mark as Verified") }}</a>
                                @endif
                        </td>
                            <td>
                                
                            <div class="d-flex align-items-center list-user-action">
                                @can('videos-edit')
                                    <a class="btn btn-sm btn-icon btn-success rounded" href="/admin/videos/edit/{{ $v->id }}"><i class="fa-solid fa-pencil"></i></a>
                                @endcan
                                @can('videos-delete')
                                    <a href="/admin/videos?remove={{ $v->id }}"
                                onclick="return confirm('{{ __('Are you sure you want to remove this VIDEO?')  }}')" class="btn btn-sm btn-icon btn-primary bg-primary border-0 delete-btn rounded" ><i class="fa-solid fa-trash"></i></a>
                                @endcan
                            </div>
                            </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                        <use xlink:href="#exclamation-triangle-fill01" />
                </svg>
                <div>
                {{ __('No videos uploaded by streamers.') }}
            </div>
            @endif
            </div>
        </div>
    </div>
</div>
<!-- @if (count($videos)) -->

<!-- <div class="flex items-center justify-end p-5">
    <form method="GET">
        <div class="flex items-center">
            <x-input name="search" class="w-52 block mr-1" id="search" type="text" />
            <x-button>{{ __('Search') }}</x-button>
        </div>
    </form>
</div> -->


<!-- <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
    @foreach($videos as $v)
    <div class="mr-5 mb-5 border-2 rounded-lg">
        <div class="relative">
            <a target="_blank" href={{route("video.page", [ "video"=> $v->id, "slug" => $v->slug ])}}
                class="font-semibold dark:text-gray-100 hover:text-gray-800 text-gray-600 dark:hover:text-gray-400">
                <img src="{{$v->thumbnail}}" class="rounded-lg mb-3 " alt="" />
            </a>
            <div
                class="absolute top-5 left-0 bg-indigo-800 text-white font-bold text-sm uppercase rounded-tr rounded-br px-2 py-1">
                {{ $v->price < 1 ? __("Free") : $v->price . __(" tokens") }} </div>
        </div>
        <div class="flex items-start p-2">
            <div class="w-10 flex-shrink-0 mr-2">
                <a target="_blank" href={{route("channel", ["user"=> $v->streamer->username]) }}>
                    <img src={{ $v->streamer->profile_picture}} class="w-10 h-10 rounded-full" />
                </a>
            </div>
            <div class="flex-grow">
                <div>
                    {{$v->title}}
                    <div class="text-gray-600 text-sm">
                        <a target="_blank"
                            href="{{ route('videos.browse', ['videocategory' => $v->category->id, 'slug' => '-' . $v->category->slug]) }}">
                            <i class="fa-solid fa-tags"></i>
                            {{ $v->category->category }}
                        </a>
                    </div>
                </div>
                <div class="mt-1.5 text-xs text-gray-500 dark:text-gray-200">
                    <div>
                        <a target="_blank" href={{route("channel", ["user"=> $v->streamer->username]) }}>
                            {{ '@' . $v->streamer->username}}
                        </a>
                    </div>
                    <div class="mt-1">
                        {{$v->views === 1
                        ? __("1 view")
                        : __(":viewsCount views", ["viewsCount" => $v->views])}}
                    </div>
                </div>
            </div>
            <div class="text-right p-5 w-10">
                <a href="/admin/videos/edit/{{ $v->id }}"><i class="fa-solid fa-pencil mr-2 text-teal-600"></i></a>
                <a href="/admin/videos?remove={{ $v->id }}"
                    onclick="return confirm('{{ __('Are you sure you want to remove this VIDEO?')  }}')"><i
                        class="fa-solid fa-trash text-red-400"></i></a>
            </div>
        </div>

    </div>
    @endforeach
</div> -->
<!-- </div> -->

<!-- <div class="p-5">
    {{-- {{ $videos->links() }} --}}
</div> -->

<!-- @else
<div class="bg-white p-3 rounded">
    @if(request()->filled('search'))
    {{ __('No videos matching ":searchTerm"', ['searchTerm' => request('search')]) }}
    <a href="/admin/videos" class="text-cyan-600 hover:underline">{{ __("Reset Search") }}</a>
    @else
    {{ __('No videos uploaded by streamers.') }}
    @endif
</div>
@endif -->
@endsection

@section('extra_bottom')
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection

@push('adminExtraJS')
<!-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
{{-- attention, dynamic because only needed on this page to save resources --}}
<script>
    $('.dataTable').dataTable({ordering:false});
</script> -->
@endpush