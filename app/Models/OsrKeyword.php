<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrKeyword extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_keywords';
    public $timestamps = false;
    protected $fillable = ['heading_id','alt_id','link_id','keyword_id','name','level','report_id','index'];

    public function heading() { return $this->belongsTo(OsrHeading::class,'heading_id'); }
    public function keyword() { return $this->belongsTo(Keyword::class,'keyword_id'); }
}
