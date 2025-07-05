@extends('admin.base')

@section('section_title')
<strong>{{ __('Tag Pixels Ads List') }}</strong>

<br />
<a href="{{route('admin.tag-pixels.create')}}" class="text-indigo-700 font-semibold hover:underline">
    {{ __('+Tag Pixels Ads') }}
</a>

@endsection

@section('section_body')

<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
        <div class="card-title d-flex w-100">
            <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
        </div>
    </div>
    <div class="card-body">
        @if (count($tagPixel))
        <table id="commentTable" class="text-stone-600 table border-collapse w-full" data-toggle="data-table">
            <thead>
                <tr>
                    <x-th>{{ __('ID') }}</x-th>
                    <x-th>{{ __('Type') }}</x-th>
                    <x-th>{{ __('code') }}</x-th>
                    <x-th>{{ __('Actions') }}</x-th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tagPixel as $s)
                <tr>
                    <x-td>
                        <x-slot name="field">{{ __('ID') }}</x-slot>
                        {{ $s->id }}
                    </x-td>
                    <x-td>
                        <x-slot name="field">{{ __('Type') }}</x-slot>
                        {{ $s->type ?? '' }}
                    </x-td>
                    <x-td>
                        <x-slot name="field" >{{ __('Code') }}</x-slot>
                        {{ $s->code ?? '' }}
                    </x-td>
                    <x-td>
                        <x-slot name="field">{{ __('Actions') }}</x-slot>
                        <a href="{{route('admin.tag-pixels.edit',$s->id)  }}" class="btn btn-sm btn-icon btn-success rounded"><i class="fa-solid fa-pencil"></i></a>
                        <a href="{{route('admin.tag-pixels.delete',$s->id)  }}"
                            onclick="return confirm('{{ __('Are you sure you want to remove this pack?')  }}')" class="btn btn-sm btn-icon btn-danger bg-danger border-0 delete-btn rounded text-red-400"><i class="fa-solid fa-trash"></i></a>
                    </x-td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="bg-white p-3 rounded">{{ __('No token packs created.') }}</div>
        @endif
    </div>
</div>
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


{{-- attention, dynamic because only needed on this page to save resources --}}
@push('adminExtraJS')
<!-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
<script>
    $('.dataTable').dataTable({ordering:false});
</script> -->
@endpush