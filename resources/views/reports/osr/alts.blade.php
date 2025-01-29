<table class="ui basic table">
    <thead style="background: whitesmoke">
    <tr>
        <th style="width: 25%">Landing Page</th>
        <th>Image "Alt" Titles</th>
        <th style="width: 20%">Keyword Matches</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            @include('reports.osr._landing_page_column', ['lptdRowspan' => 1, 'lptdLandingPage' => $row['landingPage']])
            <td>
                @if(empty($row['alts']))
                    /
                @else
                    <div class="ui list">
                        @foreach($row['alts'] as $alt)
                            <div class="item">
                                <a href="{{ $alt->src }}" target="_blank">
                                    <i class="ui small picture icon"></i>
                                </a>
                                <span data-alt_id="{{ $alt->id }}" data-alt="{{ $alt->alt }}">{{ $alt->alt }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
            <td style="vertical-align: top;">
                @if(count($row['keywords']) == 0)
                    /
                @else
                    <div class="ui list">
                        @foreach($row['keywords'] as $id => $keyword)
                            <div class="item">
                                <span class="ui small label" data-alts="{{ json_encode($keyword['alts']) }}">{{ count($keyword['alts']) }}</span>
                                <a href="{{ $keyword['permalink'] }}">{{ $keyword['name'] }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
