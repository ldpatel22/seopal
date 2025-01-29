
@extends('layouts.auth')

@section('title')
    Add User
@endsection

@section('content')

<div class="ui segment inverted title with-attachment">
    <h1 class="ui header">
        Users Management
        <a href="{{ route('user.all') }}"id="btn_toggleEditProfile" class="ui labeled icon basic inverted button right floated">
                <i class="arrow left icon"></i>
                Go back
        </a>
    </h1>
</div>

<div class="ui segment">
    <form id="form_AddUser" class="ui attached form fluid segment">
        <div class="field">
            <div class="two fields">
                {{-- Name --}}
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" placeholder="User's name">
                </div>

                {{-- Email --}}
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="User's email">
                </div>

                <br><br>

                {{-- Password --}}
                <div class="field">
                    <label>Password</label>
                    <input type="text" name="password" placeholder="User's email">
                </div>

                {{-- Status --}}
                <div class="field">
                    <label>Status</label>
                    <div class="ui fluid search selection dropdown">
                        <input type="hidden" name="access_status" value="">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select user's status</div>
                        <div class="menu">
                            <div class="item" data-value="0">Blocked</div>
                            <div class="item" data-value="1">User</div>
                            <div class="item" data-value="2">Administrator</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="ui primary submit button">Save</button>
        <a class="ui cancel button" href="{{ route('user.all') }}">Go back</a>
    </form>
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