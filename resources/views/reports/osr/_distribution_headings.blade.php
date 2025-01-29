<a class="ui right labeled mini button distribution-popup">
    <div class="ui mini blue icon button">
        <i class="grip lines icon"></i>
    </div>
    <div class="ui mini basic label">{{ $headings->count() }}</div>
</a>
<div class="ui special w400 mh300 scrollable popup" style="text-align: left">
    <div class="header">Headings</div>
    <hr/>
    <div class="list">
        @foreach($headings as $heading)
            <div class="item">@include('reports.osr._heading_icon',['headingLevel' => $heading['level']]){{ $heading['name'] }}</div>
        @endforeach
    </div>
</div>
