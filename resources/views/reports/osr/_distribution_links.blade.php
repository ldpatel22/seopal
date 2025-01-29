<a class="ui right labeled mini button distribution-popup">
    <div class="ui mini brown icon button">
        <i class="linkify icon"></i>
    </div>
    <div class="ui mini basic label">{{ $links->count() }}</div>
</a>
<div class="ui special w400 mh300 scrollable popup" style="text-align: left">
    <div class="header">Links</div>
    <hr/>
    <div class="list">
        @foreach($links as $link)
            <div class="item">
                <a href="{{ $link['href'] }}" target="_blank">{{ $link['name'] }}</a>
            </div>
        @endforeach
    </div>
</div>
