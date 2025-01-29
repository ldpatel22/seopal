<?php

namespace App\Models;

use App\Models\Contracts\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model implements Sluggable
{
    protected $fillable = ['name','project_id'];

    public function project() { return $this->belongsTo(Project::class,'project_id'); }
    public function reports() { return $this->hasMany(Report::class,'keyword_id'); }

    public function slug() { return strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','-', $this->name)); }

    /**
     * Returns a direct link to the keyword page
     *
     * @return string
     */
    public function permalink()
    {
        return route('keywords.single',[$this->id . '-' . $this->slug()]);
    }

    /**
     * Fetches last report for the keyword
     *
     * @return Report|null
     */
    public function getLastReport()
    {
        return $this->reports()->orderBy('created_at','DESC')->first();
    }
}
