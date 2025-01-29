<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrContent extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_content';
    public $timestamps = false;
    protected $fillable = ['report_id'];

    /**
     * Returns titles array or null if titles haven't been fetched yet
     *
     * @return null|array
     */
    public function getTitles()
    {
        return $this->hasTitles() ? json_decode($this->titles, true) : null;
    }

    /**
     * Checks if titles hav been fetched
     *
     * @return bool
     */
    public function hasTitles()
    {
        return $this->titles !== null;
    }

    /**
     * Returns descriptions array or null if descriptions haven't been fetched yet
     *
     * @return null|array
     */
    public function getDescriptions()
    {
        return $this->hasDescriptions() ? json_decode($this->descriptions, true) : null;
    }

    /**
     * Checks if descriptions hav been fetched
     *
     * @return bool
     */
    public function hasDescriptions()
    {
        return $this->descriptions !== null;
    }

    /**
     * Returns content or null if content hasn't been fetched yet
     *
     * @return null|array
     */
    public function getContent()
    {
        return $this->haContent() ? json_decode($this->content, true) : null;
    }

    /**
     * Checks if content has been fetched
     *
     * @return bool
     */
    public function haContent()
    {
        return $this->content !== null;
    }

}
