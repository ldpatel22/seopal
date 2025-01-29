@extends('layouts.noauth')

@section('title', 'Log in')

@section('content')

    @if(session('success'))
        <div class="ui success message">
            <div class="header">Registration successful!</div>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form id="form_logIn" class="ui large form">
        <div class="ui stacked inverted segment">
            <div class="field">
                <div class="ui left icon input">
                    <i class="user icon"></i>
                    <input style="color: black" type="text" name="email" placeholder="E-mail address">
                </div>
            </div>
            <div class="field">
                <div class="ui left icon input">
                    <i class="lock icon"></i>
                    <input type="password" name="password" placeholder="Password">
                </div>
            </div>
            <div id="btn_attemptLogin" class="ui fluid large primary submit button">Login</div>
        </div>

        <div class="ui error message">There was an error. Please contact admin.</div>

        <div class="ui message field">
            Don’t have an account? <a href="{{ route('auth.register') }}">Sign up here.</a>
        </div>

    </form>

@endsection


@section('body-end')

    {!! inject_js([
        'view/LoginPageControl',
    ]) !!}

    <script>

        $(() => { new LoginPageControl(); });

    </script>

@endsection

<style>
    input {
        color: #464444; /* Replace with your desired color */
    }
</style>