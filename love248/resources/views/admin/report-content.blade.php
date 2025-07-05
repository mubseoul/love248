@extends('admin.base')

@section('section_title')
    <strong class="text-capitalize">{{ __('message.report_content')  }}</strong>
@endsection
@section('section_body')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                    <div class="card-title d-flex w-100 iq-button">
                        <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="table-view table-responsive table-space">
                        @if (count($contents))
                            <table class="table border-collapse w-full bg-white text-stone-600 dataTable">
                                <thead>
                                    <tr>
                                        <x-th>{{ __('ID')  }}</x-th>
                                        <x-th>{{ __('message.reported_user_mail')  }}</x-th>
                                        <x-th>{{ __('message.reason')  }}</x-th>
                                        <x-th>{{ __('message.report_for_user')  }}</x-th>
                                        <x-th>{{ __('message.user')  }}</x-th>
                                        <x-th>{{ __('message.item_title')  }}</x-th>
                                        <x-th>{{ __('message.item_type')  }}</x-th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contents as $c)
                                        <tr>
                                            <x-td>
                                                <x-slot name="field">{{ __('ID')  }}</x-slot>
                                                {{ $c->id }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Email')  }}</x-slot>
                                                {{ $c->email }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Reason')  }}</x-slot>
                                                {{ $c->reason }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Email')  }}</x-slot>
                                                {{ $c->user?$c->user->email:'' }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('User')  }}</x-slot>
                                                {{ $c->user?$c->user->username:''  }}
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Item Title')  }}</x-slot>
                                                @if ($c->item == 'video' && $c->relatedItem)
                                                <a href="{{ route('video.page', ['video' => $c->relatedItem->id, 'slug' => $c->relatedItem->slug]) }}" target="_blank" class="text-decoration-underline">
                                                    {{$c->relatedItem->title}}
                                                </a>
                                                @elseif ($c->item == 'gallery' && $c->relatedItem)
                                                    <a href="{{ route('gallery.page', ['gallery' => $c->relatedItem->id, 'slug' => $c->relatedItem->slug]) }}" target="_blank" class="text-decoration-underline">
                                                        {{ $c->relatedItem->title }}
                                                    </a>
                                                @endif
                                            </x-td>
                                            <x-td>
                                                <x-slot name="field">{{ __('Item Type')  }}</x-slot>
                                                {{ $c->item }}
                                            </x-td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="bg-white p-3 rounded">{{ __('No report users.')  }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection