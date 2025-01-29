<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrPlannedPhrase extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_planned_phrases';
    public $timestamps = false;
    protected $fillable = ['name','phrase_id','report_id'];

    public $blacklisted = false;

    public function phrase() { return $this->belongsTo(OsrPhrase::class,'phrase_id'); }
    public function keyword() { return $this->belongsTo(Keyword::class,'keyword_id'); }

}
