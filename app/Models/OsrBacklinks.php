<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrBacklinks extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_backlinks';
    public $timestamps = false;

    protected $fillable = ['report_id', 'landing_page_id', 'href', 'auth_score', 'anchor', 'nofollow'];

    public function landingPage() { return $this->belongsTo(OsrLandingPage::class,'landing_page_id'); }
}
