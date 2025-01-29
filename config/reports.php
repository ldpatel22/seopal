<?php

return [

    'osr' => [
        'semrush' => [
            /**
             * Cache Timeout
             * number of HOURS cached results are valid
             */
            'cache_timeout' => 1 * 24 * 1000, // TODO remove this !!!
            /**
             * Request Timeout
             * number of seconds scraping request will wait for a response (0 = indefinetly)
             */
            'timeout' => 60,
        ],
        'se_ranking' => [
            /**
             * Request Timeout
             * number of seconds scraping request will wait for a response (0 = indefinetly)
             */
            'timeout' => 60,
        ],
        'serp_api' => [
            /**
             * Cache Timeout
             * number of HOURS cached results are valid
             */
            'cache_timeout' => 1 * 24 * 1000, // TODO remove this !!!
            /**
             * Request Timeout
             * number of seconds scraping request will wait for a response (0 = indefinetly)
             */
            'timeout' => 60,
        ],
        'rank_my_addr' => [
            /**
             * Cache Timeout
             * number of HOURS cached results are valid
             */
            'cache_timeout' => 1 * 24,
            /**
             * Request Timeout
             * number of seconds scraping request will wait for a response (0 = indefinetly)
             */
            'timeout' => 60,
            /**
             * Sleep Between Requests
             * number of seconds a request will be delayed after the previous one
             */
            'sleep' => 1,
        ],
        'scraping' => [
            /**
             * Cache Timeout
             * number of HOURS cached results are valid
             */
            'cache_timeout' => 7 * 24,
            /**
             * Request Timeout
             * number of seconds scraping request will wait for a response (0 = indefinetly)
             */
            'timeout' => 60,
        ],
    ],

];
