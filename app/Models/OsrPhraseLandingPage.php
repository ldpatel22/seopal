<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OsrPhraseLandingPage extends Model
{
    protected $table = 'osr_phrases_landing_pages';
    public $timestamps = false;
    protected $fillable = ['phrase_id','landing_page_id'];
}
