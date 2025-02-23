<?php

namespace App\Services;

use App\Models\ApiCall;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


class SemrushApiService
{
    const API_ENDPOINT = 'https://api.serpstat.com/v4/'; // Serpstat API endpoint

    /**
     * Forms request and runs it, returns response interface
     *
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function request($params)
    {
        $client = new Client();
        return $client->request('POST', self::API_ENDPOINT, [
            'json' => $params, // Send params as JSON body
            'verify' => false,  // disable SSL certificate check (if needed)
            'connect_timeout' => config('reports.osr.serp_api.timeout'),
        ]);
    }
  
  public static function getBacklinksOverviewForDomain($domain, $skipCache = false)
{
$params = [
'query' => urlencode($domain),
'token' => env('SERPSTAT_API_TOKEN'), // Your Serpstat API token
];



$hash = md5($domain);
$apiCall = $skipCache ? null : ApiCall::fromCache('serpstat', $hash, config('reports.backlinks.serpstat_api.cache_timeout'));



if ($apiCall && $apiCall->hasResponse()) {
$response = $apiCall->getResponse();
if (isset($response->exception)) {
throw new \Exception('Serpstat API Exception: ' . $response->exception);
}
$status = $response->status;
$body = $response->body;
} else {
$apiCall = new ApiCall([
'api' => 'serpstat',
'hash' => md5($domain),
'request' => json_encode(['domain' => $domain])
]);
$apiCall->save();



try {
$response = self::makeRequest($params);
} catch (GuzzleException $e) {
$apiCall->response = json_encode([
'exception' => $e->getMessage(),
'trace' => $e->getTraceAsString()
]);
$apiCall->save();
throw new \Exception('Serpstat API Guzzle Exception: ' . $e->getMessage());
}



$status = $response->getStatusCode();
$body = $response->getBody();



$apiCall->response = json_encode(['status' => $status, 'body' => (string) $body]);
$apiCall->save();
}



if ($status != 200) {
throw new \Exception('Serpstat failure: ' . $response->getStatusCode());
}



$data = json_decode($body);
if ($data == null) {
throw new \Exception('Serpstat cannot decode data: ' . $response->getStatusCode());
}



return $data;
}

    /**
     * Serpstat > Organic Competitors API
     * @see https://serpstat.com/api/
     *
     * @param string $domain
     * @param string $locale
     * @param bool $skipCache
     * @return array
     * @throws \Exception
     */
    public static function getOrganicCompetitorsReport($domain, $locale = 'us', $skipCache = false)
    {
        $params = [
            'id' => '1', // Request ID
            'method' => 'SerpstatDomainProcedure.getOrganicCompetitorsPage', // Serpstat method
            'params' => [
                'domain' => $domain,
                'se' => 'g_' . $locale, // Search engine (Google locale)
                'sort' => ['relevance' => 'desc'], // Sorting by relevance
                'page' => 1,
                'size' => 50
            ]
        ];

        // Generate unique hash for cache based on domain and locale
        $hash = md5($domain . $locale);
        $apiCall = $skipCache ? null : ApiCall::fromCache('serpstat', $hash, config('reports.osr.serp_api.cache_timeout'));

        if ($apiCall && $apiCall->hasResponse()) {
            $response = $apiCall->getResponse();
            if (isset($response->exception)) {
                throw new \Exception('Serpstat Guzzle Exception: ' . $response->exception);
            }
            $status = $response->status;
            $body = $response->body;
        } else {
            // Save the request to cache
            $apiCall = new ApiCall([
                'api' => 'serpstat',
                'hash' => md5($domain . $locale),
                'request' => json_encode(['domain' => $domain, 'locale' => $locale])
            ]);
            $apiCall->save();

            try {
                // Make the POST request to Serpstat API
                $response = self::request($params);
            } catch (GuzzleException $e) {
                $apiCall->response = json_encode([
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $apiCall->save();
                throw new \Exception('Serpstat Guzzle Exception: ' . $e->getMessage());
            }

            $status = $response->getStatusCode();
            $body = $response->getBody();

            $apiCall->response = json_encode(['status' => $status, 'body' => (string)$body]);
            $apiCall->save();
        }

        if ($status != 200) {
            throw new \Exception('Serpstat failure: ' . $response->getStatusCode());
        }

        $data = json_decode($body);
        if ($data == null) {
            throw new \Exception('Serpstat cannot decode data: ' . $response->getStatusCode());
        }

        return $data;
    }

    public static function getOrganicSearchResultsForKeyword($keyword = "", $locale = 'us', $skipCache = false)
    {
        // Set up search parameters for Serpstat API
        $params = [
            'id' => '1',
            'method' => 'SerpstatKeywordProcedure.getCompetitors',  // Use Serpstat's method for competitors
            'params' => [
                'keyword' => $keyword,  // The keyword to search for
                'se' => 'g_' . $locale, // Search engine locale (e.g., 'g_us' for Google US)
                'size' => 10,  // The number of results per page (can be adjusted)
            ]
        ];

        // Generate a hash based on the keyword and locale to use for caching
        $hash = md5($keyword . $locale);
        $apiCall = $skipCache ? null : ApiCall::fromCache('serpstat', $hash, config('reports.osr.serp_api.cache_timeout'));

        if ($apiCall && $apiCall->hasResponse()) {
            $response = $apiCall->getResponse();
            if (isset($response->exception)) {
                throw new \Exception('Serpstat Guzzle Exception: ' . $response->exception);
            }
            $status = $response->status;
            $body = $response->body;
        } else {
            $apiCall = new ApiCall([
                'api' => 'serpstat',
                'hash' => md5($keyword . $locale),
                'request' => json_encode(['keyword' => $keyword, 'locale' => $locale])
            ]);
            $apiCall->save();

            try {
                // Make the API request
                $response = self::request($params);
            } catch (GuzzleException $e) {
                $apiCall->response = json_encode([
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $apiCall->save();
                throw new \Exception('Serpstat Guzzle Exception: ' . $e->getMessage());
            }

            // Get status and body from response
            $status = $response->getStatusCode();
            $body = $response->getBody();

            // Save the response in the cache
            $apiCall->response = json_encode(['status' => $status, 'body' => (string)$body]);
            $apiCall->save();
        }

        // Check if the request was successful
        if ($status != 200) {
            throw new \Exception('Serpstat failure: ' . $response->getStatusCode());
        }

        // Decode the response body
        $data = json_decode($body);
        if ($data == null) {
            throw new \Exception('Serpstat cannot decode data: ' . $response->getStatusCode());
        }

        return $data;
    }



    /**
     * Get Organic Keyword Overview for a Domain
     * @param string $keywords
     * @param string $locale
     * @param string $url
     * @param bool $skipCache
     * @return array
     * @throws \Exception
     */
    public static function getKeywordsOverviewForDomain($keywords = "", $locale = 'us', $url, $skipCache = false)
    {
        // Set up search parameters for Serpstat API
        $params = [
            'id' => '1',
            'method' => 'SerpstatDomainProcedure.getKeywordsOverview', // Use Serpstat's method
            'params' => [
                'domain' => $url,
                'se' => 'g_' . $locale,
                'keywords' => $keywords,  // The keywords to search for
                'page' => 1,
                'size' => 50
            ]
        ];

        $hash = md5($keywords . $locale . $url);
        $apiCall = $skipCache ? null : ApiCall::fromCache('serpstat', $hash, config('reports.osr.serp_api.cache_timeout'));

        if ($apiCall && $apiCall->hasResponse()) {
            $response = $apiCall->getResponse();
            if (isset($response->exception)) {
                throw new \Exception('Serpstat Guzzle Exception: ' . $response->exception);
            }
            $status = $response->status;
            $body = $response->body;
        } else {
            $apiCall = new ApiCall([
                'api' => 'serpstat',
                'hash' => md5($keywords . $locale . $url),
                'request' => json_encode(['keywords' => $keywords, 'locale' => $locale, 'url' => $url])
            ]);
            $apiCall->save();

            try {
                $response = self::request($params);
            } catch (GuzzleException $e) {
                $apiCall->response = json_encode([
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $apiCall->save();
                throw new \Exception('Serpstat Guzzle Exception: ' . $e->getMessage());
            }

            $status = $response->getStatusCode();
            $body = $response->getBody();

            $apiCall->response = json_encode(['status' => $status, 'body' => (string)$body]);
            $apiCall->save();
        }

        if ($status != 200) {
            throw new \Exception('Serpstat failure: ' . $response->getStatusCode());
        }

        $data = json_decode($body);
        if ($data == null) {
            throw new \Exception('Serpstat cannot decode data: ' . $response->getStatusCode());
        }

        return $data;
    }

    // Additional methods for Serpstat can be added here...

}
