<?php

namespace App\Services;

use App\Models\ApiCall;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SemrushApiV3Service
{
    const API_ENDPOINT = 'https://api.serpstat.com/v4/'; // Serpstat API endpoint

    /**
     * Your Serpstat API Token
     */
    const API_TOKEN = '24bb669dc4c680d3deb32000ec445127'; // Replace with your actual token

    /**
     * Forms request and runs it, returns response interface
     *
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    // Make sure to define your API token constant

    public static function request($params)
    {
        // Build the request URL with the token as a query parameter
        $url = self::API_ENDPOINT . '?token=' . self::API_TOKEN;

        $client = new Client();

        try {
            // Send the API request
            $response = $client->request('POST', $url, [
                'json' => $params, // Send params as JSON body
                'verify' => false,  // Disable SSL certificate check (if needed)
                'connect_timeout' => config('reports.osr.serp_api.timeout'),
            ]);
        } catch (GuzzleException $e) {
            // Handle any errors during the request
            throw new \Exception('Serpstat Guzzle Exception: ' . $e->getMessage());
        }

        return $response;
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
}
