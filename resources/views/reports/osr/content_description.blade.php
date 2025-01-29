<p>&nbsp;</p>
<table class="ui celled table">
    <thead>
    <tr>
        <th>Keyword</th>
        <th>SERP Meta Description</th>
    </tr>
    </thead>
    <tbody>
    @foreach($descriptions as $description)
        <tr>
            <td>{{ $keyword }}</td>
            <td>
                <div class="content ui basic label">
                    {{ $description }}
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>