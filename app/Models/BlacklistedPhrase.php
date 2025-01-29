<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistedPhrase extends Model
{
    protected $table = 'blacklisted_phrases';
    public $timestamps = false;
    protected $fillable = ['name','project_id'];

    public function project() { return $this->belongsTo(Project::class,'project_id'); }
}
