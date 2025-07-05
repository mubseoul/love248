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
    <strong class="text-capitalize">{{ __('message.pages_manager') }} - {{__('message.page_update')}}</strong>
    <br />
    <a href="{{ route('admin-cms') }}">{{ __('message.page_overview')  }}</a>
@endsection

@section('section_body')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
        <div class="card-title d-flex w-100">
            <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('message.page_title')  }}</label>
                        <input type="text" name="page_title" class="form-control" value="{{ $p->page_title }}" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.page_content')  }}</label>
                        <textarea name="page_content" class="form-control textarea" rows="20">{{ clean($p->page_content) }}</textarea>
                    </div>
                    <div class="iq-button">
                        <button class="btn btn-primary btn-sm">{{ __('message.save')  }}</button>
                    </div>
                </div>
            </div>        
        </form>
    </div>
</div>
@endsection
