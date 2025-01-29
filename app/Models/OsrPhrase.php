<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrPhrase extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_phrases';
    public $timestamps = false;
    protected $fillable = ['name','report_id','usage'];

    public function headings() { return $this->hasMany(OsrHeading::class,'phrase_id'); }
    public function links() { return $this->hasMany(OsrLink::class,'phrase_id'); }
    public function alts() { return $this->hasMany(OsrAlt::class,'phrase_id'); }

    public function landingPages() { return $this->hasManyThrough(OsrLandingPage::class, OsrPhraseLandingPage::class, 'phrase_id', 'id', 'id', 'landing_page_id'); }
}
