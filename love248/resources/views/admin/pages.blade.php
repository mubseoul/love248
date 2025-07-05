@extends('admin.base')

@push('adminExtraJS')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '.textarea',
            plugins: 'image code link lists',
            images_upload_url: '/admin/cms/upload-image',
            toolbar: 'code | formatselect fontsizeselect | insertfile a11ycheck | numlist bullist | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive',
            promotion: false
        });
    </script>
@endpush

@section('section_title')
    <strong>{{ __('message.pages_manager')  }}</strong>
@endsection

@section('section_body')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                <div class="card-title d-flex w-100">
                    <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-view table-responsive table-space">
                    <table id="commentTable" class="table border-collapse w-full bg-white text-stone-600" data-toggle="data-table">
                        <thead>
                            <tr>
                                <x-th>{{ __('ID')  }}</x-th>
                                <x-th>{{ __('message.title')  }}</x-th>
                                <x-th>{{ __('message.updated_at')  }}</x-th>
                                <x-th>{{ __('message.actions')  }}</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $p)
                                <tr>
                                    <x-td>
                                        <x-slot name="field">{{ __('ID')  }}</x-slot>
                                        {{ $p->id }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Title')  }}</x-slot>
                                        <a href="{{ App\Models\Page::slug($p) }}" target="_blank">{{ $p->page_title }}</a>
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Updated At')  }}</x-slot>
                                        {{ $p->updated_at }}
                                    </x-td>
                                    <x-td>
                                        <x-slot name="field">{{ __('Actions')  }}</x-slot>
                                        <div class="d-flex align-items-center list-user-action">
                                            @can('pages-manger-edit')
                                                <a href="/admin/cms-edit-{{ $p->id }}" class="btn btn-sm btn-icon btn-success rounded"><i class="fa-solid fa-pencil"></i></a>
                                            @endcan
                                            @can('pages-manger-delete')
                                                <a href="/admin/cms-delete/{{ $p->id }}" onclick="return confirm('{{ __('Are you sure?')  }}')" class="btn btn-sm btn-icon btn-primary bg-primary border-0 delete-btn rounded"><i class="fa-solid fa-trash"></i></a>  
                                            @endcan
                                        </div>
                                    </x-td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_bottom')
    @if (count($errors) > 0)
        <div class="bg-rose-500 text-white font-semibold p-3 rounded border-2 border-rose-200">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @can('pages-manger-create')
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title text-capitalize">{{ __('message.create_page')  }}</h4>
                </div>
            </div>
            <div class="card-body">
                <form method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="form-label">{{ __('message.page_title')  }}</label>
                        <input type="text" name="page_title" class="form-control" required="required" value="{{ old('page_title') }}" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.page_content')  }}</label>
                        <x-textarea name="page_content" class="w-full textarea" rows="20">
                            {{ old('page_content') }}
                        </x-textarea>
                    </div>
                    <div class="form-group iq-button">
                        <button type="submit" class="btn btn-sm text-uppercase">{{ __('message.save')  }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
    <!-- <div class="box-footer"></div> -->
</div>
@endsection