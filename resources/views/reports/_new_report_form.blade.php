@php
  $newReportKeywords = project()->keywords()->orderBy('name','ASC')->get();
@endphp
<form id="form_NewReport" class="ui attached attached form fluid segment">
    <div class="field">
        <div class="four fields">
            {{-- Type --}}
            <div class="field">
                <label>Report Type</label>
                <div class="ui selection dropdown">
                    <input type="hidden" name="type" value="osr">
                    <div class="default text">Select Type</div>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <div class="item" data-value="osr">
                            Organic Search Results
                        </div>
                    </div>
                </div>
            </div>
            {{-- Keyword --}}
            <div class="field">
                <label>Keyword</label>
                <div class="ui search selection dropdown">
                    <input type="hidden" name="keywordId" value="{{ $newReportKeywords->empty() ? '' : $newReportKeywords->first()->name }}">
                    <div class="default text">Select Keyword</div>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        @foreach($newReportKeywords as $keyword)
                            <div class="item" data-value="{{ $keyword->id }}">{{ $keyword->name }}</div>
                        @endforeach
                        <div class="item" data-value="osr">
                            Organic Search Results
                        </div>
                    </div>
                </div>
            </div>
            {{-- Locale --}}
            <div class="field">
                <label>Locale</label>
                <div class="ui fluid search selection dropdown">
                    <input type="hidden" name="locale" value="{{ project()->locale }}">
                    <i class="dropdown icon"></i>
                    <div class="default text">Select Country</div>
                    <div class="menu">
                        @foreach(config('locales') as $key => $value)
                            <div class="item" data-value="{{ $key }}"><i class="{{ $key }} flag"></i>{{ $value }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- Device --}}
            <div class="field">
                <label>Device</label>
                <div class="ui fluid selection dropdown">
                    <input type="hidden" name="device" value="desktop">
                    <i class="dropdown icon"></i>
                    <div class="default text">Select Device</div>
                    <div class="menu">
                        @foreach(['desktop','tablet','mobile'] as $device)
                            <div class="item" data-value="{{ $device }}">
                                @include('shared._device',['device' => $device, 'iconOnly' => false])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="ui primary submit button">Start report</button>
    <button class="ui cancel button">Cancel</button>
</form>
