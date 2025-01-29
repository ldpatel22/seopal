@if(!empty($backlinks))

<div id="osr_AddToPlannerBacklinksModal" class="ui tiny modal backlinks-modal">
    <div class="header">Add to planner</div>
    <div class="content">
        <form class="ui form">
            <div class="field">
                <label>Confirm backlink:</label>
                <input type="text" name="backlink">
            </div>
        </form>
    </div>
    <div class="actions">
        <div class="ui cancel button">Cancel</div>
        <div class="ui approve primary button">Add to planner</div>
    </div>
</div>

<table id="osr_BacklinksTable" class="ui basic table datatable">
    <thead>
    <tr>
        <th>Add to planner</th>
        <th>Backlinks<br/>
            <span class="ui small text">
                @if($type == 'landing') 
                    For landing page: {{$landing_page}}
                @else 
                    For domain: {{$domain}}
                @endif
            </span>
        </th>
        <th>Auth Score</th>
        <th>Anchor</th>
        <th>Follow/Nofollow</th>
    </tr>
    </thead>
    <tbody>

    @php $i=0; @endphp
        @foreach($backlinks as $backlink)
            <tr data-id="{{ $backlink->id }}" data-name="{{ $backlink->href }}" data-type="{{ $type }}" style="vertical-align: middle" class="@if($backlink->planned == 'planned') yellow @endif">
                @if($backlink->planned == "planned")
                    <td>
                        <i class="lightbulb link icon" title="Already in planner"></i>
                    </td>
                @else
                    <td>
                        <i class="lightbulb link icon" data-action="plan" title="Add phrase to planner"></i>
                    </td>
                @endif

                <td><a href="{{ $backlink->href }}">{{ $backlink->href }}</a></td>

                @if(isset($backlink->auth_score))
                    <td>{{ $backlink->auth_score }}</td>
                @else
                    <td></td>
                @endif

                @if(isset($backlink->auth_score))
                    <td>{{ $backlink->anchor }}</td>
                @else
                    <td></td>
                @endif

                <td>
                    @if( $backlink->nofollow == "false")
                        Follow
                    @else
                        No follow
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
@endif

