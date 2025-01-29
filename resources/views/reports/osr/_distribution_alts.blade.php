<a class="ui right labeled mini button distribution-popup">
    <div class="ui mini yellow icon button">
        <i class="image icon"></i>
    </div>
    <div class="ui mini basic label">{{ $alts->count() }}</div>
</a>
<div class="ui special w400 mh300 scrollable popup" style="text-align: left">
    <div class="header">"Alt" tags</div>
    <hr/>
    <div class="list">
        @foreach($alts as $alt)
            <div class="item">
                <a href="{{ $alt['src'] }}" target="_blank">{{ $alt['alt'] }}</a>
            </div>
        @endforeach
    </div>
</div>
