@if(empty($domains))
    <div class="ui borderless center aligned vertical segment tmp">
        <span>We didn't find any bonus data for this report!</span>
    </div>
@else
    <div id="osr_AddToPlannerBonusModal" class="ui tiny modal bonus-modal">
        <div class="header">Add to planner</div>
        <div class="content">
            <form class="ui form">
                <div class="field">
                    <label>Confirm keyword:</label>
                    <input type="text" name="keyword">
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui cancel button">Cancel</div>
            <div class="ui approve primary button">Add to planner</div>
        </div>
    </div>

    <p>For the keyword: {{$keyword}}, which has search volume of: {{$search_volume}}, <br>
    we have found these domains with Domain Authority between 1 and 10 which are ranked in the first five results of the SERP.<br>
    <p>Every domain which has DA between 1 and 10 and it's ranked high for keyword which has more than 300 searches means that is the easy keyword.</p>
    <p>Below the landing page you can find more keywords for this domain.</p>


        @foreach($domains as $domain)
            <table class="ui basic table datatable">
                <thead>
                    <tr>
                        <th width="5%">Domain</th>
                        <th width="70%">Domain Authority (DA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$domain->name}}</td>
                        <td>{{$domain->auth_score}}</td>
                    </tr>
                </tbody>
            </table>

            <table class="ui basic table datatable osr_BonusTable">
            <thead>
            <tr>
                <th></th>
                <th>Keyword</th>
                <th>Search Volume</th>
                <th>CPC</th>
                <th>Competition</th>
                <th>Organic Results</th>
                <th>Intent</th>
                <th>Key. Difficulty</th>
            </tr>
            </thead>
            <tbody>
                @foreach($domain->keywords as $keyword)
                    <tr data-name="{{ $keyword->keyword }}" class="@if($keyword->planned == 'planned') yellow @endif">
                        @if($keyword->planned == "planned")
                            <td>
                                <i class="lightbulb link icon" title="Already in planner"></i>
                            </td>
                        @else
                            <td>
                                <i class="lightbulb link icon" data-action="plan" title="Add phrase to planner"></i>
                            </td>
                        @endif


                        <td>{{ $keyword->keyword}}</td>
                        <td>{{ $keyword->stats->search_volume}}</td>
                        <td>{{ $keyword->stats->cpc}}</td>
                        <td>{{ $keyword->stats->competition}}</td>
                        <td>{{ $keyword->stats->organic_results}}</td>
                        <td>{{ $keyword->stats->intent}}</td>
                        <td>{{ $keyword->stats->keyword_difficulty}}</td>

                    </tr>
                @endforeach
        @endforeach
        </tbody>
    </table>
@endif