<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Stats extends Model
{
    protected $table = 'keyword_stats';

    protected $fillable = ['type','entity','locale'];

    const TYPE_KEYWORD_OVERVIEW = 'kwd_overview';
    const TYPE_DOMAIN_BACKLINKS  = 'bcklnk_domain';
    const TYPE_URL_BACKLINKS = 'bcklnk_url';

    /**
     * Returns fresh stats for an array of entities in specified locale
     *
     * @param string $type
     * @param array $entities
     * @param string|null $locale
     * @param int $timeoutHours
     * @return Collection
     */
    public static function retrieve($type, $entities, $locale, $timeoutHours)
    {
        $query = $locale !== null ? Stats::where('locale',$locale) : Stats::whereNull('locale');
        return $query->where('type',$type)
            ->whereIn('entity',$entities)
            ->get()->filter(function(Stats $stat) use($timeoutHours) {
                return $stat->updated_at->isAfter(Carbon::now()->subHours($timeoutHours));
            });
    }

    /**
     * Dumps a stat (updates existing or creates new)
     *
     * @param string $type
     * @param string $entity
     * @param string|null $locale
     * @param mixed $data
     * @return Stats
     */
    public static function dump($type, $entity, $locale, $data)
    {
        $query = $locale !== null ? Stats::where('locale',$locale) : Stats::whereNull('locale');
        $stat = $query->where('entity',$entity)->first();

        if(!$stat) {
            $stat = new Stats();
            $stat->type = $type;
            $stat->locale = $locale;
            $stat->entity = $entity;
        }

        $stat->setData($data);
        $stat->save();
        return $stat;
    }

    /**
     * Returns JSON decoded response
     *
     * @param mixed $data
     * @return object|null|array
     */
    public function setData($data)
    {
        $this->data = json_encode($data);
    }

    /**
     * Returns JSON decoded response
     *
     * @param bool $assoc
     * @return object|null|array
     */
    public function getData($assoc = false)
    {
        return json_decode($this->data, $assoc);
    }

    /**
     * Checks if there is data
     *
     * @return bool
     */
    public function hasData()
    {
        return $this->data !== null;
    }
}
