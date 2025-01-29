<div class="ui segment inverted title with-attachment">
    <h1 class="ui header">
        {{ __('keywords.pageTitle') }}
        <button id="btn_toggleAddKeywords" class="ui icon basic inverted button right floated">
            <i class="plus icon"></i>
            <span>{{ __('keywords.addKeyword') }}</span>
        </button>
    </h1>
    <form id="form_AddKeywords" class="ui attached form fluid segment">
        <div class="field">
            <label>Keywords List</label>
            <textarea name="keywords" rows="2"></textarea>
            <span class="ui grey small text">
                    Enter or paste a list of keywords (separate each keyword into a new row).
                </span>
        </div>
        <button class="ui primary submit button">Add</button>
        <button class="ui cancel button">Cancel</button>
    </form>
</div>
<div class="ui segment">
    <table id="table_Keywords" class="ui basic table @if($keywords->isEmpty()) empty @endif datatable">
        <thead style="background: whitesmoke">
        <tr>
            <th class="no-sort" width="1%">&nbsp;</th>
            <th class="data-attr-type" width="40%">Keyword</th>
            <th class="data-attr-type">Related Keywords</th>
            <th class="data-attr-type">Search Vol.</th>
            <th class="data-attr-type">CPC</th>
            <th class="data-attr-type">Keyword difficulty</th>
            <th class="data-attr-type">Intent</th>
            <th class="data-attr-type">Last Report</th>
            <th class="no-sort" width="1%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @if($keywords->isEmpty())
            <tr>
                <td colspan="7">{{ __('keywords.noKeywords') }}</td>
            </tr>
        @else
            @foreach($keywords as $keyword)
                @php $lastReport = $keyword->getLastReport(); @endphp
                <tr data-key="id" data-id="{{ $keyword->id }}" data-stats="0">
                    <td class="no-sort">
{{--                        <i class="quote right middle aligned icon"></i>--}}
                        @if($keyword->planner)
                            <i class="lightbulb middle aligned yellow icon"></i>
                        @endif
                    </td>
                    <td>
                        <a href="{{ $keyword->permalink() }}">{{ $keyword->name }}</a>
                    </td>
                    <td data-id="{{ $keyword->id }}" class='show-related'>
                        <button class="open ui basic tiny icon button">
                            <i class="arrow down icon"></i> Open
                        </button>
                        <button class="close ui basic tiny icon button">
                            <i class="arrow up icon"></i> Close
                        </button>
                        <i class="ui large grey loading spinner icon"></i>
                    </td>
                    <td class='keyword-stats' data-key="search_volume" data-value="null" data-sort="null"></td>
                    <td class='keyword-stats' data-key="cpc" data-value="null" data-sort="null"></td>
                    <td class='keyword-stats' data-key="keyword_difficulty" data-value="null" data-sort="null"></td>
                    <td class='keyword-stats' data-key="intent" data-value="null" data-sort="null"></td>
                    <td data-sort="{{ $lastReport ? $lastReport->created_at->timestamp : 0 }}">
                        @if($lastReport)
                            <a href="{{ $lastReport->permalink() }}">{{ $lastReport->created_at->diffForHumans() }}</a>
                        @else
                            <span style="display: none">{{ 0 }}</span>
                            <span class="ui red text">never</span>
                        @endif
                    </td>
                    <td class="no-sort">
                        <a href="{{ $keyword->permalink() }}/?newReport=yes" class="ui mini icon basic primary button right floated">
                            <i class="chart line icon"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
