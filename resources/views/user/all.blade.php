@extends('layouts.auth')

@section('title')
    All Users
@endsection

@section('content')

<div class="ui segment inverted title with-attachment">
    <h1 class="ui header">
        Users Management
        <a href="{{ route('user.new') }}"id="btn_toggleEditProfile" class="ui labeled icon basic inverted button right floated">
                <i class="add icon"></i>
                Add User
        </a>
    </h1>
</div>

<div class="ui segment">
    <table id="table_Keywords" class="ui basic table datatable">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Created at</th>
            <th>Status</th>
            <th class="no-sort" width="1%">&nbsp;</th>
            <th class="no-sort" width="1%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                @php
                    if($user->email == $current_user->email)
                        continue;
                @endphp
                <tr class="@if($user->access_status == 0) red @endif">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ date('d-m-Y H:i:s', strtotime($user->created_at)) }}</td>
                    <td>
                        @switch($user->access_status)
                            @case(0)
                                Blocked
                            @break
                            @case(1)
                                User
                            @break
                            @case(2)
                                <strong>Administrator</strong>
                            @break
                        @endswitch
                    </td>
                    <td class="no-sort">
                        <a href="{{ route('user.single', $user->id) }}" class="ui mini icon basic primary button right floated">
                            Edit
                        </a>
                    </td>
                    <td class="no-sort">
                        <a href="#" data-id="{{$user->id}}" class="delete-user ui mini red icon basic primary button right floated">
                            Delete
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="modal_DeleteUser" class="ui tiny test modal front transition hidden">
        <div class="header">
            Delete User?
        </div>
        <div class="content">
            <p>Please confirm you wish to delete this user.</p>
        </div>
        <div class="actions">
            <div class="ui cancel button">Cancel</div>
            <div class="ui primary button">Confirm Deletion</div>
        </div>
    </div>
@endsection

@section('body-end')

    {!! inject_js([
        'app/ToggleControl',
        'view/UserProfilePageControl'
    ]) !!}

    <script>
        $(() => { new UserProfilePageControl(); });
    </script>

@endsection