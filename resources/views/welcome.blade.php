@extends('layouts.html')

@section('title')
    TEST
@endsection

@section('body')

    <style>

        #sidebar {
            position: fixed;
            z-index: 1;
            width: 250px;
            -webkit-box-flex: 0;
            -webkit-flex: 0 0 auto;
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            padding: 10px;
        }

        #content {
            webkit-box-flex: 1;
            -webkit-flex: 1 1 auto;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            min-width: 0px;
            margin-left: 230px;
            padding: 10px;
        }

        #main-wrapper {
            margin-top: 10px;
        }

    </style>

    <div id="header" class="ui top fixed">
        <div class="ui menu">
            <div class="item">
                menu
            </div>
            <div class="item">
                <strong>LOGO</strong>
            </div>
            <div class="right item">
                <img class="ui avatar image" src="https://helloputra.github.io/simple-ui/dist/images/user.png" />
                <span>Milos Djekic</span>
            </div>
        </div>
    </div>
    <div id="main-wrapper">
        <div id="sidebar">
            <div class="ui inverted vertical pointing menu">
                <a class="item">
                    My Projects
                </a>
                <a class="item">
                    My Subscription
                </a>
            </div>
            <strong>Cycling</strong>
            <div class="ui inverted vertical pointing menu">
                <a class="active item">
                    Keywords
                </a>
                <a class="item">
                    Reports
                </a>
                <a class="item">
                    Settings
                </a>
            </div>
        </div>

        <div id="content" style="">
            <div class="ui segment">
                <h1 class="ui header">
                    My Projects
                </h1>

                <div class="ui container">
                    <div class="ui relaxed divided list">
                        <div class="item">
                            <i class="large clipboard middle aligned icon"></i>
                            <div class="content">
                                <a class="header">Cycling</a>
                                <div class="description">
                                    Last report: 10h ago
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <i class="large clipboard middle aligned icon"></i>
                            <div class="content">
                                <a class="header">Powerful Keywords</a>
                                <div class="description">Last report: 2d ago</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button class="ui labeled icon button right floated">
                            <i class="plus icon"></i>
                            Add Project
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>



{{--    <div class="ui top fixed inverted menu">--}}
{{--        <div class="item">--}}
{{--            menu--}}
{{--        </div>--}}
{{--        <div class="item">--}}
{{--            <strong>LOGO</strong>--}}
{{--        </div>--}}
{{--        <div class="right item">--}}
{{--            <img src="https://helloputra.github.io/simple-ui/dist/images/user.png" style="width: 20px; height: 20px;" />--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="ui breadcrumb top fixed" style="margin-top: 55px">--}}
{{--        <a class="section">Projects</a>--}}
{{--        <i class="right angle icon divider"></i>--}}
{{--        <a class="section">SEO Course</a>--}}
{{--        <i class="right angle icon divider"></i>--}}
{{--        <div class="active section">Keywords</div>--}}
{{--    </div>--}}

{{--    <div class="ui divider"></div>--}}

{{--    <div class="ui container">--}}
{{--        <div class="ui inverted vertical pointing menu">--}}
{{--            <div class="item">--}}
{{--                <div class="header">SEO Pal</div>--}}
{{--                <div class="menu">--}}
{{--                    <a class="item">My Projects</a>--}}
{{--                    <a class="item">My Subscription</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="item ">--}}
{{--                <div class="header">Project XYZ</div>--}}
{{--                <div class="menu">--}}
{{--                    <a class="item active">Keywords</a>--}}
{{--                    <a class="item">Reports</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <a class="item">--}}
{{--                Messages--}}
{{--            </a>--}}
{{--            <a class="item">--}}
{{--                Friends--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="ui bottom fixed inverted menu">--}}
{{--        <div class="item">--}}
{{--            Terms of Service--}}
{{--        </div>--}}
{{--        <div class="item">--}}
{{--            Privacy Policy--}}
{{--        </div>--}}
{{--        <div class="right item">--}}
{{--            <img src="https://helloputra.github.io/simple-ui/dist/images/user.png" style="width: 20px; height: 20px;" />--}}
{{--        </div>--}}
{{--    </div>--}}

@endsection
