<?php

namespace App\Jobs;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

abstract class ReportJobStage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const STATUS_SKIP = -2;
    const STATUS_FAILED = -1;
    const STATUS_SCHEDULED = 1;
    const STATUS_STARTED = 1;
    const STATUS_COMPLETED = 2;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @var Report
     */
    public $report;

    /**
     * @var string
     */
    public $stage;

    /**
     * Instantiates the report phase
     *
     * @param Report $report
     * @param string $stage
     */
    protected function __construct($report,$stage)
    {
        $this->report = $report;
        $this->stage = $stage;
    }

//    /**
//     * Determine the time at which the job should timeout.
//     *
//     * @return \DateTime
//     */
//    public function retryUntil()
//    {
//        return now()->addMinutes(10);
//    }

    /**
     * Performs the job
     *
     * @throws \Exception
     * @return void
     */
    protected abstract function perform();

    /**
     * Execute the job.
     *
     * @throws \Exception
     * @return void
     */
    public function handle()
    {
        $this->report->fresh();
        $this->updateStage(self::STATUS_STARTED,'Starting ' . $this->stage . ' stage');
        try {
            $this->perform();
            $this->updateStage(self::STATUS_COMPLETED,'Finished ' . $this->stage . ' stage');
        } catch (\Exception $e) {
            $this->updateStage(self::STATUS_FAILED, 'Stage ' . $this->stage . ' failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Writes to log
     *
     * @param string $log
     */
    protected function log($log)
    {
        DB::table('report_logs')->insert([
            'report_id' => $this->report->id,
            'message' => $log,
            'created_at' => Carbon::now()->toDateTime(),
            'updated_at' => Carbon::now()->toDateTime(),
        ]);
    }

    /**
     * Update stage status
     *
     * @param int $status
     * @param string $log
     */
    protected function updateStage($status, $log = null)
    {
        $data = $this->report->data;
        $data['stages'][$this->stage] = $status;
        $this->report->data = $data;

        if($log) $this->log($log);
        $this->report->save();
    }
}
