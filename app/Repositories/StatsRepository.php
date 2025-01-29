<?php

namespace App\Repositories;

use App\Models\Stats;
use Illuminate\Support\Collection;

abstract class StatsRepository {

    /**
     * Stats freshness timeout in HOURS
     *
     * @return int
     */
    protected abstract function timeout();

    /**
     * Fetches stats (via API)
     *
     * @param array $entities
     * @param string|null $locale
     *
     * @return Collection
     */
    protected abstract function fetchStats(array $entities, $locale);

    /**
     * Retrieves fresh stats and combines with freshly cached stats
     *
     * @param string type
     * @param array $entities
     * @param string|null $locale
     * @return \Illuminate\Support\Collection
     */
    protected function getStats($type, $entities, $locale)
    {
        // get stats
        $stats = Stats::retrieve($type, $entities, $locale, $this->timeout());
        $freshEntities = $stats->map(function ($stat) { return $stat->entity; })->toArray();

        if(!empty(array_diff($entities,$freshEntities))){
            // merge with fetched stats
            return $stats->merge($this->fetchStats(array_diff($entities,$freshEntities),$locale));
        }

        return $stats;

    }

}
