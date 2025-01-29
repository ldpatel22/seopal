<!DOCTYPE html>
<html>
<head>
    <title>{{ $report->name() }}</title>
        {!! inject_css([
            'fomantic/semantic.min',
        ]) !!}
    <style>

        @page { size: a4 landscape; }
        /** Set the margins of the page to 0, so the footer and the header can be of the full height and width ! **/
        @page { margin: 1cm 1cm; }

        /** Define now the real margins of every page in the PDF **/
        /*body {*/
        /*    margin-top: 1cm;*/
        /*    margin-left: 1cm;*/
        /*    margin-right: 1cm;*/
        /*    margin-bottom: 1cm;*/
        /*}*/

        /*header {*/
        /*    position: fixed;*/
        /*    top: 0cm;*/
        /*    left: 0cm;*/
        /*    right: 0cm;*/
        /*    height: 1cm;*/

        /*    !** Extra personal styles **!*/
        /*    background-color: rgb(27, 28, 29);*/
        /*    color: white;*/
        /*    text-align: left;*/
        /*    line-height: 1cm;*/
        /*}*/

        /*footer {*/
        /*    position: fixed;*/
        /*    bottom: 0cm;*/
        /*    left: 0cm;*/
        /*    right: 0cm;*/
        /*    height: 1cm;*/

        /*    !** Extra personal styles **!*/
        /*    background-color: rgb(27, 28, 29);*/
        /*    color: white;*/
        /*    text-align: center;*/
        /*    line-height: 1cm;*/
        /*}*/
    </style>
</head>
<body>

{{--<!-- Define header and footer blocks before your content -->--}}
{{--<header>--}}
{{--    <img class="logo" src="{{ asset('img/logo-white.png') }}" style="width: 75px;">--}}
{{--    {{ $report->name() }}--}}
{{--</header>--}}

{{--<footer>--}}
{{--    {{ $report->permalink() }}--}}
{{--</footer>--}}

<!-- Wrap the content of your PDF inside a main tag -->
<main>

    <h1>Report Summary</h1>
    <div class="ui borderless segment full-screen-report-hidden">
        <div class="ui four column stackable grid">
            <div class="column">
                <div>
                    <strong>Report Type</strong>
                </div>
                <p>
                    <span class="ui label">Organic Search Results</span>
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Focus Keyword</strong>
                </div>
                <p>
                    <a href="{{ $report->keyword->permalink() }}">{{ $report->keyword->name }}</a>
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Locale</strong>
                </div>
                <p>
                    @include('shared._locale',['locale' => $report->locale, 'flagOnly' => false])
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Device</strong>
                </div>
                <p>
                    @include('shared._device',['device' => $report->device, 'iconOnly' => false])
                </p>
            </div>
        </div>
        <div class="ui four column stackable grid">
            <div class="column">
                <div>
                    <strong>Started On</strong>
                </div>
                <p>
                    {{ $report->created_at }}
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Started By</strong>
                </div>
                <p>
                    <img class="ui avatar image" src="{{ $report->user->getAvatar() }}" style="width: 20px; height: 20px;" />
                    {{ $report->user->name }}
                </p>
            </div>
        </div>
    </div>

    @foreach($sections as $section => $data)
        <div style="page-break-after: always;"></div>
        @include('reports.' . $section, $data)
    @endforeach

</main>

</body>
</html>
