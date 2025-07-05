@extends('admin.base')

@push('adminExtraJS')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '.textarea',
            plugins: 'image code link lists',
            images_upload_url: '/admin/cms/upload-image',
            toolbar: 'code | formatselect fontsizeselect | insertfile a11ycheck | numlist bullist | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive',
            promotion: false,
            editable_root: false,
        });
    </script>
@endpush

@section('section_title')
    <div class="d-flex flex-column">
        {{ __('message.update_subs_plan') }}
    </div>
@endsection

@section('section_body')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3">
            <div class="card-title flex-grow-1 d-flex align-items-center justify-content-between">
                <h4>@yield('section_title', '')</h4>
                <a href="/admin/subscription-plans" class="d-inline-flex align-items-center btn btn-sm btn-primary"> {{-- &raquo; --}} &laquo; {{ __('message.back') }}</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="/admin/update-subscription-plans/{{ $tokenPack->id }}">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('message.plan_name') }}</label>
                            <input id="subscription_name" class="form-control" type="text" value="{{ $tokenPack->subscription_name }}" name="subscription_name" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __("Subscription Level") }}</label>
                            <select name="subscription_level" id="subscription_level" class="form-select" required>
                                <option value="1" {{ ($tokenPack->subscription_level ?? 1) == 1 ? 'selected' : '' }}>{{ __("Level 1 - Free (Public content only)") }}</option>
                                <option value="2" {{ ($tokenPack->subscription_level ?? 1) == 2 ? 'selected' : '' }}>{{ __("Level 2 - Premium (Proposals, private rooms, media gallery)") }}</option>
                                <option value="3" {{ ($tokenPack->subscription_level ?? 1) == 3 ? 'selected' : '' }}>{{ __("Level 3 - Boosted (Profile highlighting, search priority)") }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.days') }}</label>
                            <select name="days" id="days" class="form-select">
                                <option value="30" <?php echo $tokenPack->days == 30 ? 'selected' : ''; ?>>1 Month</option>
                                <option value="182" <?php echo $tokenPack->days == 182 ? 'selected' : ''; ?>>6 Month</option>
                                <option value="365" <?php echo $tokenPack->days == 365 ? 'selected' : ''; ?>>1 Year</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.price') . ' - ' . opt('payment-settings.currency_symbol') }}</label>
                            <input id="subscription_price" class="form-control" type="number" value="{{ $tokenPack->subscription_price }}" name="subscription_price" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('message.details') }}</label>
                            <textarea name="details" id="details" class="form-control textarea" rows="20">{{ $tokenPack->details }}</textarea>
                        </div>
                        <div class="form-group iq-button">
                            <button class="btn btn-sm text-uppercase">{{ __('message.update_plan') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
