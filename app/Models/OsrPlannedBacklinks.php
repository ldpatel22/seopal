<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrPlannedBacklinks extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_planned_backlinks';
    public $timestamps = false;

    protected $fillable = ['report_id', 'backlink_id', 'name', 'type'];

}
