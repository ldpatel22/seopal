@extends('layouts.auth')

@section('title')
    My Profile
@endsection

@section('content')

    <div class="ui segment inverted title with-attachment">
        <h1 class="ui header">
            My Profile
            <button id="btn_toggleEditProfile" class="ui labeled icon basic inverted button right floated">
                <i class="edit icon"></i>
                Edit Info
            </button>
            <button id="btn_toggleEditPassword" class="ui labeled icon basic inverted button right floated">
                <i class="lock icon"></i>
                Change Password
            </button>
        </h1>
        <form id="form_EditProfile" class="ui attached form fluid segment">
            <div class="field">
                <div class="two fields">
                    {{-- Name --}}
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Your name here" value="{{ $user->name }}">
                    </div>
                    {{-- Email --}}
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Your email here" value="{{ $user->email }}">
                    </div>
                </div>
            </div>
            <button class="ui primary submit button">Update Profile</button>
            <button class="ui cancel button">Cancel</button>
        </form>
        <form id="form_EditPassword" class="ui attached form fluid segment">
            <div class="field">
                <div class="two fields">
                    {{-- Name --}}
                    <div class="field">
                        <label>New Password</label>
                        <input type="password" name="password" placeholder="Use a strong password">
                    </div>
                    {{-- Email --}}
                    <div class="field">
                        <label>Retype Password</label>
                        <input type="password" name="password_repeat" placeholder="Retype password">
                    </div>
                </div>
                {{-- Name --}}
                <div class="field">
                    <label>Current Password</label>
                    <input type="password" name="password_old" placeholder="Confirm with current password">
                </div>
            </div>
            <button class="ui primary submit button">Change Password</button>
            <button class="ui cancel button">Cancel</button>
        </form>
    </div>
    <div class="ui borderless segment">
        <div class="ui four column stackable grid">
            <div class="column">
                <div>
                    <strong>Name</strong>
                </div>
                <p>{{ $user->name }}</p>
            </div>
            <div class="column">
                <div>
                    <strong>Email</strong>
                </div>
                <p>
                    <a>{{ $user->email }}</a>
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Signed Up</strong>
                </div>
                <p>
                    {{ $user->created_at }}
                </p>
            </div>
        </div>
        <div class="ui four column stackable grid">
            <div class="column">
                <div>
                    <strong>Owned Projects</strong>
                </div>
                <p>
                    <a href="{{ route('projects.all') }}">{{ $user->ownedProjects()->where('deleted',false)->count() }}</a>
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>External Projects</strong>
                </div>
                <p>
                    <a href="{{ route('projects.all') }}">{{ $user->externalProjects()->where('deleted',false)->count() }}</a>
                </p>
            </div>
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
