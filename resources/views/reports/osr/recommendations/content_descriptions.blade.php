<p>&nbsp;</p>
<p>Here are three optimized landing page descriptions for the keyword <strong>{{ $keyword }}</strong>:</p>
<div class="ui divided list">
    @foreach($descriptions as $description)
        <div class="item">
            <div class="left floated content">
                <div class="ui mini icon button"><i class="copy icon"></i></div>
            </div>
            <div class="content ui basic label">
                {{ $description }}
            </div>
        </div>
    @endforeach
</div>
