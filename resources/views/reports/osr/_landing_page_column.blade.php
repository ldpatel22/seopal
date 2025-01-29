<td rowspan="{{ $lptdRowspan }}" style="vertical-align: top;">
    <img src="{{ $lptdLandingPage->getFaviconUrl() }}" style="width: 12px" />
    <a href="#" class="page-info-popup">{{ $lptdLandingPage->url }}</a>
    <div class="ui special w400 mh300 scrollable popup" style="text-align: left">
        <div class="header">{{ $lptdLandingPage->domain->name }}</div>
        <hr/>
        <p>
            <span class="ui small text">URL:</span>
            <br />
            <a href="{{ $lptdLandingPage->url }}" target="_blank">{{ $lptdLandingPage->url }}</a>
        </p>
        <p>
            <span class="ui small text">Title:</span>
            <br />
            @if(empty($lptdLandingPage->title))
                /
            @else
                {{ $lptdLandingPage->title }}
            @endif
        </p>
        <p>
            <span class="ui small text">Description:</span>
            <br />
            @if(empty($lptdLandingPage->description))
                /
            @else
                {{ $lptdLandingPage->description }}
            @endif
        </p>
        <p>
            <span class="ui small text">Word count:</span>
            <br />
            @if($lptdLandingPage->word_count === null)
                /
            @else
                <span class="ui small label">{{ number_format($lptdLandingPage->word_count) }}</span>
            @endif
        </p>
        <p>
            <span class="ui small text">Backlinks:</span>
            <br />
            @if($lptdLandingPage->backlinks === null)
                /
            @else
                <span class="ui small label">{{ number_format($lptdLandingPage->backlinks) }}</span>
            @endif
        </p>
        <p>
            <span class="ui small text">Preview Image:</span>
            <br />
            @if($lptdLandingPage->hasPreviewImage())
                <img class="ui image" src="{{ $lptdLandingPage->getPreviewImageUrl() }}" />
            @else
                /
            @endif
        </p>
    </div>
</td>
