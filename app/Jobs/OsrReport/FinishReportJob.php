<?php

namespace App\Jobs\OsrReport;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FinishReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Report */
    private $report;

    /**
     * Create a new job instance.
     *
     * @param Report $report
     * @return void
     */
    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     * @return void
     */
    public function handle()
    {
        // prep logs
        $query = DB::table('report_logs')->where('report_id', $this->report->id);

        // save logs
        $this->report->log = array_map(function($log){
            return [$log->created_at,$log->message];
        },$query->get()->toArray());

        // update status
        $this->report->status = Report::STATUS_COMPLETED;
        $this->report->save();

        // clear logs
        $query->delete();
    }
}
