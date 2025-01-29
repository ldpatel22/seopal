<?php

namespace App\Jobs\OsrReport;

use App\Jobs\ReportJobStage;
use App\Models\ApiCall;
use App\Models\OsrDomain;
use App\Models\OsrLandingPage;
use App\Models\Report;
use App\Repositories\DomainBacklinksOverviewStatsRepository;
use App\Repositories\UrlBacklinksOverviewStatsRepository;
use App\Services\SerpApiService;
use App\Services\SemrushApiService;
use App\Services\SemrushApiV3Service;


class RetrieveSearchResultsJob extends ReportJobStage
{
/**
* Create a new job instance.
*
* @param Report $report
* @return void
*/
public function __construct($report)
{
parent::__construct($report,'searchresults');
}

private function getSearchResultsWithSemrush(&$domains, &$landingPages)
{
// Generate cache hash
$hash = md5($this->report->device . $this->report->locale . $this->report->keyword->name);

// Check for cached API response
$apiCall = ApiCall::fromCache('semrush_OrganicSearchResults', $hash, config('reports.osr.semrush.cache_timeout'));
  
if ($apiCall && $apiCall->hasResponse() && @$apiCall->getResponse()->body) {
$this->log("Getting Serpstat search results for keyword {$this->report->keyword->name}, locale {$this->report->locale} from cache.");
 
  return json_decode(@$apiCall->getResponse()->body);
} else {
// If no cache, store the new API call in cache
$apiCall = ApiCall::cache('semrush_OrganicSearchResults', $hash, [
'keyword' => $this->report->keyword->name,
'locale' => $this->report->locale
]);
  
}
//dd($this->report->keyword->name,$this->report->locale);
// Call Serpstat API to get search results
$this->log("Calling Serpstat for search results for keyword {$this->report->keyword->name}, locale {$this->report->locale}");
$searchResults = SemrushApiV3Service::getOrganicSearchResultsForKeyword($this->report->keyword->name, $this->report->locale);
$this->log('Got search results from SerpstatApi');
//dd("search",$searchResults);
// Cache the results from Serpstat
$apiCall->response = json_encode($searchResults);
$apiCall->save();

// Process domain results from Serpstat API response
if (isset($searchResults->result->data)) {
foreach ($searchResults->result->data as $domainData) {
$this->processDomainResult($domainData, $domains, $landingPages);
}
}
}

/**
* Process a domain result from Serpstat.
*
* @param object $domainData
* @param array $domains
* @param array $landingPages
* @return void
*/
private function processDomainResult($domainData, &$domains, &$landingPages)
{
// Create or find the domain for the result
$osrDomain = OsrDomain::where(['report_id' => $this->report->id, 'name' => $domainData->domain])->first();
if (!$osrDomain) {
$osrDomain = new OsrDomain([
'report_id' => $this->report->id,
'name' => $domainData->domain,
//'traffic' => $domainData->traff,  // Save traffic from Serpstat
//'keywords' => $domainData->keywords,  // Save keywords count
//'visible' => $domainData->visible,  // Save visibility
]);
$osrDomain->save();
$domains[] = $osrDomain;
}

// Add or update the landing page (link) associated with this domain
// Note: Depending on your data model, you might want to link to specific pages or just keep domain-level data.
$osrLandingPage = new OsrLandingPage([
'report_id' => $this->report->id,
'domain_id' => $osrDomain->id,
'url' => $domainData->domain,  // You can adjust this if you have specific landing page URLs
]);
$osrLandingPage->save();
$landingPages[] = $osrLandingPage;
}

/**
* Performs the job to retrieve and process search results.
*
* @throws \Exception
* @return void
*/
protected function perform()
{
$domains = [];
$landingPages = [];

// Retrieve and process search results from Serpstat
$this->getSearchResultsWithSemrush($domains, $landingPages);
   
$this->log('Created ' . count($landingPages) . ' landing page(s) and ' . count($domains) . ' domains.');

// Fetch domain stats (backlinks) for each domain
$this->log('Fetching domain backlinks from SerpstatApi.');
$domainsRepo = new DomainBacklinksOverviewStatsRepository();
$stats = $domainsRepo->get(array_map(function ($osrDomain) { return $osrDomain->name; }, $domains));
$this->log('Finished fetching domain backlinks from SerpstatApi.');
 
// Save domain backlinks data
$this->log('Saving domain backlinks data.');
foreach ($domains as $osrDomain) { /** @var OsrDomain $osrDomain */
$stat = $stats->first(function ($stat) use ($osrDomain) { return $osrDomain->name == $stat->entity; });
if ($stat) {
$data = $stat->getData();
   
$osrDomain->auth_score = @$data->authority_score;
$osrDomain->backlinks = @$data->total;
$osrDomain->backlinks_domains = @$data->referring_domains;
$osrDomain->backlinks_urls = @$data->referring_urls;
$osrDomain->save();
}
}
$this->log('Finished saving domain backlinks data.');

// Fetch and save landing page stats (backlinks)
$this->log('Fetching landing page backlinks from SerpstatApi.');
$urlsRepo = new UrlBacklinksOverviewStatsRepository();
$stats = $urlsRepo->get(array_map(function ($osrLandingPage) { return $osrLandingPage->url; }, $landingPages));
$this->log('Finished fetching landing page backlinks from SerpstatApi.');

$this->log('Saving landing page backlinks data.');
foreach ($landingPages as $osrLandingPage) { /** @var OsrLandingPage $osrLandingPage */
$stat = $stats->first(function ($stat) use ($osrLandingPage) { return $osrLandingPage->url == $stat->entity; });
if ($stat) {
$data = $stat->getData();
$osrLandingPage->auth_score = $data->authority_score;
$osrLandingPage->backlinks = $data->total;
$osrLandingPage->backlinks_domains = $data->referring_domains;
$osrLandingPage->backlinks_urls = $data->referring_urls;
$osrLandingPage->save();
}
}
$this->log('Finished saving landing page backlinks data.');
}
}
