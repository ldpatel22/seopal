<?php

namespace App\Repositories;

use App\Models\Stats;
use App\Services\SerpApiService;
use Illuminate\Support\Collection;

class KeywordOverviewStatsRepository extends StatsRepository {

    protected function timeout() { return 48 * 10000000000; } // TODO remove CACHING

    protected function fetchStats($entities, $locale)
    {
        $stats = new Collection();
        $data = SerpApiService::getKeywordsOverviewForLocale($entities, $locale);

        if (isset($data->error)) {
            \Log::error('Error fetching stats: ' . json_encode($data->error));
            return $stats; // Return empty collection or handle accordingly
        }

        try {
            foreach ($data as $stat) {
                if (!isset($stat->keyword)) {
                    \Log::warning('Skipping malformed data: ' . json_encode($stat));
                    continue;
                }
                $stats->add(Stats::dump(Stats::TYPE_KEYWORD_OVERVIEW, $stat->keyword, $locale, $stat));
            }
        } catch (\Exception $e) {
            \Log::error('Error processing stats: ' . $e->getMessage(), ['exception' => $e]);
        }

        return $stats;
    }


    /**
     * Retrieves stats
     *
     * @param array $keywords
     * @param string|null $locale
     * @return \Illuminate\Support\Collection
     */
    public function get($keywords, $locale)
    {
        return $this->getStats(Stats::TYPE_KEYWORD_OVERVIEW, $keywords, $locale);
    }

}
