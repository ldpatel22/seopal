<div class="ui stackable grid">
    <div class="row">
        <div class="eight wide column"></div>
        <div class="right aligned eight wide column">
            <div class="ui form">
                <label>Show:</label>
                <span id="osr_PhrasesFilter">
                    <span class="ui yellow label" data-color="yellow" data-tag="planned" title="Show/hide phrases added to planner">
                        <i class="lightbulb icon"></i> Planned
                    </span>
                    <span class="ui basic grey label" data-color="grey" data-tag="blacklisted" title="Show/ide blacklsited phrases">
                        <i class="eye slash icon"></i> Blacklisted
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div id="osr_AddToPlannerModal" class="ui tiny modal">
    <div class="header">Add to planner</div>
    <div class="content">
        <form class="ui form">
            <div class="field">
                <label>Confirm phrase:</label>
                <input type="text" name="phrase">
            </div>
        </form>
    </div>
    <div class="actions">
        <div class="ui cancel button">Cancel</div>
        <div class="ui approve primary button">Add to planner</div>
    </div>
</div>

<table id="osr_PhrasesTable" class="ui basic table planned">
    <thead>

{{--    <tr style="text-align: center">--}}
{{--        <th colspan="2">&nbsp;</th>--}}
{{--        <th colspan="{{ $landingPages->count() }}">Landing Pages</th>--}}
{{--    </tr>--}}
    <tr style="text-align: center">
        <th colspan="2">&nbsp;</th>
        <th style="text-align: left">Phrase</th>
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
    @foreach($phrases as $phrase)
        <tr data-id="{{ $phrase->id }}" data-name="{{ $phrase->name }}" class="@if($phrase->planned_phrase_id) yellow @elseif($phrase->blacklisted) grey @endif">
            <td>
                <i class="eye slash link icon" data-action="blacklist" title="Blacklist phrase"></i>
            </td>
            <td>
                <i class="lightbulb link icon" data-action="plan" title="Add phrase to planner"></i>
            </td>
            <td style="text-align: left">
                <span>{{ $phrase->name }}</span>
            </td>
            @foreach($landingPages as $landingPage)
                @php
                    $headings = $phrase->headings->where('landing_page_id',$landingPage->id);
                    $links = $phrase->links->where('landing_page_id',$landingPage->id);
                    $alts = $phrase->alts->where('landing_page_id',$landingPage->id);
                @endphp
                <td class="distribution list">
                    @unless($headings->isEmpty())
                        <div class="item">@include('reports.osr._distribution_headings', ['headings' => $headings])</div>
                    @endunless
                    @unless($links->isEmpty())
                        <div class="item">@include('reports.osr._distribution_links', ['links' => $links])</div>
                    @endunless
                    @unless($alts->isEmpty())
                        <div class="item">@include('reports.osr._distribution_alts', ['alts' => $alts])</div>
                    @endunless
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

