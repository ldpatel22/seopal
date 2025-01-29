@extends('layouts.html')

@section('head-end')

    <style>

        #content {
            position: fixed;
            top: 70px;
            left: 0px;
            right: 0;
            bottom: 0;
            padding: 10px;
            overflow: scroll;
        }

        body.hidden-sidebar #sidebar { display: none !important; }
        body.hidden-sidebar #sidebar-toggle { display: block; }
        body.hidden-sidebar #content { left: 10px; }

        body.full-screen-report #header { display: none !important; }
        body.full-screen-report #sidebar { display: none !important; }
        body.full-screen-report #content { top: 0 !important; left: 0 !important; }
        body.full-screen-report.full-screen-report .full-screen-report-hidden { display: none !important; }
        #full-screen-header { display: none; height: 54px; }
        body.full-screen-report #full-screen-header { display: flex; }
        body.full-screen-report #tabNavigation { margin-top: 54px; }
        /*body.full-screen-report { background-color: black !important; }*/

        @media only screen and (min-width: 992px) {
            #header .item.mobile,
            #header .item.tablet,
            #header .menu.mobile,
            #header .menu.tablet {
                display: none !important;
            }
            #header .item.computer{
                display: block;
            }
            #header .menu.computer {
                display: flex;
            }
        }

        @media only screen and (max-width: 991px) {
            #header .item.mobile,
            #header .item.tablet,
            #header .menu.mobile,
            #header .menu.tablet {
                display: block;
            }
            #header .item.computer,
            #header .menu.computer {
                display: none !important;
            }
            .icon.button.right.floated span { display: none; }
        }

        .inverted.menu .active.item {
            background-color: #30475E !important;
        }

        .title.segment.with-attachment .header { margin-bottom: 0; }
        .title.segment.with-attachment .attached { display: none; margin-top: 14px; }

        #userAvatar { cursor: pointer }

        #mobileOverlay { bottom: 0; }
        #mobileMenuContent { width: 100%; height: 100%; text-align: center; padding: 25px; }
        #mobileMenuContent .menu { display: inline-block; }

    </style>

@endsection

@section('body')

    @yield('pre-header')

    <div id="header" class="ui fixed inverted secondary menu">
        <a href="{{ route('home') }}" class="header item">
            <img class="logo" src="{{ asset('img/logo-white.png') }}" style="width: 75px;">
        </a>
        <div class="item computer">
            <div class="ui form" style="width: 250px;">
                <div class="field">
                    <select id="select_focusedProject" class="ui search selection dropdown">
                        <option value="{{ route('projects.all') }}" @if(Route::is('projects.all')) selected @endif>ALL PROJECTS</option>
                        <optgroup label="My Projects">
                            @foreach(user()->ownedProjects->where('deleted',false)->sortBy('name') as $project)
                                <option value="{{ $project->permalink() }}" @if(!Route::is('projects.all') && project() && $project->id == project()->id) selected @endif>{{ $project->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="External Projects">
                            @foreach(user()->externalProjects->where('deleted',false)->sortBy('name') as $project)
                                <option value="{{ $project->permalink() }}" @if(!Route::is('projects.all') && project() && $project->id == project()->id) selected @endif>{{ $project->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>
        </div>
        @if(!Route::is('projects.all') && project())
            <a class="@if(Route::is('projects.single')) active @endif item computer" href="{{ project()->permalink() }}">
                Summary
            </a>
            <a class="@if(Route::is('keywords' . '*')) active @endif item computer" href="{{ route('keywords.all') }}">
                Keywords
            </a>
            <a class="@if(Route::is('reports' . '*')) active @endif item computer" href="{{ route('reports.all') }}">
                Reports
            </a>
        @else
            <a class="disabled item computer">Summary</a>
            <a class="disabled item computer">Keywords</a>
            <a class="disabled item computer">Reports</a>
        @endif

        <div class="right menu computer">
            <a id="userAvatar" class="item">
                <img class="ui avatar image" src="{{ user()->getAvatar() }}" />
                <span>{{ user()->name }}</span>
            </a>
            <div class="ui inverted popup">
                <div class="ui inverted vertical menu">
                    <a class="@if(Route::is('user.profile')) active @endif item" href="{{ route('user.profile') }}">
                        <i class="user icon"></i>
                        My Profile
                    </a>
                    <a class="@if(Route::is('subscription.index')) active @endif item" href="{{ route('subscription.index') }}">
                        <i class="certificate icon"></i>
                        Subscription
                    </a>
                    @if(user()->isAdmin())
                        <a class="@if(Route::is('user.all')) active @endif item" href="{{ route('user.all') }}">
                            <i class="users icon"></i>
                            Users
                        </a>
                    @endif
                    <a class="item" href="{{ route('auth.logout') }}">
                        <i class="unlink icon"></i>
                        Sign Out
                    </a>
                </div>
            </div>
        </div>
        <div class="right item mobile tablet" style="padding-left: 25px; padding-top: 15px">
            <i id="mobileMenu" class="large bars link icon"></i>
            <div class="ui inverted fluid popup">
                <div class="ui inverted menu">
                    <a class="item" href="{{ route('user.profile') }}">
                        <i class="user icon"></i>
                        My Profile
                    </a>
                    <a class="item" href="{{ route('auth.logout') }}">
                        <i class="unlink icon"></i>
                        Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="content">
        @yield('content')
    </div>

    {{-- TODO--}}
    <div id="mobileOverlay" class="ui inverted overlay fullscreen modal front scrolling transition hidden">
        <div id="mobileMenuContent">
            <a class="ui right floated compact icon close button">
                <i class="close icon"></i>
            </a>
            <p>
                <img class="logo" src="{{ asset('img/logo-white.png') }}" style="width: 75px;">
            </p>
            <div class="ui inverted vertical menu">
                <a class="@if(Route::is('projects.all')) active @endif item" href="{{ route('projects.all') }}">
                    All Projects
                </a>
            </div>
            <br />
            @if(project())
                <h4 class="ui horizontal divider header">
                    <span class="ui label">{{ project()->name }}</span>
                </h4>
                <div class="ui inverted vertical menu">
                    <a class="@if(Route::is('projects.single')) active @endif item" href="{{ project()->permalink() }}">
                        Summary
                    </a>
                    <a class="@if(Route::is('keywords' . '*')) active @endif item" href="{{ route('keywords.all') }}">
                        Keywords
                    </a>
                    <a class="@if(Route::is('reports' . '*')) active @endif item" href="{{ route('reports.all') }}">
                        Reports
                    </a>
                </div>
            @endif
            <h4 class="ui horizontal divider header">
                <span class="ui label">
                    <img class="ui avatar image" src="{{ user()->getAvatar() }}" />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span>
            </h4>
            <div class="ui inverted vertical menu">
                <a class="@if(Route::is('user.profile')) active @endif item" href="{{ route('user.profile') }}">
                    <i class="user icon"></i>
                    My Profile
                </a>
                <a class="@if(Route::is('subscription.index')) active @endif item" href="{{ route('subscription.index') }}">
                    <i class="certificate icon"></i>
                    Subscription
                </a>
                @if(user()->isAdmin())
                    <a class="@if(Route::is('user.all')) active @endif item" href="{{ route('user.all') }}">
                        <i class="users icon"></i>
                        Users
                    </a>
                @endif
                <a class="item" href="{{ route('auth.logout') }}">
                    <i class="unlink icon"></i>
                    Sign Out
                </a>
            </div>
        </div>
    </div>

    @yield('post-content')

@endsection

@section('body-scripts')

    <script>
        $(()=>{
            // mobile menu
            let $mobileMenu = $('#mobileMenu');
            let $mobileOverlay = $('#mobileOverlay');
            $mobileMenu.on('click', () => { $mobileOverlay.modal('show'); });
            $mobileOverlay.find('.close.button').on('click', () => { $mobileOverlay.modal('hide'); });

            // user menu
            $('#userAvatar').popup({
                on: 'click',
                inline: true,
                hoverable: false,
                position: 'bottom left'
            });

            // project switch
            $('#select_focusedProject').on('change',function (){
                window.location = $(this).val();
            });

        });
    </script>

@endsection
