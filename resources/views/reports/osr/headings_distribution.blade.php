{{--<table class="ui basic table">--}}
{{--    <thead>--}}
{{--    <tr class="mobile">--}}
{{--        <th style="width: 25%; vertical-align: bottom !important;">Landing Page / Heading</th>--}}
{{--        @foreach(array_keys($headings) as $heading)--}}
{{--            <th><span>{{ $heading }}</span></th>--}}
{{--        @endforeach--}}
{{--    </tr>--}}
{{--    <tr class="non-mobile">--}}
{{--        <th>Landing Page / Heading</th>--}}
{{--        @foreach(array_keys($headings) as $heading)--}}
{{--            <th class="rotated"><span>{{ $heading }}</span></th>--}}
{{--        @endforeach--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--    <tbody>--}}
{{--    @foreach($landingPages as $landingPageId => $url)--}}
{{--        <tr>--}}
{{--            <td>--}}
{{--                <a href="{{ $url }}">{{ $url }}</a>--}}
{{--            </td>--}}
{{--            @foreach($headings as $heading_id => $pages)--}}
{{--                <td>--}}
{{--                    @if(isset($pages[$landingPageId]))--}}
{{--                        @include('reports.osr._heading_icon',['headingLevel' => $pages[$landingPageId]])--}}
{{--                    @else--}}
{{--                        &nbsp;--}}
{{--                    @endif--}}
{{--                </td>--}}
{{--            @endforeach--}}
{{--        </tr>--}}
{{--    @endforeach--}}
{{--    </tbody>--}}
{{--</table>--}}

<table class="ui basic table">
    <thead>
    <tr style="text-align: center">
        <th>&nbsp;</th>
        <th>Heading / Landing Page</th>
        @foreach($landingPages as $landingPage)
            <th>
                <a class="ui link page-popup">
                    <img src="{{ $landingPage->getFaviconUrl() }}" style="height: 24px" />
                </a>
                <div class="ui special popup" style="text-align: left">
                    <div class="header">{{ $landingPage->domain->name }}</div>
                    <hr/>
                    <a href="{{ $landingPage->url }}" target="_blank">
                        {{ $landingPage->url }}
                    </a>
                </div>
            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($entries as $label => $entry)
        <tr style="text-align: center">
            <td>
                <i class="grey lightbulb icon"></i>
            </td>
            <td style="text-align: left">
                <span>{{ $label }}</span>
            </td>
            @foreach($landingPages as $landingPage)
                @php
                    $headings = array_filter($entry['headings'], function($heading) use($landingPage){ return $landingPage->id == $heading['landing_page_id']; });
                    $links = array_filter($entry['links'], function($link) use($landingPage){ return $landingPage->id == $link['landing_page_id']; });
                    $alts = array_filter($entry['alts'], function($alt) use($landingPage){ return $landingPage->id == $alt['landing_page_id']; });
                @endphp
                <td class="distribution list">
                    @if(count($headings) > 0)
                        <div class="item">@include('reports.osr._distribution_headings', ['headings' => $headings])</div>
                    @endif
                    @if(count($links) > 0)
                        <div class="item">@include('reports.osr._distribution_links', ['links' => $links])</div>
                    @endif
                    @if(count($alts) > 0)
                        <div class="item">@include('reports.osr._distribution_alts', ['alts' => $alts])</div>
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

