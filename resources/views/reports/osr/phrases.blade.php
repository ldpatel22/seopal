<div class="ui icon message">
    <i class="quote right icon"></i>
    <div class="content">
        <div class="header">
            Most Used Keywords
        </div>
        <p>Most used keywords found in top ranking landing pages that generate traffic.</p>
    </div>
</div>

@if(empty($phrases))

    <table id="osr_PhrasesTable" class="ui basic table planned">
        <thead style="background: whitesmoke">
        <tr>
            <th>Planner</th>
            <th>Keyword</th>
            <th style="text-align: right">Landing Pages</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="4">No keywords found.</td>
        </tr>
        </tbody>
    </table>

@else

<table id="osr_PhrasesTable" class="ui basic table planned">
    <thead style="background: whitesmoke">
    <tr>
        <th style="width: 10%">Planner</th>
        <th>Keyword</th>
        <th style="text-align: right">Landing Pages</th>
        <th style="width: 10%">&nbsp;</th>
    </tr>
    </thead>
    <tbody>
        @php $i=0; @endphp
        @foreach($phrases as $phrase)
            <tr data-id="{{ $phrase->id }}" data-name="{{ $phrase->name }}" class="@if($phrase->planned_phrase_id) yellow @elseif($phrase->blacklisted) grey @endif">
                <td>
                    <i class="lightbulb link icon" data-action="plan" title="Add phrase to planner"></i>
                </td>
                <td>{{ $phrase->name }}</td>
                <td style="text-align: right">{{ $phrase->usage }} </td>
                <td style="text-align: left" data-id="{{ $phrase->id }}" class='show-related'>
                    <button class="open ui basic tiny icon button">
                        <i class="arrow down icon"></i> Show
                    </button>
                    <button class="close ui basic tiny icon button">
                        <i class="arrow up icon"></i> Hide
                    </button>
                    <i class="ui large grey loading spinner icon"></i>
                </td>
            </tr>
            @foreach($phrase->landingPages as $landingPage)
                <tr data-related-id="{{ $phrase->id }}" class="related-row" style="display: none">
                    <td>&nbsp;</td>
                    <td colspan="3">
                        <img class="ui middle aligned image" src="{{ $landingPage->getFaviconUrl() }}" style="width: 24px"/>&nbsp;
                        <a href="{{ $landingPage->url }}" target="_blank">
                            {{ $landingPage->name() }}
                        </a>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

@endif

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
