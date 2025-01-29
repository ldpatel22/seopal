<?php

namespace App\Models\Traits;

use App\Models\Report;

trait BelongsToReport
{
    public function report() { return $this->belongsTo(Report::class,'report_id'); }
}
