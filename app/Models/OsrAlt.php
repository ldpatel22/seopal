<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrAlt extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_alts';
    public $timestamps = false;
    protected $fillable = ['landing_page_id','alt','src','report_id'];

    public function landingPage() { return $this->belongsTo(OsrLandingPage::class,'landing_page_id'); }
}
