<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrHeading extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_headings';
    public $timestamps = false;
    protected $fillable = ['landing_page_id','name','level','report_id'];

    public function landingPage() { return $this->belongsTo(OsrLandingPage::class,'landing_page_id'); }
}
