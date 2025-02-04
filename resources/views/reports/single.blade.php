@extends('layouts.auth')

@section('title')
{{ $report->name() }} | {{ $report->keyword->project->name }}
@endsection

@section('pre-header')
<div id="full-screen-header" class="ui fixed inverted menu">
    <div class="left item">
        <span class="ui label">Organic Search Results</span>
        &nbsp;
        @include('shared._device',['device' => $report->device, 'iconOnly' => true])
        &nbsp;
        <i class="{{ $report->locale }} flag"></i>

        {{ $report->keyword->name }}
    </div>
    <div class="right item">
        <i class="window close outline large link icon toggle-full-screen-report"></i>
    </div>
</div>
@endsection

@section('content')

{{-- <style>--}}
{{-- .dataTables_wrapper .row:first-child { display: none }--}}
{{-- .dataTables_wrapper .row:last-child { display: none }--}}
{{-- </style>--}}

{{-- Summary --}}
<div class="ui segment inverted full-screen-report-hidden">
    <h1 class="ui  ">
        Report Summary
        @if($options['deleteReport'])
        <button id="btn_toggleDeleteReport" class="ui icon basic inverted button right floated">
            <i class="trash icon"></i>
            <span>Delete Report</span>
        </button>
        @endif
    </h1>
</div>
<div class="ui borderless segment full-screen-report-hidden">
    <div class="ui four column stackable grid">
        <div class="column">
            <div>
                <strong>Report Type</strong>
            </div>
            <p>
                <span class="ui label">Organic Search Results</span>
            </p>
        </div>
        <div class="column">
            <div>
                <strong>Focus Keyword</strong>
            </div>
            <p>
                <a href="{{ $report->keyword->permalink() }}">{{ $report->keyword->name }}</a>
            </p>
        </div>
        <div class="column">
            <div>
                <strong>Country</strong>
            </div>
            <p>
                @include('shared._locale',['locale' => $report->locale, 'flagOnly' => false])
            </p>
        </div>
        <div class="column">
            <div>
                <strong>Device</strong>
            </div>
            <p>
                @include('shared._device',['device' => $report->device, 'iconOnly' => false])
            </p>
        </div>
    </div>
    <div class="ui four column stackable grid">
        <div class="column">
            <div>
                <strong>Started On</strong>
            </div>
            <p>
                {{ $report->created_at }}
            </p>
        </div>
        <div class="column">
            <div>
                <strong>Started By</strong>
            </div>
            <p>
                <img class="ui avatar image" src="{{ $report->user->getAvatar() }}" style="width: 20px; height: 20px;" />
                {{ $report->user->name }}
            </p>
        </div>
    </div>
</div>
<div class="ui borderless segment full-screen-report-hidden">
    <div>
        <strong>Report Status</strong>
    </div>
    @if($report->isCompleted() || $report->isFailed())
    @include('reports.osr.progressbar')
    @else`
    <div id="projectStages" class="ui placeholder">
        <div class="header">
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>
    @endif
</div>

{{-- Failure Message --}}
<div id="failMessage" class="ui segment" style="{{ $report->isFailed() ? '' : 'display:none' }}">
    <div class="ui relaxed divided list">
        Report failed.
    </div>
</div>

@unless($report->isFailed())
@include('reports.osr.findings')
@include('reports.osr.recommendations')
@endunless

{{-- Delete Modal --}}
@if($options['deleteReport'])
<div id="modal_DeleteReport" class="ui tiny test modal front transition hidden" data-report-id="{{ $report->id }}">
    <div class="header">
        Delete Report?
    </div>
    <div class="content">
        <p>Please confirm you wish to delete this Report.</p>
    </div>
    <div class="actions">
        <div class="ui cancel button">Cancel</div>
        <div class="ui primary button">Confirm Deletion</div>
    </div>
</div>
@endif

@endsection

@section('body-end')

{{-- {!! inject_css([--}}
{{-- 'dataTables.fixedHeader.min',--}}
{{-- ]) !!}--}}

{!! inject_js([
'view/SingleReportPageControl',
]) !!}

<script>

</script>

<style>
    [data-headings],
    [data-links],
    [data-alts] {
        cursor: pointer;
    }

    th.rotated {
        vertical-align: bottom;
        text-align: center;
        vertical-align: bottom !important;
        font-size: small;
        text-transform: lowercase !important;
    }

    th.rotated span {
        -ms-writing-mode: tb-rl;
        -webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
    }

    @media only screen and (max-width: 767px) {
        tr.non-mobile {
            display: none !important;
        }
    }

    @media only screen and (min-width: 768px) {
        tr.mobile {
            display: none !important;
        }
    }

    .distribution.list .item {
        padding-bottom: 5px;
    }

    #osr_HeadingsLevelFilter .label {
        cursor: pointer;
    }

    #osr_HeadingsTable .item.l1,
    #osr_HeadingsTable .item.l2,
    #osr_HeadingsTable .item.l3,
    #osr_HeadingsTable .item.l4,
    #osr_HeadingsTable .item.l5,
    #osr_HeadingsTable .item.l6 {
        display: none;
    }

    #osr_HeadingsTable.l1 .item.l1 {
        display: block;
    }

    #osr_HeadingsTable.l2 .item.l2 {
        display: block;
    }

    #osr_HeadingsTable.l3 .item.l3 {
        display: block;
    }

    #osr_HeadingsTable.l4 .item.l4 {
        display: block;
    }

    #osr_HeadingsTable.l5 .item.l5 {
        display: block;
    }

    #osr_HeadingsTable.l6 .item.l6 {
        display: block;
    }

    #osr_PhrasesTable thead {
        background-color: white;
        position: sticky;
        top: -10px;
    }

    .full-screen-report #osr_PhrasesTable thead {
        top: 44px !important;
    }

    #osr_PhrasesFilter .label {
        cursor: pointer;
    }

    #osr_PhrasesTable tr.yellow,
    #osr_PhrasesTable tr.grey {
        display: none;
    }

    #osr_PhrasesTable.planned tr.yellow {
        display: table-row;
    }

    #osr_PhrasesTable.blacklisted tr.grey {
        display: table-row;
    }

    /*#osr_PhrasesTable tr { vertical-align: top; }*/
    #osr_PhrasesTable tr .link.icon {
        visibility: hidden;
    }

    #osr_PhrasesTable tr:hover .link.icon {
        visibility: visible;
    }

    #osr_PhrasesTable tr.yellow [data-action="plan"] {
        visibility: visible;
    }

    #osr_PhrasesTable tr.yellow [data-action="blacklist"] {
        display: none;
    }

    #osr_PhrasesTable tr.grey [data-action="plan"] {
        display: none;
    }

    #osr_PhrasesTable tr.grey [data-action="blacklist"] {
        visibility: visible;
    }

    /*.tab.segment {*/
    /*    border-left: none !important;*/
    /*    border-right: none !important;*/
    /*}*/
</style>

<script>
    const ReportStatus = {
        SCHEDULED: 0,
        RUNNING: 1,
        COMPLETED: 2,
        FAILED: -1
    };

    $(() => {
        new SingleReportPageControl({
            'reportId': {
                {
                    $report - > id
                }
            },
            'status': {
                {
                    $report - > status
                }
            }
        });
    });
</script>

@endsection