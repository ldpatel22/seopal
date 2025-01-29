<?php

namespace App\Jobs;

use App\Jobs\OsrReport\AnalyseLandingPagesJob;
use App\Jobs\OsrReport\FinishReportJob;
use App\Jobs\OsrReport\ScrapeLandingPagesJob;
use App\Jobs\OsrReport\RetrieveSearchResultsJob;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class StartOsrReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Report
     *
     * @var Report
     */
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
     * @return void
     */
    public function handle()
    {
        // TODO log if this is the first start or restart, think about start_date end_date in the DB

        $report = $this->report;
        $report->status = Report::STATUS_RUNNING;
        $report->save();

        Bus::chain([
            new RetrieveSearchResultsJob($report),
            new ScrapeLandingPagesJob($report),
            new AnalyseLandingPagesJob($report),
            new FinishReportJob($report)
        ])->catch(function (\Throwable $e) use ($report) {
            $report = $report->fresh();

            // log error
            DB::table('report_logs')->insert([
                'report_id' => $report->id,
                'message' => $e->getMessage(),
                'created_at' => Carbon::now()->toDateTime(),
                'updated_at' => Carbon::now()->toDateTime(),
            ]);

            // prep logs
            $query = DB::table('report_logs')->where('report_id', $report->id);

            // save logs
            $report->log = array_map(function($log){
                return [$log->created_at,$log->message];
            },$query->get()->toArray());

            // update status
            $report->status = Report::STATUS_FAILED;
            $report->save();

            // clear logs
            $query->delete();
        })->dispatch();
    }
}
