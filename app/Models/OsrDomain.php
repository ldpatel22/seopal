<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrDomain extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_domains';
    public $timestamps = false;

    protected $fillable = ['report_id','name', 'local'];

}
