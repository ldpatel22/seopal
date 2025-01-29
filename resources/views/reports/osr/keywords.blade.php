<table class="ui basic table">
    <thead>
    <tr style="text-align: center">
        <th style="text-align: left; width: 25%">Keyword</th>
        <th>Pages</th>
        @foreach([1,2,3,4,5,6] as $level)
            <th>
                @include('reports.osr._heading_icon',['headingLevel' => $level])
            </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr style="text-align: center">
            <td style="text-align: left">
                <a href="{{ $row['permalink'] }}">{{ $row['name'] }}</a>
            </td>
            <td>
                @if(empty($row['pages']))
                    <span class="ui small disabled label">0</span>
                @else
                    <a class="ui small label count-popup">{{ count($row['pages']) }}</a>
                    <div class="ui special w350 popup" style="text-align: left">
                        <div class="header">Pages</div>
                        <div class="ui relaxed divided scrollable mh150 list">
                            @foreach($row['pages'] as $page)
                                <div class="ui item">
                                    <span class="ui small label">{{ $page['count'] }}</span>
                                    <a href="{{ $page['url'] }}" target="_blank">{{ $page['url'] }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </td>
            @foreach([1,2,3,4,5,6] as $level)
            <td>
                @if(empty($row['h'.$level]))
                    <span class="ui small disabled label">0</span>
                @else
                    <a class="ui small label count-popup">{{ count($row['h'.$level]) }}</a>
                    <div class="ui special w350 popup" style="text-align: left">
                        <div class="header">
                            @include('reports.osr._heading_icon',['headingLevel' => $level])
                            Headings
                        </div>
                        <div class="ui relaxed divided scrollable mh150 list">
                            @foreach($row['h'.$level] as $title)
                            <div class="ui item">{!! $title !!}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
