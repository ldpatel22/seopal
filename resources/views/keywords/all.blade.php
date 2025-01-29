@extends('layouts.auth')

@section('title')
    {{ __('keywords.pageTitle') }} | {{ project()->name }}
@endsection

@section('content')

    @include('keywords._keywords')

@endsection

@section('body-end')

    {!! inject_js([
        'app/ToggleControl',
        'view/KeywordsDataFormatter',
        'view/KeywordsDataTableControl',
        'view/RelatedKeywordsDataTableControl',
        'view/NewKeywordControl'
    ]) !!}

    <script>
        $(() => {
            new KeywordsDataTableControl();
            new RelatedKeywordsDataTableControl();
            new NewKeywordControl();
        });
    </script>

@endsection
