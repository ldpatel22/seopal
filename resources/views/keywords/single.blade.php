@extends('layouts.auth')

@section('title')
    {{ $keyword->name }} | {{ project()->name }}
@endsection

@section('content')

    @php $lastReport = $keyword->getLastReport(); @endphp

    <div class="ui segment inverted">
        <h1 class="ui header">
            Keyword Summary
            @if($options['deleteKeyword'])
                <button id="btn_toggleDeleteKeyword" class="ui icon basic inverted button right floated">
                    <i class="trash icon"></i>
                    <span>Delete Keyword</span>
                </button>
            @endif
        </h1>
    </div>
    @unless($lastReport)
        <div class="ui info message">
            <div class="header">
                Start Report
            </div>
            <p>
                You never started a report for this keyword.
                <a class="ui mini primary button" href="{{ $keyword->permalink() }}/?newReport=yes">Start Report</a>
            </p>
        </div>
    @endunless
    <div class="ui borderless segment">
        <div class="ui four column stackable grid">
            <div class="column">
                <div>
                    <strong>Keyword</strong>
                </div>
                <p>{{ $keyword->name }}</p>
            </div>
            <div class="column">
                <div>
                    <strong>Last Report</strong>
                </div>
                <p>
                    @if($lastReport)
                        <a href="{{ $lastReport->permalink() }}">{{ $lastReport->created_at->diffForHumans() }}</a>
                    @else
                        <span class="ui red text">never</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Reports--}}
    <div class="ui segment inverted title with-attachment">
        <h1 class="ui header">
            Reports
            <button id="btn_toggleStartReport" class="ui icon basic inverted button right floated">
                <i class="angle down icon"></i>
                <span>New Report</span>
            </button>
        </h1>
        <form id="form_NewReport" class="ui attached attached form fluid segment">
          @csrf
            <input type="hidden" name="keywordId" value="{{ $keyword->id }}" />
            <div class="field">
                <div class="three fields">
                    {{-- Type --}}
                    <div class="field">
                        <label>Report Type</label>
                        <div class="ui selection dropdown">
                            <input type="hidden" name="type" value="osr">
                            <div class="default text">Type</div>
                            <i class="dropdown icon"></i>
                            <div class="menu">
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
            <i class="ui large grey loading spinner icon hide inline-spinner"></i>
        </form>
    </div>
    <div class="ui segment">
        <div class="ui relaxed divided list">
            @if($reports->isEmpty())
                You haven't ran any reports for this keyword yet. Hit the "New Report" button to run a report.
            @else
                <table id="table_Reports" class="ui basic table datatable">
                    <thead>
                    <tr>
                        <th width="1%">&nbsp;</th>
                        <th>Type</th>
                        <th>Country</th>
                        <th>Device</th>
                        <th>Started At</th>
                        <th>Started By</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>
                                <a href="{{ $report->permalink() }}">
                                    <i class="eye icon"></i>
                                </a>
                            </td>
                            <td>
                                <div class="ui small label">Organic Search Results</div>
                            </td>
                            <td>
                                @include('shared._locale',['locale' => $report->locale, 'flagOnly' => false])
                            </td>
                            <td>
                                @include('shared._device',['device' => $report->device, 'iconOnly' => false])
                            </td>
                            <td>{{ $report->created_at }}</td>
                            <td>
                                <img class="ui avatar image" src="{{ $report->user->getAvatar() }}" style="width: 20px; height: 20px;" />
                                {{ $report->user->name }}
                            </td>
                            <td>
                                @include('reports._status')
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Delete Modal --}}
    @if($options['deleteKeyword'])
        <div id="modal_DeleteKeyword" class="ui tiny test modal front transition hidden" data-keyword-id="{{ $keyword->id }}">
            <div class="header">
                Delete Keyword?
            </div>
            <div class="content">
                <p>Please confirm you wish to delete this keyword.</p>
            </div>
            <div class="actions">
                <div class="ui cancel button">Cancel</div>
                <div class="ui primary button">Confirm Deletion</div>
            </div>
        </div>
    @endif

@endsection

@section('body-end')

    {!! inject_js([
        'app/ToggleControl',
        'view/SingleKeywordPageControl',
        'view/NewReportControl',
    ]) !!}

    <script>
        $(() => {
            new SingleKeywordPageControl();
            new NewReportControl();
        });
    </script>

@endsection
