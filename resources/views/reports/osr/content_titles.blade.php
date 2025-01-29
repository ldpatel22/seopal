<p>
    Here's five <span class="ui label"><i class="android icon"></i> AI Powered</span> 
    SEO optimized landing page titles for the keyword <strong>{{ $keyword }}</strong> inspired by headings of top ranking websites for this keyword:
</p>

<div class="ui relaxed list">
    @foreach($titles as $title)
    <div class="item">
        <i class="heading middle aligned icon"></i>    
        <div class="content">            
            <div class="ui compact message"><p>{{ $title }}</p></div>
        </div>
    </div>
    @endforeach
</div>