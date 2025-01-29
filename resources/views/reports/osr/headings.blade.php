<div class="ui stackable grid">
    <div class="row">
        <div class="eight wide column"></div>
        <div class="right aligned eight wide column">
            <div class="ui form">
                <label>Show:</label>
                <span id="osr_HeadingsLevelFilter">
                    <span class="ui black label" data-level="1" data-color="black">H1</span>
                    <span class="ui blue label" data-level="2" data-color="blue">H2</span>
                    <span class="ui basic label" data-level="3" data-color="violet">H3</span>
                    <span class="ui basic label" data-level="4" data-color="purple">H4</span>
                    <span class="ui basic label" data-level="5" data-color="teal">H5</span>
                    <span class="ui basic label" data-level="6" data-color="olive">H6</span>
                </span>
            </div>
        </div>
    </div>
</div>

<table id="osr_HeadingsTable" class="ui basic table l1 l2">
    <thead style="background: whitesmoke">
    <tr>
        <th style="width: 25%">Landing Page</th>
        <th>Headings</th>
        <th style="width: 20%">Keyword Matches</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            @include('reports.osr._landing_page_column', ['lptdRowspan' => 1, 'lptdLandingPage' => $row['landingPage']])
            <td>
                @if(empty($row['headings']))
                    /
                @else
                    <div class="ui list">
                    @foreach($row['headings'] as $heading)
                        <div class="item l{{ $heading->level }}">
                            @include('reports.osr._heading_icon',['headingLevel' => $heading->level])
                            <span data-heading_id="{{ $heading->id }}" data-name="{{ $heading->name }}">{{ $heading->name }}</span>
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
                                <span class="ui small label" data-headings="{{ json_encode($keyword['headings']) }}">{{ count($keyword['headings']) }}</span>
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
