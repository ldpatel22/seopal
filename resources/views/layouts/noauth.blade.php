@extends('layouts.html')

@section('head-end')

    <style>
        body {
            background-color: #E8E8E8;
        }
        #container {
            height: 80%;
        }
    </style>

@endsection

@section('body')

    <div id="container" class="ui middle aligned center aligned grid">
        <div class="column" style="max-width: 500px">
            <h2 class="ui header">
                <div class="content">
                    <p><img src="{{ asset('img/logo.png') }}" style="height:50px" /></p>
                    <p>@yield('title')</p>
                </div>
            </h2>
            @yield('content')
        </div>
    </div>

@endsection
