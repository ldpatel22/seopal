@switch($report->status)
    @case(\App\Models\Report::STATUS_COMPLETED)
        <div class="ui small green label">completed</div>
        @break
    @case(\App\Models\Report::STATUS_FAILED)
        <div class="ui small red label">failed</div>
        @break
    @case(\App\Models\Report::STATUS_RUNNING)
        <div class="ui small yellow label">running</div>
        @break
    @case(\App\Models\Report::STATUS_SCHEDULED)
        <div class="ui small label">scheduled</div>
        @break
@endswitch
