<p>
    Here's the <span class="ui label"><i class="android icon"></i> AI Powered</span> SEO optimized landing page content
    for the keyword <strong>{{ $keyword }}</strong>
    driven by the median word count of <span class="blue-text">{{$word_count}}</span>, based on the top ranking landing pages for that keyword:
</p>

<div class="ui relaxed list">
    <div class="item">
        <i class="font middle aligned icon"></i>
        <div class="content">
            <div class="ui message">
                {{-- {!! $content !!} --}}
                {!! Str::markdown($content) !!}
            </div>
        </div>
    </div>
</div>