<?php

namespace App\Repositories;

use App\Models\Keyword;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Jobs\StartOsrReportJob;

class ReportsRepository {

/**
* Prepares report data by calling Serpstat API for organic search results
*
* @param Keyword $keyword
* @param string $type
* @param string $locale
* @param string $device
*
* @return array
* @throws \Exception
*/
private function prepareReportData($keyword, $type, $locale, $device)
{
// Initialize base data structure
$data = [
'keyword' => $keyword->name,
'locale' => $locale,
'device' => $device,
];

// Handle report type
switch ($type) {
case Report::TYPE_ORGANIC_SEARCH_RESULTS:
try {
// Log the keyword and locale parameters being used
Log::info("Fetching organic search results for keyword: {$keyword->name}, locale: {$locale}");

// Prepare the request payload for Serpstat API
$params = [
'id' => '1',
'method' => 'SerpstatKeywordProcedure.getCompetitors',
'params' => [
'keyword' => $keyword->name,  // The keyword to search for
'se' => 'g_' . $locale,  // Search engine locale (e.g., 'g_ba' for Google Bosnia)
'size' => 10,  // Number of results to fetch
]
];

// Initialize Guzzle client
$client = new Client();

// Prepare the request URL with the token
$url = 'https://api.serpstat.com/v4/?token=24bb669dc4c680d3deb32000ec445127';

// Send the request with Guzzle
$response = $client->post($url, [
'json' => $params,  // Send the params as JSON
]);

// Check if we received a valid response
if ($response->getStatusCode() != 200) {
Log::error("API request failed with status code: " . $response->getStatusCode());
throw new \Exception('Serpstat API request failed with status code: ' . $response->getStatusCode());
}

// Decode the response body
$responseBody = $response->getBody();

$responseData = json_decode($responseBody);
  
// Log the decoded response for debugging
Log::info("Decoded Serpstat API response: " . print_r($responseData, true));

// Check if the data exists in the response
if ($responseData->result->data) {
// Extract competitor data from Serpstat response
$competitorsData = [];
foreach ($responseData->result->data as $domain => $domainData) {
$competitorsData[] = [
'domain' => $domain,
'visible' => $domainData->visible ?? 0,
'keywords' => $domainData->keywords ?? 0,
'traffic' => $domainData->traff ?? 0,
'relevance' => $domainData->relevance ?? 0,
'new_keywords' => $domainData->new_keywords ?? 0,
'out_keywords' => $domainData->out_keywords ?? 0,
'rised_keywords' => $domainData->rised_keywords ?? 0,
'down_keywords' => $domainData->down_keywords ?? 0,
];
}

// Merge Serpstat data into the report data
$data = array_merge($data, [
'results_count' => count($competitorsData),  // Number of competitors
'competitors' => $competitorsData,  // List of competitors
'stages' => [
'searchresults' => 0,
'scraping' => 0,
'analysing' => 0,
'backlinks' => -2, // Placeholder for backlinks if needed
]
]);
} else {
Log::error("No data found in Serpstat API response for keyword: {$keyword->name}, locale: {$locale}");
throw new \Exception('No data returned from Serpstat for keyword: ' . $keyword->name);
}

} catch (\Exception $e) {
// Log any exceptions during the fetch process
  $data['error'] = $e->getMessage();
  //return $data;
Log::error("Error fetching organic search results from Serpstat: " . $e->getMessage());
throw new \Exception('Error fetching organic search results from Serpstat: ' . $e->getMessage());
}
break;

default:
// Log the invalid report type
Log::error("Invalid report type: {$type}");
// Throw exception if an invalid report type is provided
throw new \Exception('Invalid report type: ' . $type);
}

return $data;
}

/**
* Creates a new report and prepares it for running
*
* @param Keyword $keyword
* @param string $type
* @param string $locale
* @param string $device
*
* @return Report
* @throws \Exception
*/
    public function prepareNewReport($keyword, $type, $locale, $device)
    {
        $data = $this->prepareReportData($keyword, $type, $locale, $device);

        $report = new Report([
            'keyword_id' => $keyword->id,
            'type' => $type,
            'locale' => $locale,
            'device' => $device,
            'status' => Report::STATUS_SCHEDULED,
            'data' => $data,
            'user_id' => user()->id
        ]);

        // Log report before saving to check if the data is valid
        Log::info("Preparing to save report: " . print_r($report->toArray(), true));

        if (!$report->save()) {
            Log::error("Failed to save report to database.");
            throw new \Exception('Unable to create report in the database.');
        }

        // Log the successful save
        Log::info("Report saved successfully with ID: {$report->id}");

        return $report;
    }


/**
* Schedules report for running
*
* @param Report $report
* @throws \Exception
*/
public function scheduleReport(Report $report)
{
switch ($report->type) {
case Report::TYPE_ORGANIC_SEARCH_RESULTS:
StartOsrReportJob::dispatchSync($report);
return;
}

throw new \Exception('Invalid report type: ' . $report->type);
}
}
