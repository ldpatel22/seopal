@extends('layouts.auth')

@section('title')
    Reports | {{ project()->name }}
@endsection

@section('content')

    <div class="ui segment inverted title with-attachment">
        <h1 class="ui header">
            Reports
            <button id="btn_toggleStartReport" class="ui icon basic inverted button right floated">
                <i class="chart line icon"></i>
                <span>New Report</span>
            </button>
        </h1>
        @include('reports._new_report_form')
    </div>
    <div class="ui segment">
        <table id="table_Reports" class="ui basic table datatable">
            <thead>
            <tr>
                <th width="1%">&nbsp;</th>
                <th>Type</th>
                <th>Country</th>
                <th>Device</th>
                <th>Started At</th>
                <th>Started By</th>
                <th>Keyword</th>
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
                        <a href="{{ $report->keyword->permalink() }}">{{ $report->keyword->name }}</a>
                    </td>
                    <td>
                        @include('reports._status')
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('body-end')

    {!! inject_js([
        'app/ToggleControl',
        'view/NewReportControl',
        'view/ReportsPageControl',
    ]) !!}

    <script>
        $(() => {
            new ReportsPageControl();
            new NewReportControl();
        });
    </script>

@endsection
