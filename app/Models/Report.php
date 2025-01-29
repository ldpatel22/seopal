<?php

namespace App\Models;

use App\Models\Contracts\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Report extends Model implements Sluggable
{
    const STATUS_SCHEDULED = 0;
    const STATUS_RUNNING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = -1;

    const TYPE_ORGANIC_SEARCH_RESULTS = 'osr';

    protected $casts = [
        'data' => 'array',
        'log' => 'array',
    ];

    protected $fillable = ['keyword_id','type','locale','device','status','data','user_id'];

    public function user() { return $this->belongsTo(User::class,'user_id'); }
    public function keyword() { return $this->belongsTo(Keyword::class,'keyword_id'); }
    public function slug() { return $this->keyword->slug() . '-' .  $this->created_at->format('Y-m-d'); }

    /**
     * Returns a direct link to the report page
     *
     * @return string
     */
    public function permalink()
    {
        return route('reports.single',[$this->id . '-' . $this->slug()]);
    }

    /**
     * Returns report name
     *
     * @return string
     */
    public function name()
    {
        return $this->keyword->name . ' @ ' . $this->created_at;
    }

    public function isScheduled() { return $this->status == self::STATUS_SCHEDULED; }
    public function isRunning() { return $this->status == self::STATUS_RUNNING; }
    public function isCompleted() { return $this->status == self::STATUS_COMPLETED; }
    public function isFailed() { return $this->status == self::STATUS_FAILED; }
}
