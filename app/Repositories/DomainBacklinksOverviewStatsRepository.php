<?php

namespace App\Repositories; 

use App\Models\Stats;
use App\Services\SerpApiService;
use Illuminate\Support\Collection;
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);

class DomainBacklinksOverviewStatsRepository extends StatsRepository {

    protected function timeout() { return 48 * 10000000000; } // TODO remove CACHING

    protected function fetchStats($entities,$locale)
    {
        $stats = new Collection();

        foreach ($entities as $domain) {
            try {
                $data = SerpApiService::getBacklinksOverviewForDomain($domain,true);
              	if(is_object($data)){
                  continue;
                }
                $stats->add(Stats::dump(Stats::TYPE_DOMAIN_BACKLINKS,$domain,$locale,@$data[0]));
            } catch (\Exception $e) {
                // TODO handle this exception how exactly?
                //dd('SRANJE: ' . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Retrieves stats
     *
     * @param array $domains
     * @return \Illuminate\Support\Collection
     */
    public function get($domains)
    {
        return $this->getStats(Stats::TYPE_DOMAIN_BACKLINKS, $domains, null);
    }

}
