@extends('layouts.noauth')

@section('title', 'Join SEO Pal now.')

@section('content')

    <form id="signupForm" class="ui large form" method="POST" action="{{ route('auth.register') }}">
        @csrf
        <div class="ui stacked inverted segment">
            @if ($errors->any())
                <div class="ui error message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                <div class="field">
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input type="text" name="name" id="name" placeholder="Full name" value="{{ old('full_name') }}" required>
                    </div>
                    @error('name')
                    <div class="ui pointing red basic label">{{ $message }}</div>
                    @enderror
                </div>

            <div class="field">
                <div class="ui left icon input">
                    <i class="mail icon"></i>
                    <input type="email" name="email" id="email" placeholder="E-mail address" value="{{ old('email') }}" required>
                </div>
                @error('email')
                <div class="ui pointing red basic label">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="field">
                <div class="ui left icon input">
                    <i class="lock icon"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                @error('password')
                <div class="ui pointing red basic label">{{ $message }}</div>
                @enderror
            </div>

            <!-- Repeat Password Field -->
            <div class="field">
                <div class="ui left icon input">
                    <i class="lock icon"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Repeat password" required>
                </div>
                @error('password_confirmation')
                <div class="ui pointing red basic label">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label>Confirm you accept <a href="#">Terms and Conditions</a></label>
                </div>
                @error('terms')
                <div class="ui pointing red basic label">{{ $message }}</div>
                @enderror
            </div>

            <button id="btn_attemptRegister" type="submit" class="ui fluid large primary submit button">Sign Up</button>
        </div>

        <div class="ui message field">
            Already have an account? <a href="{{ route('auth.login') }}">Log In here.</a>
        </div>
    </form>

@endsection

@section('body-end')

    {!! inject_js([
        'view/RegisterPageControl',
    ]) !!}

@endsection

<style>
    input#name::placeholder {
        color: #4b4848;
    }
    input#email::placeholder {
        color: #4b4848;
    }
    input#password::placeholder {
        color: #4b4848;
    }
    input#password_confirmation::placeholder {
        color: #4b4848;
    }
</style>
