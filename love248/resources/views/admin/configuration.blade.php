@extends('admin.base')

@section('section_title')
    <strong>{{ __('message.general_config') }}</strong>
@endsection

@section('section_body')
    @include('admin.configuration-navi')
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('message.seo_title_tag') }}</label>
                        <input type="text" name="seo_title" value="{{ opt('seo_title') }}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.seo_des_tag') }}</label>
                        <input type="text" name="seo_desc" value="{{ opt('seo_desc') }}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.seo_keyword') }}</label>
                        <input type="text" name="seo_keys" value="{{ opt('seo_keys') }}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.site_logo') }}</label>
                        <input type="file" name="site_logo" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.site_favico') }}</label>
                        <input type="file" name="site_favico" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.require_streamer_label') }}</label>
                        <select class="form-select" name="streamersIdentityRequired">
                            <option value="Yes" @if (opt('streamersIdentityRequired', 'No') == 'Yes') selected @endif>
                                {{ __('Yes') }}
                            </option>
                            <option value="No" @if (opt('streamersIdentityRequired', 'No') == 'No') selected @endif>
                                {{ __('No') }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('message.fb_pixel_code') }}</label>
                        <textarea class="form-control" name="facebook" id="facebook" rows="5">{{ opt('facebook') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.google_tag') }}</label>
                        <textarea class="form-control" name="google" id="google" rows="5">{{ opt('google') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('message.tik_tok_tag') }}</label>
                        <textarea class="form-control" name="tiktok" id="tiktok" rows="5">{{ opt('tiktok') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group iq-button">
                <button type="submit" class="btn btn-sm text-uppercase">{{ __('message.save_settings') }}</button>
            </div>
        </form>
        </div>
    </div>
    <!-- <div class="bg-white rounded p-3 text-stone-600">
        <form method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mt-5 flex md:flex-row flex-col md:space-x-5 space-y-10 md:space-y-0">
                <div class="md:w-2/3 w-full">
                    <dl>
                        <dt class="font-semibold text-stone-600">{{ __('SEO Title Tag') }}</dt>
                        <dd>
                            <x-input type="text" name="seo_title" value="{{ opt('seo_title') }}" class="md:w-2/3 w-full" />
                        </dd>
                        <br>
                        <dt class="font-semibold text-stone-600">{{ __('SEO Description Tag') }}</dt>
                        <dd>
                            <x-input type="text" name="seo_desc" value="{{ opt('seo_desc') }}" class="md:w-2/3 w-full" />
                        </dd>
                        <br>
                        <dt class="font-semibold text-stone-600">{{ __('SEO Keywords') }}</dt>
                        <dd>
                            <x-input type="text" name="seo_keys" value="{{ opt('seo_keys') }}" class="md:w-2/3 w-full" />
                        </dd>
                        <br>
                        <dt class="font-semibold text-stone-600">{{ __('Site Logo (Top Navi) (max 200x80px)') }}</dt>
                        <dd>
                            <x-input type="file" name="site_logo" class="md:w-2/3 w-full" />
                        </dd>

                        <br>
                        <dt class="font-semibold text-stone-600">{{ __('Site Favico') }}
                            <strong>({{ __('must be 128x128px') }})</strong>
                        </dt>
                        <dd>
                            <x-input type="file" name="site_favico" class="md:w-2/3 w-full" />
                        </dd>

                        <label
                            class="mt-5 font-semibold text-stone-600 block">{{ __('Require Streamers to Verify Identity
                                                    ?') }}</label>
                        <x-select name="streamersIdentityRequired" class="md:w-1/4 w-full">
                            <option value="Yes" @if (opt('streamersIdentityRequired', 'No') == 'Yes') selected @endif>
                                {{ __('Yes') }}
                            </option>
                            <option value="No" @if (opt('streamersIdentityRequired', 'No') == 'No') selected @endif>
                                {{ __('No') }}
                            </option>
                        </x-select>
                    </dl>
                </div>
                <div class="md:w-2/3 w-full">
                    <dl>
                        <dt class="font-semibold text-stone-600">{{ __('Facebook Pixel Code') }}</dt>
                        <dd>
                            <x-textarea name="facebook" id="facebook" cols="35" class="md:w-2/3 w-full">{{ opt('facebook') }}</x-textarea>
                        </dd>
                        <br>

                        <dt class="font-semibold text-stone-600">{{ __('Google Tag') }}</dt>
                        <dd>
                            <x-textarea name="google" id="google" cols="35" class="md:w-2/3 w-full">{{ opt('google') }}</x-textarea>
                        </dd>
                        <br>

                        <dt class="font-semibold text-stone-600">{{ __('Tik Tok Tag') }}</dt>
                        <dd>
                            <x-textarea name="tiktok" id="tiktok" cols="35" class="md:w-2/3 w-full">{{ opt('tiktok') }}</x-textarea>
                        </dd>
                        <br>

                    </dl>
                </div>


            </div>

            <div class="flex w-full my-3">
                <x-button>{{ __('Save Settings') }}</x-button>
            </div>
        </form>
    </div> -->
@endsection
