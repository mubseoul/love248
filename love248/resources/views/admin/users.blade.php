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
            height: 200
        });
    </script>
@endpush
@section('section_title')
<strong>{{ __($active==='streamers'?__('message.streamers_management'):__('message.users_management'), ['type' => ucfirst($active)]) }}</strong>
@endsection


@section('section_body')
<form method="POST" action="{{ route('admin.whatsapp.messages') }}">
    {{ csrf_field() }}
    <div class="card p-3">
        @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger mb-3">{{ $error }}</div>
            @endforeach
        @endif
        <input type="hidden" name="type" value="email"/>
        {{-- <label class="mb-3"><input type="checkbox" id="select-all"> {{__('message.select_all_users_whatsapp')}}</label> --}}
        <label class="mb-3"><input type="checkbox" id="select-all2"> {{__('message.select_all_users_email')}}</label>
        <div class="form-group mb-3 iq-button">
            <input type="text" name="subject" class="form-control" placeholder="Please enter subject"/>
        </div>
        <div class="form-group mb-0 iq-button">
            <textarea name="message" placeholder="{{__('message.send_message')}}" class="form-control textarea" rows="20"></textarea>
            {{-- <input type="text" name="message"   class="form-control me-2" /> --}}
            <button class="btn btn-sm text-uppercase mt-3">{{ __('message.send_message') }}</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3 flex-grow-1">
                    <div class="card-title d-flex w-100 iq-button">
                        <h4 class="d-flex flex-grow-1 align-items-center justify-content-between m-0">@yield('section_title', '')</h4>
                        <a href="{{route("admin.exportCSV")}}" class="btn btn-primary btn-sm text-capitalize">{{__('message.export_csv')}}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-view table-responsive table-space">
                        <table id="commentTable" class="data-tables table custom-table movie_table" data-toggle="data-table">
                            <thead>
                                <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('message.thumb') }}</th>
                                {{-- <th>{{ __('message.select_whatsapp') }} </th> --}}
                                <th>{{ __('message.username') }}</th>
                                <th>{{ __('message.name') }}</th>
                                <th>{{ __('Email') }} && {{ __('message.send_email') }}</th>
                                <th>{{ __('Tokens') }}</th>
                                @if($active == 'streamers')
                                <th>{{ __('message.is_verified') }}</th>
                                @endif
                                {{-- <th>{{ __('message.is_admin') }}</th> --}}
                                <th>{{ __('Is SubAdmin') }}</th>
                                <th>{{ __('message.ip_address') }}</th>
                                <th>{{ __('message.join_date') }}</th>
                                <th>{{ __('message.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $u)
                                <tr>
                                    <td>
                                        <x-slot name="field">{{ __('ID') }}</x-slot>
                                        {{ $u->id }}
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('Profile') }}</x-slot>
                                        <img src="{{ $u->profile_picture }}" alt="" class="bg-soft-primary rounded img-fluid avatar-40 me-3" />
                                    </td>
                                    {{-- <td>
                                        @if ($u->whatsapp_number!==null)
                                        <label>
                                            <input type="checkbox" name="phone[]" value="{{$u->whatsapp_number}}" class="checkbox-item"/> 
                                            {{$u->whatsapp_number}}
                                        </label>
                                        @else
                                        <span class="mt-2 badge border border-primary text-primary mt-2">
                                            {{__('message.no_whatsapp')}}
                                        </span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        <x-slot name="field">{{ __('Username') }}</x-slot>
                                        @if($u->is_streamer == 'yes')
                                        <a href="{{ route('channel', ['user' => $u->username]) }}" target="_blank"
                                            class="text-primary fw-bold hover:underline">
                                            {{ '@' . $u->username }}
                                        </a>
                                        @else
                                        {{ $u->username }}
                                        @endif
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('Name') }}</x-slot>
                                        {{ $u->name }}
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('Email') }}</x-slot>
                                        <input type="checkbox" name="email[]" value="{{$u->email}}" class="checkbox-item2"/>
                                        {{ $u->email }}
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('Tokens') }}</x-slot>
                                        <span class="mt-2 badge border border-primary text-primary fw-bold mt-2">
                                            {{ $u->tokens }}
                                        </span>
    
                                        <a href="/admin/user/{{ $u->id }}/add-tokens"
                                            class=" hover:underline text-xs block mt-2 text-primary fw-bold">{{ __("Adjust") }}</a>
                                    </td>
                                    @if($active == 'streamers')
                                    <td>
                                        <x-slot name="field">{{ __('Is Admin') }}</x-slot>
                                        @if($u->is_streamer_verified == 'yes')
                                        <span class="mt-2 badge border border-success text-success mt-2">
                                            {{ __('Yes') }}
                                        </span>
                                        @else
                                        <span class="mt-2 badge border border-primary text-primary mt-2">{{ __('No') }}</span>
                                        <br />
                                        <a href="/admin/approve-streamer?user={{ $u->id }}" class="text-xs text-primary fw-bold">{{ __("Mark as Verified") }}</a>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <x-slot name="field">{{ __('Is Admin') }}</x-slot>
                                        <span
                                            class="mt-2 badge border mt-2 {{ $u->hasRole('subadmin') ? 'border-success text-success' : 'border-primary text-primary' }}">
                                            {{$u->hasRole('subadmin') ?'yes':'no'}}
                                        </span>
                                        {{-- <span
                                            class="mt-2 badge border mt-2 {{ $u->is_admin == 'yes' ? 'border-success text-success' : 'border-primary text-primary' }}">
                                            {{ ucfirst($u->is_admin) }}
                                        </span> --}}
                                        <br>
                                        {{-- @if ($u->is_admin == 'yes')
                                        <a href="/admin/users/unsetadmin/{{ $u->id }}" class="text-success fw-bold text-xs hover:underline">{{ __('Unset Admin Role') }}</a>
                                        @elseif($u->is_admin == 'no')
                                        <a href="/admin/users/setadmin/{{ $u->id }}" class="text-primary fw-bold text-xs hover:underline">{{ __('Set Admin Role') }}</a>
                                        @endif --}}
                                        @if ($u->hasRole('subadmin'))
                                        <a href="/admin/users/unsetsubadmin/{{ $u->id }}" class="text-success fw-bold text-xs hover:underline">{{ __('Unset SubAdmin Role') }}</a>
                                    @elseif(!$u->hasRole('subadmin'))
                                        <a href="/admin/users/setsubadmin/{{ $u->id }}" class="text-primary fw-bold text-xs hover:underline">{{ __('Set SubAdmin Role') }}</a>
                                        @endif
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('IP') }}</x-slot>
                                        <span class="mt-2 badge border border-secondary text-secondary mt-2">
                                            {{ $u->ip ? $u->ip : __('N/A') }}
                                        </span>
                                        <br>
                                        @if (!$u->isBanned)
                                        <a href=" /admin/users/ban/{{ $u->id }}" class=" hover:underline fw-bold">
                                                {{ __('Ban') }}
                                            </a>
                                            @else
                                            <a href="/admin/users/unban/{{ $u->id }}" class="text-red-400 hover:underline">
                                                {{ __('Unban') }}
                                            </a>
                                            @endif
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('Join Date') }}</x-slot>
                                        {{ $u->created_at->format('jS F Y') }}
                                    </td>
                                    <td>
                                        <x-slot name="field">{{ __('Actions') }}</x-slot>
                                        <!-- <a href="/admin/loginAs/{{ $u->id }}"
                                            onclick="return confirm('{{ __('This will log you out as an admin and login as a vendor. Continue?')  }}')"
                                            class="text-teal-600 hover:underline">{{ __('Login as User') }}</a> -->
    
                                        <!-- <br>
                                        <br> -->
                                        <!-- <a href="/admin/users?remove={{ $u->id }}"
                                            onclick="return confirm('Are you sure you want to delete this user and his data? This is irreversible!!!')"
                                            class="text-red-400 hover:underline">{{ __('Delete User & His Data') }}</a> -->
                                            <div class="d-flex align-items-center list-user-action">
                                                {{-- @if($active == 'users') --}}
                                                <a class="btn btn-sm btn-icon btn-success bg-success border-0 delete-btn rounded text-red-400" data-bs-toggle="tooltip" data-bs-placement="top" title="View Transaction" href="/admin/user-transactions/{{ $u->id }}">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                {{-- @endif --}}
                                                <a class="btn btn-sm btn-icon btn-success rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Login as User" href="/admin/loginAs/{{ $u->id }}"
                                                onclick="return confirm('This will log you out as an admin and login as a vendor. Continue?')">
                                                    <i class="fa fa-sign-in"></i>
                                                </a>  
                                                <a onclick="return confirm('Are you sure you want to delete this user and his data? This is irreversible!!!')" class="btn btn-sm btn-icon btn-primary bg-primary border-0 delete-btn rounded text-red-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete User & His Data'" href="/admin/users?remove={{ $u->id }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                                <a href="/admin/user-pdf/{{$u->id}}" class="btn btn-sm btn-icon btn-success bg-success border-0 delete-btn rounded text-red-400 text-white" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Exported PDF">
                                                    <i class="fa-solid fa-envelope"></i>
                                                </a>
                                                
                                            </div>
    
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- <table class=" table  dataTable  bg-white">
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Thumb') }}</th>
            <th>{{ __('Username') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Tokens') }}</th>
            @if($active == 'streamers')
            <th>{{ __('Is Verified') }}</th>
            @endif
            <th>{{ __('Is Admin') }}</th>
            <th>{{ __('IP Address') }}</th>
            <th>{{ __('Join Date') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $u)
        <tr>
            <td>
                <x-slot name="field">{{ __('ID') }}</x-slot>
                {{ $u->id }}
            </td>
            <td>
                <x-slot name="field">{{ __('Profile') }}</x-slot>
                <img src="{{ $u->profile_picture }}" alt="" class="w-16 h-16 rounded-full" />
            </td>
            <td>
                <x-slot name="field">{{ __('Username') }}</x-slot>
                @if($u->is_streamer == 'yes')
                <a href="{{ route('channel', ['user' => $u->username]) }}" target="_blank"
                    class="text-cyan-600 hover:underline">
                    {{ '@' . $u->username }}
                </a>
                @else
                {{ $u->username }}
                @endif
            </td>
            <td>
                <x-slot name="field">{{ __('Name') }}</x-slot>
                {{ $u->name }}
            </td>
            <td>
                <x-slot name="field">{{ __('Email') }}</x-slot>
                {{ $u->email }}
            </td>
            <td>
                <x-slot name="field">{{ __('Tokens') }}</x-slot>
                <span class="inline-flex px-2 py-1 rounded-lg text-white bg-cyan-500">
                    {{ $u->tokens }}
                </span>

                <a href="/admin/user/{{ $u->id }}/add-tokens"
                    class=" hover:underline text-xs block mt-2" style="color: #08b1ba">{{
                    __("Adjust")
                    }}</a>
            </td>
            @if($active == 'streamers')
            <td>
                <x-slot name="field">{{ __('Is Admin') }}</x-slot>
                @if($u->is_streamer_verified == 'yes')
                <span class="px-2 py-1 inline-flex rounded-lg text-white bg-emerald-500">
                    {{ __('Yes') }}
                </span>
                @else
                <span class="px-2 py-1 inline-flex rounded-lg text-white bg-amber-400">
                    {{ __('No') }}
                </span>
                <br />
                <a href="/admin/approve-streamer?user={{ $u->id }}" class="text-xs">{{ __("Mark as Verified") }}</a>
                @endif
            </td>
            @endif
            <td>
                <x-slot name="field">{{ __('Is Admin') }}</x-slot>
                <span
                    class="inline-flex px-2 py-1 rounded-lg text-white {{ $u->is_admin == 'yes' ? 'bg-teal-600' : 'bg-stone-500' }}">
                    {{ ucfirst($u->is_admin) }}
                </span>
                <br>
                @if ($u->is_admin == 'yes')
                <a href="/admin/users/unsetadmin/{{ $u->id }}" class="text-red-400 text-xs hover:underline">{{ __('Unset
                    Admin
                    Role') }}</a>
                @elseif($u->is_admin == 'no')
                <a href="/admin/users/setadmin/{{ $u->id }}" class="text-teal-600 text-xs hover:underline">{{ __('Set
                    Admin
                    Role') }}</a>
                @endif
            </td>
            <td>
                <x-slot name="field">{{ __('IP') }}</x-slot>
                <span class="inline-flex px-2 py-1 bg-slate-100 text-slate-500 rounded-lg">
                    {{ $u->ip ? $u->ip : __('N/A') }}

                    {{-- {{ $u->ip ? '<a href="/admin/users/ban/'.$u->id.'>Ban IP</a>' : '' }} --}}
                </span>
                <br>
                @if (!$u->isBanned)
                <a href=" /admin/users/ban/{{ $u->id }}" class=" hover:underline">
                        {{ __('Ban') }}
                    </a>
                    @else
                    <a href="/admin/users/unban/{{ $u->id }}" class="text-red-400 hover:underline">
                        {{ __('Unban') }}
                    </a>
                    @endif
            </td>
            <td>
                <x-slot name="field">{{ __('Join Date') }}</x-slot>
                {{ $u->created_at->format('jS F Y') }}
            </td>
            <td>
                <x-slot name="field">{{ __('Actions') }}</x-slot>
                <a href="/admin/loginAs/{{ $u->id }}"
                    onclick="return confirm('{{ __('This will log you out as an admin and login as a vendor. Continue?')  }}')"
                    class="text-teal-600 hover:underline">{{ __('Login as User') }}</a>

                <br>
                <br>
                <a href="/admin/users?remove={{ $u->id }}"
                    onclick="return confirm('Are you sure you want to delete this user and his data? This is irreversible!!!')"
                    class="text-red-400 hover:underline">{{ __('Delete User & His Data') }}</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table> -->
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
    $(document).ready(function() {
        $('.dataTable').dataTable({ordering:false});
    });
</script> -->
<script>
    $('#select-all').on('change', function() {
        $('.checkbox-item').prop('checked', $(this).prop('checked'));
    });

    // Optional: Handle individual checkbox changes to update "Select All" status
    $('.checkbox-item').on('change', function() {
        // Check if all checkboxes are checked
        $('#select-all').prop('checked', $('.checkbox-item:checked').length === $('.checkbox-item').length);
    });
    
    $('#select-all2').on('change', function() {
        $('.checkbox-item2').prop('checked', $(this).prop('checked'));
    });

    // Optional: Handle individual checkbox changes to update "Select All" status
    $('.checkbox-item2').on('change', function() {
        // Check if all checkboxes are checked
        $('#select-all2').prop('checked', $('.checkbox-item2:checked').length === $('.checkbox-item2').length);
    });

    $('#select-all').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.checkbox-item').prop('checked', isChecked);

        // Change input value to 'whatsapp' when #select-all is checked
        if (isChecked) {
            $('input[name="type"]').val('email');
        } else {
            $('input[name="type"]').val('');
        }
    });

    $('#select-all2').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.checkbox-item2').prop('checked', isChecked);

        // Change input value to 'email' when #select-all2 is checked
        if (isChecked) {
            $('input[name="type"]').val('email');
        } else {
            $('input[name="type"]').val('');
        }
    });

</script>
@endpush