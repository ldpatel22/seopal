<?php

namespace App\Models;

use App\Models\Contracts\Sluggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Project extends Model implements Sluggable
{
    protected $fillable = ['name','locale'];

    public function users() { return $this->hasManyThrough(User::class, UserProject::class,'project_id', 'id', 'id', 'user_id'); }
    public function keywords() { return $this->hasMany(Keyword::class,'project_id'); }
    public function reports() { return $this->hasManyThrough(Report::class, Keyword::class); }
    public function blacklistedPhrases() { return $this->hasMany(BlacklistedPhrase::class,'project_id'); }
    public function user() { return $this->hasOneThrough(User::class, UserProject::class,'project_id', 'id', 'id', 'user_id')->where('type',UserProject::TYPE_OWNER); }

    public function slug() { return strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','-', $this->name)); }

    /**
     * Returns a direct link to the project page
     *
     * @return string
     */
    public function permalink()
    {
        return route('projects.single',[$this->id . '-' . $this->slug()]);
    }

    /**
     * Fetches last report for the project
     *
     * @return Report|null
     */
    public function getLastReport()
    {
        return $this->reports()->orderBy('created_at','DESC')->first();
    }

    /**
     * Checks if a keyword by name exists in project
     *
     * @param string $name
     * @return bool
     */
    public function hasKeyword($name)
    {
        return $this->keywords->where('name',$name)->count() > 0;
    }
}
