<div style="display: none"></div>

<table id="osr_PlannerTable" class="ui basic table">
    <thead>
    <tr>
        <th width="1%">&nbsp;</th>
        <th>Phrase</th>
        <th>Original Phrase</th>
        <th>Search Vol.</th>
        <th>CPC</th>
        <th>Competition</th>
        <th>Organic Results</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
        @if($phrases->isEmpty())
            <tr class="placeholder">
                <td colspan="7">
                    You don't have any phrases in the planner. Add them from the "Phrases" tab.
                </td>
            </tr>
        @else
            @foreach($phrases as $phrase)
                <tr data-id="{{ $phrase->id }}" data-type="phrase-row" class="@if($phrase->keyword_id) olive @endif">
                    @if($phrase->keyword_id)
                        <td>
                            <i class="ui check icon"></i>
                        </td>
                    @else
                        <td class="ui form">
                            <div class="inline field">
                                <div class="ui checkbox">
                                    <input type="checkbox" tabindex="0" value="{{ $phrase->id }}" class="hidden" />
                                    <label></label>
                                </div>
                            </div>
                        </td>
                    @endif
                    <td>
                        @if($phrase->keyword_id)
                            <a href="{{ $phrase->keyword->permalink() }}" target="_blank">{{ $phrase->name }}</a>
                        @else
                            {{ $phrase->name }}
                        @endif
                    </td>
                    <td>
                        {{ $phrase->phrase->name }}
                    </td>
                    <td data-key="search_volume">
                        <div class="ui placeholder"><div class="short line"></div></div>
                    </td>
                    <td data-key="cpc">
                        <div class="ui placeholder"><div class="short line"></div></div>
                    </td>
                    <td data-key="competition">
                        <div class="ui placeholder"><div class="short line"></div></div>
                    </td>
                    <td data-key="organic_results">
                        <div class="ui placeholder"><div class="short line"></div></div>
                    </td>
                    <td>
                    <button data-id="{{$phrase->id}}" data-type="phrase" class="delete-planner ui mini red icon basic primary button right floated">
                        <i class="trash icon"></i>
                        <span>Delete</span>
                    </button>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>


<div class="ui form" style="text-align: right">
    <div id="osr_addPhrasesButton" class="ui small disabled primary button">Add to Keywords</div>
</div>


<div class='multi-table'>
    <div class='left'>

        <table id="osr_PlannerBacklinksTableLanding" class="ui basic table">
            <thead>
            <tr>
                <th>Backlinks to follow</th>
                <th>Auth Score</th>
                <th>Anchor</th>
                <th>Follow/Nofollow</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                @if($backlinks_landing->isEmpty() && $backlinks_domain->isEmpty())
                    <tr class="placeholder">
                        <td colspan="7">
                            You don't have any backlinks in the planner. Add them from the "Backlinks" tab.
                        </td>
                    </tr>
                @else
                    @if(!$backlinks_landing->isEmpty())
                        @foreach($backlinks_landing as $backlink)
                            <tr data-id="{{ $backlink->id }}" data-type="backlink-row">
                                <td>
                                    <a href="{{ $backlink->name }}" target="_blank">{{ $backlink->name }}</a>
                                </td>
                                <td>
                                    {{ $backlink->auth_score }}</a>
                                </td>
                                <td>
                                    {{ $backlink->anchor }}</a>
                                </td>
                                <td>
                                    @if( $backlink->nofollow == "false")
                                        Follow
                                    @else
                                        No follow
                                    @endif
                                </td>
                                <td>
                                    <button data-id="{{$backlink->id}}" data-type="backlink" class="delete-planner ui mini red icon basic primary button floated">
                                        <i class="trash icon"></i>
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    @if(!$backlinks_domain->isEmpty())
                        @foreach($backlinks_domain as $backlink)
                            <tr data-id="{{ $backlink->id }}" data-type="backlink-row">
                                <td>
                                    <a href="{{ $backlink->name }}" target="_blank">{{ $backlink->name }}</a>
                                </td>
                                <td>
                                    {{ $backlink->auth_score }}</a>
                                </td>
                                <td>
                                    {{ $backlink->anchor }}</a>
                                </td>
                                <td>
                                    @if( $backlink->nofollow == "false")
                                        follow
                                    @else
                                        nofollow
                                    @endif
                                </td>
                                <td>
                                    <button data-id="{{$backlink->id}}" data-type="backlink" class="delete-planner ui mini red icon basic primary button floated">
                                        <i class="trash icon"></i>
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endif
            </tbody>
        </table>

    </div>
    <div class='right'>
        <table id="osr_PlannerWordCountTable" class="ui basic table">
            <thead>
            <tr>
                <th>First ten results in SERP have average number of <span class='blue-text'>{{ $word_count }}</span> words per landing page.</th>
                <th></th>
            </tr>
            </thead>
        </table>
        <table id="osr_PlannerWordCountTable" class="ui basic table">
            <thead>
            <tr>
                <th>Domain</th>
                <th>Auth. Score</th>
                <th>Landing Page</th>
                <th>Auth. Score</th>
            </tr>
            </thead>
            <tbody>
                @foreach($landing_pages as $landing_page)
                    <tr>
                        <td>{{ $landing_page->domain->name }}</td>
                        <td>{{ $landing_page->domain->auth_score }}</td>
                        <td>{{ $landing_page->name() }}</td>
                        <td>{{ $landing_page->auth_score }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">
                        <strong>First three domain results in SERP have average number of <span class='blue-text'>{{ $auth_count }}</span> for authority score.</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div id="modal_DeletePlanner" class="ui tiny test modal front transition hidden">
    <div class="header">
        Delete this item from planner?
    </div>
    <div class="content">
        <p>Please confirm you wish to delete this item from the planner.</p>
    </div>
    <div class="actions">
        <div class="ui cancel button">Cancel</div>
        <div class="ui primary button">Confirm Deletion</div>
    </div>
</div>
