<?php

namespace App\Jobs\OsrReport;

use App\Jobs\ReportJobStage;
use App\Models\ApiCall;
use App\Models\OsrDomain;
use App\Models\OsrLandingPage;
use App\Models\Report;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\CssSelector\CssSelectorConverter;

class ScrapeLandingPagesJob extends ReportJobStage
{
    /**
     * Create a new job instance.
     *
     * @param Report $report
     * @return void
     */
    public function __construct($report)
    {
        parent::__construct($report,'scraping');
    }

    /**
     * Scrapes given URL and returns the information
     *
     * @param string $url
     * @param array $info
     * @param boolean $skipCache
     *
     * @throws \Exception
     * @return array
     */
    private function scrapeUrl($url, $skipCache = false)
    {
        $hash = md5($url);
        $apiCall = $skipCache ? null : ApiCall::fromCache('scraper',$hash,config('reports.osr.scraping.cache_timeout'));
        if($apiCall && $apiCall->hasResponse()) {
            return $apiCall->getResponse(true);
        }
 
        $apiCall = new ApiCall([ 
            'api' => 'scraper',
            'hash' => $hash,
            'request' => json_encode(['url' => $url])
        ]); $apiCall->save();
		$data = [];

        try {
            $client = new Client(HttpClient::create([
                'verify_peer' => false,
                'verify_host' => false
            ]));
            $this->log("Scraping url {$url}");
            $crawler = $client->request('GET', $url, [
                'allow_redirects' => true, // make it possible for landing page to redirect
                'verify' => false, // disable SSL certificate check
                'connect_timeout' => config('reports.osr.scraping.timeout'),
                'synchronous' => true,
            ]);
        } catch (\Exception $e) {
          $data = [];
             $data['html'] ='Error scraping ' . $url . ' :'  . $e->getMessage();
                      $data['title']="";
                      $data['description']="";
                      $data['wordCount']=0;
          return $data;
            throw new \Exception('Error scraping ' . $url . ' :'  . $e->getMessage());
        }

        $pageHost = parse_url($url)['host'];
        
        # region processing

        // html
        
          

        // title
        {
            // <title></title>
            $title = null;
            $crawler->filter('title')->each(function ($node) use (&$title) {
                $title = $node->text();
            });
            $data['title'] = $title;
        }

        // description
        {
            // <meta name="description" content="" />
            $description = null;
            $crawler->filter('meta[name="description"],meta[name="Description"]')->each(function ($node) use (&$description) {
                $description = $node->attr('content');
            });
            $data['description'] = $description;
        }

        // preview image
        {
            // <meta property="og:image" content="">
            // <meta name="twitter:image" content="">
            $previewImage = null;
            $crawler->filter('meta[property="og:image"]')->each(function ($node) use (&$previewImage) {
                $previewImage = $node->attr('content');
            });
            if(!$previewImage) $crawler->filter('meta[name="twitter:image"]')->each(function ($node) use (&$previewImage) {
                $previewImage = $node->attr('content');
            });
            $data['previewImage'] = $previewImage;
        }

        // titles
        {
            $titles = [];
            //387: Change to only h1 and h2, but reverted back to using from h1 to h6
            $crawler->filter('h1,h2,h3,h4,h5,h6')->each(function ($node) use (&$titles) {
                $titles[] = [
                    'level' => intval(str_replace('h','',$node->nodeName())),
                    'text' => $node->text(),
                ];
            });
            $data['titles'] = $titles;
        }

        // images
        {
            // <img src="" alt="" />
            $images = [];
            $crawler->filter('img')->each(function ($node) use (&$images) {
                $image = [];
                $image['src'] = $node->attr('src');
                $image['alt'] = $node->attr('alt');
                $images[] = $image;
            });
            $data['images'] = $images;
        }

        // videos
        {
            // <img src="" alt="" />
            $videos = [];
            $crawler->filter('video')->each(function ($node) use (&$videos) { /** @var Crawler $node */
                if($node->attr('src')) {
                    $videos[] = ['src' => $node->attr('src')];
                } else {
                    $node->children('source')->each(function($child) use (&$videos){
                        if($child->attr('src')) {
                            $videos[] = ['src' => $child->attr('src')];
                        }
                    });
                }
            });
            $data['videos'] = $videos;
        }

        // links
        {
            // <a>label</a>
            $links = [];
            $crawler->filter('a')->each(function ($node) use (&$links, $pageHost) {
                $href = $node->attr('href');
                $info = $href ? parse_url($href) : [];
                $host = isset($info['host']) ? $info['host'] : null;
                $scheme = isset($info['scheme']) ? $info['scheme'] : null;
                $hash = isset($info['fragment']) ? $info['fragment'] : null;

                $link = [];
                $link['href'] = $href;
                $link['host'] = $host;
                $link['scheme'] = $scheme;
                $link['hash'] = $hash;
                $link['title'] = $node->attr('title');
                $link['text'] = $node->text();
                $link['external'] = $host && ($host != $pageHost);

                // Get the parent element information
                $parentNode = $node->getNode(0)->parentNode;
                $link['parent_tag'] = $parentNode->nodeName;

                $links[] = $link;
            });
            $data['links'] = $links;
        }

        // word count
        {
            $wordCount = 0;
            $crawler->filter('body')->each(function ($node) use (&$wordCount) {
                $html = $node->html();
                $search = [
                    '@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                    '@<style[^>]*?>.*?</style>@siU',    // strip style tags
                    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
                ];
                $content = preg_replace($search, '', $html);
                $wordCount = str_word_count(strip_tags(strtolower($content)));
            });
            $data['wordCount'] = $wordCount;
        }

        # endregion

        $apiCall->response = json_encode($data);
        $apiCall->save();

        return $data;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     * @return void
     */
    protected function perform()
    {
        $domains = OsrDomain::whereReportId($this->report->id)->get();
        foreach($domains as $domain) {
            /** @var OsrDomain $domain */
            $data = $this->scrapeUrl('http://' . $domain->name);
            $domain->word_count = $data['wordCount'];
            $domain->save();

            $landingPages = OsrLandingPage::whereDomainId($domain->id)->get();
            foreach($landingPages as $landingPage) {
                /** @var OsrLandingPage $landingPage */
                $data = $this->scrapeUrl($landingPage->url);
                if(is_array($data)) {
                    $landingPage->html = $data['html'];
                    $landingPage->title = $data['title'];
                    $landingPage->description = $data['description'];
                    $landingPage->word_count = $data['wordCount'];
                    foreach(['html','title','description','wordCount'] as $key) unset($data[$key]);
                }
                $landingPage->data = json_encode($data); // JSON_UNESCAPED_UNICODE
                $landingPage->save();
            }
        }
    }
}
