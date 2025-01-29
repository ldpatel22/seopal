<table class="ui basic table">
    <thead style="background: whitesmoke">
    <tr>
        <th style="width: 25%">Landing Page</th>
        <th>Links</th>
        <th> Parent elements </th>
        <th style="width: 20%">Keyword Matches</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            @include('reports.osr._landing_page_column', ['lptdRowspan' => 1, 'lptdLandingPage' => $row['landingPage']])
            <td>
                @if(empty($row['links']))
                    /
                @else
                    <div class="ui list">
                        @foreach($row['links'] as $link)
                            <div class="item">
                                <a href="{{ $link->href }}" target="_blank">
                                    <i class="ui small share icon"></i>

                                <span data-link_id="{{ $link->id }}" data-name="{{ $link->name }}">{{ $link->name }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
            <td>
                @if(empty($row['links']))
                    /
                @else
                    <div class="ui list">
                        @foreach($row['links'] as $link)
                            <div class="item">
                                <span class="ui small basic grey label">{{ "<" . $link->parent_tag . ">" }}</span>
                                </a>
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
                                <span class="ui small label" data-links="{{ json_encode($keyword['links']) }}">{{ count($keyword['links']) }}</span>
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
