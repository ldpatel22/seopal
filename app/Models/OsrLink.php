<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrLink extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_links';
    public $timestamps = false;
    protected $fillable = ['landing_page_id','name','href','host','report_id','parent_tag'];

    public function landingPage() { return $this->belongsTo(OsrLandingPage::class,'landing_page_id'); }
}
