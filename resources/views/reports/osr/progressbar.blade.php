<div id="projectStages" class="ui mini steps">
    <div class="step @include('reports._stage_class',['stage' => 'searchresults'])" data-stage="searchresults">
        @include('reports._stage_icon',['stage' => 'searchresults'])
        <div class="content">
            <div class="title">Search Results</div>
            <div class="description">
                @if($report->data['stages']['searchresults'] < -1)
                    Search results disabled
                @elseif($report->data['stages']['searchresults'] == -1)
                    Error obtaining search results
                @elseif($report->data['stages']['searchresults'] == 0)
                    Search results fetching
                @elseif($report->data['stages']['searchresults'] == 1)
                    Obtaining search results
                @elseif($report->data['stages']['searchresults'] > 1)
                    Search results obtained
                @endif
            </div>
        </div>
    </div>
    <div class="step @include('reports._stage_class',['stage' => 'scraping'])" data-stage="scraping">
        @include('reports._stage_icon',['stage' => 'scraping'])
        <div class="content">
            <div class="title">Scraping</div>
            <div class="description">
                @if($report->data['stages']['scraping'] < -1)
                    Website scraping disabled
                @elseif($report->data['stages']['scraping'] == -1)
                    Error scraping websites
                @elseif($report->data['stages']['scraping'] == 0)
                    Websites scraping
                @elseif($report->data['stages']['scraping'] == 1)
                    Scraping websites
                @elseif($report->data['stages']['scraping'] > 1)
                    Websites scraped
                @endif
            </div>
        </div>
    </div>
    <div class="step @include('reports._stage_class',['stage' => 'analysing'])" data-stage="analysing">
        @include('reports._stage_icon',['stage' => 'analysing'])
        <div class="content">
            <div class="title">Landing Pages</div>
            <div class="description">
                @if($report->data['stages']['analysing'] < -1)
                    Landing pages analysis disabled
                @elseif($report->data['stages']['analysing'] == -1)
                    Error analysing landing pages
                @elseif($report->data['stages']['analysing'] == 0)
                    Landing pages analysis
                @elseif($report->data['stages']['analysing'] == 1)
                    Analysing landing pages
                @elseif($report->data['stages']['analysing'] > 1)
                    Landing pages analysed
                @endif
            </div>
        </div>
    </div>

    {{--<div class="step @include('reports._stage_class',['stage' => 'backlinks'])" data-stage="backlinks">
        @include('reports._stage_icon',['stage' => 'backlinks'])
        <div class="content">
            <div class="title">Backlinks</div>
            <div class="description">
                @if($report->data['stages']['backlinks'] < -1)
                    Backlink analysis disabled
                @elseif($report->data['stages']['backlinks'] == -1)
                    Error analysing backlinks
                @elseif($report->data['stages']['backlinks'] == 0)
                    Backlink analysis
                @elseif($report->data['stages']['backlinks'] == 1)
                    Analysing backlinks
                @elseif($report->data['stages']['backlinks'] > 1)
                    Backlinks analysed
                @endif
            </div>
        </div>
    </div>--}}
</div>
