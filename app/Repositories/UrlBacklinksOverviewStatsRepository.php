<?php

namespace App\Repositories;

use App\Models\Stats;
use App\Services\SerpApiService;
use Illuminate\Support\Collection;

class UrlBacklinksOverviewStatsRepository extends StatsRepository {

    protected function timeout() { return 48 * 10000000000; } // TODO remove CACHING

    protected function fetchStats($entities,$locale)
    {
        $stats = new Collection();

        foreach ($entities as $url) {
            try {
                $data = SerpApiService::getBacklinksOverviewForUrl($url,$locale);
                $stats->add(Stats::dump(Stats::TYPE_URL_BACKLINKS,$url,$locale,$data[0]));
            } catch (\Exception $e) {
                // TODO handle this exception how exactly? => consider (for ALL SIMILAR) to return null stats (for "50" not found)
                //dd('SRANJE: ' . $e->getMessage());
                // TODO "SRANJE: Semrush API error on getBacklinksOverview(https://grm.digital/bs/blog/technical-seo-key-facts,url) => ERROR 50 :: NOTHING FOUND"
            }
        }

        return $stats;
    }

    /**
     * Retrieves stats
     *
     * @param array $urls
     * @return \Illuminate\Support\Collection
     */
    public function get($urls)
    {
        return $this->getStats(Stats::TYPE_URL_BACKLINKS, $urls, null);
    }

}
