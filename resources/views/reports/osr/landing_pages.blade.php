@if(!empty($localLinks) ||$localLinks != null)
    <h2>Local Pack</h2>

    <table class="ui basic table datatable irelevant">
    <tbody>
    @foreach($localLinks as $link)
    <tr style="vertical-align: middle">
        <td style="white-space: nowrap;">
            <img class="ui middle aligned image" src="http://www.google.com/s2/favicons?domain={{ $link->name }}" style="width: 24px"/>&nbsp;
            <a href="https://{{ $link->name }}" target="_blank">{{ $link->name }}</a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif

<h2>Search Results</h2>
<table class="ui basic table datatable">
    <thead style="background: whitesmoke">
    <tr>
        <th>
            <span class="info-popup">Pos.<sup>?</sup></span>
            <div class="ui special w250 inverted popup" style="text-align: left">
                <div class="header">Position</div>
                <hr/>
                <div>Landing page position on the first page of Google Search.</div>
            </div>
        </th>
        {{-- Domain--}}
        <th>Domain</th>
        <th class="center aligned">
            <span class="info-popup">Auth. Score<sup>?</sup></span>
            <div class="ui special w250 inverted popup" style="text-align: left">
                <div class="header">Authority Score (Domain)</div>
                <hr/>
                <div>A [0-100] measure of the domain’s overall quality and SEO performance.</div>
            </div>
            <br/><span class="ui small text">(domain)</span>
        </th>
        <th class="center aligned">
            <span class="info-popup">Word Count<sup>?</sup></span>
            <div class="ui special w250 inverted popup" style="text-align: left">
                <div class="header">Word Count (Domain)</div>
                <hr/>
                <div>Number of words on the first page of the domain.</div>
            </div>
            <br/><span class="ui small text">(domain page)</span>
        </th>
        <th class="center aligned">Backlinks<br/><span class="ui small text">(domain)</span></th>
        {{-- Page--}}
        <th>Landing Page</th>
        <th class="center aligned">
            <span class="info-popup">Auth. Score<sup>?</sup></span>
            <div class="ui special w250 inverted popup" style="text-align: left">
                <div class="header">Authority Score (Landing Page)</div>
                <hr/>
                <div>A [0-100] measure of the landing page’s overall quality and SEO performance.</div>
            </div>
            <br/><span class="ui small text">(landing page)</span>
        </th>
        <th class="center aligned">Backlinks<br/><span class="ui small text">(landing page)</span></th>
        <th class="center aligned">Word Count<br/><span class="ui small text">(landing page)</span></th>
    </tr>
    </thead>
    <tbody>

    @php $i=0; @endphp
    @foreach($landingPages as $page)
        <tr style="vertical-align: middle">
            <td class="center aligned">
                {{ ++$i }}.
            </td>
            <td style="white-space: nowrap;">
                <img class="ui middle aligned image" src="{{ $page->getFaviconUrl() }}" style="width: 24px"/>&nbsp;
                <a href="http://{{ $page->domain->name }}" target="_blank">
                    {{ $page->domain->name }}
                </a>
            </td>
            <td class="center aligned @if($page->domain->auth_score == $maxDomainAuthScore) positive @elseif($page->domain->auth_score == $minDomainAuthScore) error @endif">
                @if($page->domain->auth_score === null)
                    &nbsp;
                @else
                    {{ number_format($page->domain->auth_score) }}
                @endif
            </td>
            <td class="center aligned @if($page->domain->word_count == $maxDomainWordCount) positive @elseif($page->domain->word_count == $minDomainWordCount) error @endif">
                @if($page->domain->word_count === null)
                    &nbsp;
                @else
                    {{ number_format($page->domain->word_count) }}
                @endif
            </td>
            <td data-backlinks="domain" data-id="{{ $page->domain->id }}" data-report-id="{{ $page->report_id }}" class="center aligned open-backlinks @if($page->domain->backlinks == $maxDomainBacklinks) positive @elseif($page->domain->backlinks == $minDomainBacklinks) error @endif">
                @if($page->domain->backlinks === null)
                    &nbsp;
                @else
                    {{ number_format($page->domain->backlinks) }}
                @endif
            </td>
            <td>
                <a href="{{ $page->url }}" target="_blank">
                    <i class="ui small share icon"></i>
                </a>
                <a>{{ $page->name() }}</a>
            </td>
            <td class="center aligned @if($page->auth_score == $maxPageAuthScore) positive @elseif($page->auth_score == $minPageAuthScore) error @endif">
                @if($page->auth_score === null)
                    &nbsp;
                @else
                    {{ number_format($page->auth_score) }}
                @endif
            </td>
            <td data-backlinks="landing" data-id="{{ $page->id }}" data-report-id="{{ $page->report_id }}" class="center aligned open-backlinks @if($page->backlinks == $maxPageBacklinks) positive @elseif($page->backlinks == $minPageBacklinks) error @endif">
                @if($page->backlinks === null)
                    &nbsp;
                @else
                    {{ number_format($page->backlinks) }}
                @endif
            </td>
            <td class="center aligned @if($page->word_count == $maxPageWordCount) positive @elseif($page->word_count == $minPageWordCount) error @endif">
                @if($page->word_count === null)
                    &nbsp;
                @else
                    {{ number_format($page->word_count) }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
