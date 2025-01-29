<!doctype html>
<html class="no-js" lang="sr-Latn">

<head>

{{--    @if(env('APP_ENV') == 'production')--}}
{{--        @include('snippets.gtm-head')--}}
{{--    @endif--}}

    @yield('head-start')

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon ============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">

    <!-- Head CSS -->
    {!! inject_css([
        'fomantic/semantic.min',
        'fomantic/responsive-semantic-ui',
        'datatables.min',
        'app'
    ]) !!}

    <style>
        .ui.table th.blue { background: #ddf4ff !important; color: #2185d0; }
        .ui.table th.yellow { background: #fff9d2 !important; color: #b58105; }
        .inverted { background-color: #222831 !important; }

        .hide { display: none !important; }
        .inline { display: inline !important; }
        .inline-block { display: inline-block !important; }
        .borderless { border: none !important; box-shadow: none !important; }
        .scrollable { overflow-y: scroll; }

        .w100 { width: 100px !important; }
        .w150 { width: 150px !important; }
        .w200 { width: 200px !important; }
        .w250 { width: 250px !important; }
        .w350 { width: 350px !important; }
        .w400 { width: 400px !important; }
        .w450 { width: 450px !important; }
        .w500 { width: 400px !important; }

        .mw250 { max-width: 250px !important; }
        .mw300 { max-width: 300px !important; }
        .mw350 { max-width: 350px !important; }

        .h100 { height: 100px !important; }
        .h150 { height: 150px !important; }
        .h200 { height: 200px !important; }
        .h250 { height: 250px !important; }
        .h300 { height: 300px !important; }
        .h350 { height: 350px !important; }
        .h400 { height: 400px !important; }
        .h450 { height: 450px !important; }
        .h500 { height: 500px !important; }

        .mh100 { max-height: 100px !important; }
        .mh150 { max-height: 150px !important; }
        .mh200 { max-height: 200px !important; }
        .mh250 { max-height: 250px !important; }
        .mh300 { max-height: 300px !important; }

    </style>

    @yield('head-end')
</head>

{{--<body class="full-screen-report">--}}
<body>

@yield('body-start')

@yield('body')

{{--@if(env('APP_ENV') == 'production')--}}
{{--    @include('snippets.gtm-body')--}}
{{--@endif--}}

@include('shared.javascript')

<!-- Body-End JS -->
{!! inject_js([
    'jquery-3.5.1.min',
    'semantic.min',
    'datatables.min',
    'util'
]) !!}

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(() => {
        $('.selection.dropdown').dropdown();
    });
</script>

@yield('body-scripts')

@yield('body-end')

</body>

</html>
