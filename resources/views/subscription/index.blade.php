@extends('layouts.auth')

@section('title')
    Subscription
@endsection

@section('content')

    {{-- My Projects --}}
    <div class="ui segment inverted title">
        <h1 class="ui header">
            Subscription Information
        </h1>
    </div>
    <div class="ui segment">
        <div class="ui relaxed divided list">
            <h2>
                SEOPAL BETA
            </h2>
            <p>
                You are using a <strong>CLOSED BETA</strong> version of SEO Pal.
            </p>
            <p>
                The service is provided as-is and for <strong>reasonable use</strong>.
            </p>
            <p>
                Feel free to report any issues and provide feedback directly to <a href="mailto:mdjekic+seopal@gmail.com">mdjekic+seopal@gmail.com</a>.
            </p>
            <p>
                Thank you for your business.
            </p>
        </div>
    </div>

@endsection
