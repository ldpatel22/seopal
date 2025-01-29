<?php

namespace App\Repositories;

use App\Models\Keyword;
use App\Models\OsrAlt;
use App\Models\OsrContent;
use App\Models\OsrDomain;
use App\Models\OsrHeading;
use App\Models\OsrKeyword;
use App\Models\OsrLandingPage;
use App\Models\OsrBacklinks;
use App\Models\OsrPhraseLandingPage;
use App\Models\OsrPlannedBacklinks;
use App\Models\OsrLink;
use App\Models\OsrPhrase;
use App\Models\OsrPlannedPhrase;
use App\Models\Report;
use App\Models\ApiCall;
use App\Services\SerpApiService;
use App\Http\Controllers\ChatGPTController;
use DB;

class OsrReportsRepository {

    /**
     * Prepares data for the OSR landing pages report
     *
     * @param Report $report
     * @return array
     */
    public function prepareLandingPagesReportData(Report $report)
    {
        if($report->data['stages']['scraping'] < 2) {
            return null;
        } $data = [];

        $landingPages = OsrLandingPage::whereReportId($report->id)->get();

        $data['maxDomainAuthScore'] = 0; $data['minDomainAuthScore'] = PHP_INT_MAX;
        $data['maxDomainWordCount'] = 0; $data['minDomainWordCount'] = PHP_INT_MAX;
        $data['maxDomainBacklinks'] = 0; $data['minDomainBacklinks'] = PHP_INT_MAX;
        $data['maxPageAuthScore'] = 0; $data['minPageAuthScore'] = PHP_INT_MAX;
        $data['maxPageBacklinks'] = 0; $data['minPageBacklinks'] = PHP_INT_MAX;
        $data['maxPageWordCount'] = 0; $data['minPageWordCount'] = PHP_INT_MAX;

        foreach($landingPages as $landingPage) {
            $domain = $landingPage->domain;
            if($domain->auth_score !== null) {
                if($domain->auth_score > $data['maxDomainAuthScore']) $data['maxDomainAuthScore'] = $domain->auth_score;
                if($domain->auth_score < $data['minDomainAuthScore']) $data['minDomainAuthScore'] = $domain->auth_score;
            }
            if($domain->word_count !== null) {
                if ($domain->word_count > $data['maxDomainWordCount']) $data['maxDomainWordCount'] = $domain->word_count;
                if ($domain->word_count < $data['minDomainWordCount']) $data['minDomainWordCount'] = $domain->word_count;
            }
            if($domain->backlinks !== null) {
                if ($domain->backlinks > $data['maxDomainBacklinks']) $data['maxDomainBacklinks'] = $domain->backlinks;
                if ($domain->backlinks < $data['minDomainBacklinks']) $data['minDomainBacklinks'] = $domain->backlinks;
            }
            if($landingPage->auth_score !== null) {
                if($landingPage->auth_score > $data['maxPageAuthScore']) $data['maxPageAuthScore'] = $landingPage->auth_score;
                if($landingPage->auth_score < $data['minPageAuthScore']) $data['minPageAuthScore'] = $landingPage->auth_score;
            }
            if($landingPage->backlinks !== null) {
                if($landingPage->backlinks > $data['maxPageBacklinks']) $data['maxPageBacklinks'] = $landingPage->backlinks;
                if($landingPage->backlinks < $data['minPageBacklinks']) $data['minPageBacklinks'] = $landingPage->backlinks;
            }
            if($landingPage->word_count !== null) {
                if($landingPage->word_count > $data['maxPageWordCount']) $data['maxPageWordCount'] = $landingPage->word_count;
                if($landingPage->word_count < $data['minPageWordCount']) $data['minPageWordCount'] = $landingPage->word_count;
            }
        }

        /* 387: Get local links */
        $localLinks = OsrDomain::where(['report_id' => $report->id, 'local' => '1'])->get();

        // domain rank switch
//        $maxDomainAuthScore = $data['maxDomainAuthScore'];
//        $data['maxDomainAuthScore'] = $data['minDomainAuthScore'];
//        $data['minDomainAuthScore'] = $maxDomainAuthScore;

        $data['landingPages'] = $landingPages;
        $data['localLinks'] = $localLinks;
        return $data;
    }

    /**
     * Prepares data for the OSR headings report
     *
     * @param Report $report
     * @return array
     */
    public function prepareHeadingsReportData(Report $report)
    {
        if($report->data['stages']['analysing'] < 2) {
            return null;
        } $data = ['rows' => []];

        $landingPages = OsrLandingPage::whereReportId($report->id)->get();
        foreach($landingPages as $landingPage) {
            $headings = OsrHeading::whereLandingPageId($landingPage->id)->orderBy('level')->get();

            $keywordLinks = OsrKeyword::whereIn('heading_id',$headings->map(function(OsrHeading $heading){ return $heading->id; })->toArray())->get();
            $keywords = [];
            foreach ($keywordLinks as $osrKeyword) { /** @var OsrKeyword $osrKeyword */
                $keyword = $osrKeyword->keyword;
                if(!isset($keywords[$keyword->id])) {
                    $keywords[$keyword->id] = ['permalink' => $keyword->permalink(), 'name' => $keyword->name, 'headings' => []];
                }
                $keywords[$keyword->id]['headings'][$osrKeyword->heading_id] = $osrKeyword->index;
            }

            // sort by max headings
            uasort($keywords, function($x,$y){
                $a = count($x['headings']);
                $b = count($y['headings']);
                if ($a == $b) return 0;
                return ($a > $b) ? -1 : 1;
            });

            $row = [
                'landingPage' => $landingPage,
                'headings' => $headings,
                'keywords' => $keywords,
            ];
            $data['rows'][] = $row;
        }
        return $data;
    }

    /**
     * Prepares data for the OSR links report
     *
     * @param Report $report
     * @return array
     */
    public function prepareLinksReportData(Report $report)
    {
        // Return null if the report stage is not sufficient
        if ($report->data['stages']['analysing'] < 2) {
            return null;
        }

        $data = ['rows' => []];

        // Get all landing pages for the report
        $landingPages = OsrLandingPage::whereReportId($report->id)
            ->with(['links.keywords']) // Eager load related links and keywords
            ->get();

        foreach ($landingPages as $landingPage) {
            // Prepare an associative array for keywords
            $keywords = $this->getKeywordsForLandingPage($landingPage);

            // Sort keywords by the number of links
            uasort($keywords, function ($x, $y) {
                return count($y['links']) - count($x['links']);
            });

            $data['rows'][] = [
                'landingPage' => $landingPage,
                'links' => $landingPage->links,
                'keywords' => $keywords,
            ];
        }

        return $data;
    }

    /**
     * Prepares data for the OSR alts report
     *
     * @param Report $report
     * @return array
     */
    public function prepareAltsReportData(Report $report)
    {
        if($report->data['stages']['analysing'] < 2) {
            return null;
        } $data = ['rows' => []];

        $landingPages = OsrLandingPage::whereReportId($report->id)->get();
        foreach($landingPages as $landingPage) {
            $alts = OsrAlt::whereLandingPageId($landingPage->id)->get();

            $keywordLinks = OsrKeyword::whereIn('alt_id',$alts->map(function(OsrAlt $alt){ return $alt->id; })->toArray())->get();
            $keywords = [];
            foreach ($keywordLinks as $osrKeyword) { /** @var OsrKeyword $osrKeyword */
                $keyword = $osrKeyword->keyword;
                if(!isset($keywords[$keyword->id])) {
                    $keywords[$keyword->id] = ['permalink' => $keyword->permalink(), 'name' => $keyword->name, 'alts' => []];
                }
                $keywords[$keyword->id]['alts'][$osrKeyword->alt_id] = $osrKeyword->index;
            }

            // sort by max headings
            uasort($keywords, function($x,$y){
                $a = count($x['alts']);
                $b = count($y['alts']);
                if ($a == $b) return 0;
                return ($a > $b) ? -1 : 1;
            });

            $row = [
                'landingPage' => $landingPage,
                'alts' => $alts,
                'keywords' => $keywords,
            ];
            $data['rows'][] = $row;
        }
        return $data;
    }

    /**
     * Prepares data for the OSR keywords report
     *
     * @param Report $report
     * @return array
     */
    /**
     * Prepare the report data for keywords.
     *
     * @param Report $report
     * @return array|null
     */
    public function prepareKeywordsReportData(Report $report)
    {
        if ($report->data['stages']['analysing'] < 2) {
            return null;
        }

        $data = ['rows' => []];

        // Retrieve unique keyword IDs for the given report
        $ids = DB::table('reports_osr_keywords')
            ->where('report_id', $report->id)
            ->distinct()
            ->pluck('keyword_id')
            ->toArray();

        // Retrieve keywords with eager loading for their associated data
        $keywords = project()->keywords()->with('osrKeywords.heading.landingPage')->whereIn('id', $ids)->get();

        foreach ($keywords as $keyword) {
            /** @var Keyword $keyword */
            $row = [
                'name' => $keyword->name,
                'permalink' => $keyword->permalink(),
                'pages' => [],
                'h1' => [],
                'h2' => [],
                'h3' => [],
                'h4' => [],
                'h5' => [],
                'h6' => [],
            ];

            foreach ($keyword->osrKeywords as $osrKeyword) {
                /** @var OsrKeyword $osrKeyword */
                $heading = $osrKeyword->heading;

                if (!isset($row['pages'][$heading->landing_page_id])) {
                    $row['pages'][$heading->landing_page_id] = [
                        'count' => 0,
                        'url' => $heading->landingPage->url,
                    ];
                }
                $row['pages'][$heading->landing_page_id]['count']++;

                $index = $osrKeyword->index;
                $headingText = $heading->name;
                $keywordLength = strlen($osrKeyword->name);
                $textLength = strlen($headingText);

                // Build HTML for highlighted heading text
                if ($index === 0) {
                    $html = $textLength === $keywordLength
                        ? "<span class='ui primary text'>{$headingText}</span>"
                        : "<span class='ui primary text'>" . substr($headingText, 0, $keywordLength) . "</span>" . substr($headingText, $keywordLength);
                } else {
                    $html = substr($headingText, 0, $index) .
                        "<span class='ui primary text'>" . substr($headingText, $index, $keywordLength) . "</span>" .
                        substr($headingText, $index + $keywordLength);
                }
                $row['h' . $heading->level][] = $html;
            }

            $data['rows'][] = $row;
        }

        return $data;
    }

    /**
     * Prepares data for the OSR backlinks report
     *
     * @param Report $report
     * @return array
     */
    public function prepareBacklinksReportData(Report $report){
        /* 387: Return nothing on inital load */
        $data['backlinks'] = "";
        return $data;
    }

    /**
     * Gets the keywords for a specific landing page
     *
     * @param OsrLandingPage $landingPage
     * @return array
     */
    private function getKeywordsOverviewForLandingPage(OsrLandingPage $landingPage) {
        $report = $landingPage->report;

        $hash = md5($report->data['keyword'] . $report->locale . $landingPage->url .  "KEYWORDS_OVERVIEW_API_CALL");
        $apiCall = ApiCall::fromCache('semrushv3_getKeywordsOverviewForDomain', $hash, config('reports.osr.semrush.cache_timeout'));

        if ($apiCall && $apiCall->hasResponse()) {
            return $apiCall->getResponse(true);
        }

        $data = SerpApiService::getKeywordsOverviewForLocale(
            $report->keyword->name,
            $report->locale,
        );


       // $apiCall = ApiCall::cache('semrushv3_getKeywordsOverviewForDomain', $hash, ['keyword' => $report->keyword->name, 'locale' => $report->locale, 'url' => $landingPage->url]);
        $apiCall->response = json_encode($data);
        $apiCall->save();

        return $data;
    }

    /**
     * Prepares data for the OSR phrases report
     *
     * @param Report $report
     * @return array
     */
    public function preparePhrasesReportData(Report $report)
    {
        if($report->data['stages']['analysing'] < 2) {
            return null;
        }

        $landingPages = OsrLandingPage::whereReportId($report->id)->get();
        $phrases = OsrPhrase::whereReportId($report->id)->get();

        $phrase_keywords = [];

        if($phrases->isEmpty()) {
            $keywords = [];

            foreach($landingPages as $landingPage) {
               
                $relatedKeywords = array_map(function ($item) {
                    if(is_array($item) && $item['keyword']) return $item['keyword'];
                    $item->keyword;
                }, $this->getKeywordsOverviewForLandingPage($landingPage));

                foreach($relatedKeywords as $keyword) {
                    if(!array_key_exists($keyword, $keywords)) {
                        $keywords[$keyword] = [$landingPage];
                    } else {
                        $keywords[$keyword][] = $landingPage;
                    }
                }
            }

            uasort($keywords, function ($x,$y) {
                return count($y) <=> count($x); // reversed to get top keyword usages first
            });

            // store phrases in the database
            foreach($keywords as $keyword => $landingPages) {
                if(count($landingPages) < 2) continue;

                // already in the database?
                $phrase = OsrPhrase::where(['name' => $keyword, 'report_id' => $report->id])->first();
                if($phrase) {
                    // clear all links with landing pages just in case
                    //$phrase->landingPages()->detach();
                    //OsrPhraseLandingPage::where
                } else {
                    $phrase = new OsrPhrase(['name' => $keyword, 'report_id' => $report->id]);
                }

                $phrase->usage = count($landingPages);
                $phrase->save();

                // link landing pages
                foreach($landingPages as $landingPage) {
                    $link = new OsrPhraseLandingPage(['phrase_id' => $phrase->id,'landing_page_id' => $landingPage->id]);
                    $link->save();
                }

                $phrase_keywords[] = $phrase;
            }
        } else {
            $phrase_keywords = $phrases;
        }

        return ['phrases' => $phrase_keywords];
    }

    /**
     * Prepares data for the OSR planner distribution report
     *
     * @param Report $report
     * @return array
     */
    public function preparePlannerReportData(Report $report)
    {
        if($report->data['stages']['analysing'] < 2) {
            return null;
        }

        $phrases = OsrPlannedPhrase::whereReportId($report->id)->get();
        $backlinks_domain = OsrPlannedBacklinks::where(["report_id" => $report->id, "type" => "domain"])->get();

        foreach($backlinks_domain as &$backlink){

            $backlink_data = OsrBacklinks::find($backlink->backlink_id);
            $backlink->anchor = $backlink_data->anchor;
            $backlink->auth_score = $backlink_data->auth_score;
            $backlink->nofollow = $backlink_data->nofollow;

        }

        $backlinks_landing = OsrPlannedBacklinks::where(["report_id" => $report->id, "type" => "landing"])->get();

        foreach($backlinks_landing as &$backlink){

            $backlink_data = OsrBacklinks::find($backlink->backlink_id);
            $backlink->anchor = $backlink_data->anchor;
            $backlink->auth_score = $backlink_data->auth_score;
            $backlink->nofollow = $backlink_data->nofollow;

        }

        $landingPages = OsrLandingPage::whereReportId($report->id)->get();

        $words_sum = 0;
        $words_count = 0;
        $auth_sum = 0;
        $landing_view = [];

        $i = 0;
        foreach($landingPages as $landingPage){
            if($i < 3){

                $auth_sum += $landingPage->domain->auth_score;
                $landing_view[] = $landingPage;

            }

            $words_sum += $landingPage->word_count;
            $words_count++;
            $i++;
        }

        $word_count = round( $words_sum / $words_count, 0 );
        $auth_count = round( $auth_sum / 3, 0 );

        return ['phrases' => $phrases, 'backlinks_domain' => $backlinks_domain, 'backlinks_landing' => $backlinks_landing, 'word_count' => $word_count, 'auth_count' => $auth_count, 'landing_pages' => $landing_view];
    }

    /**
     * Fetches content titles
     *
     * @param Report $report
     * @return array|object|null
     */
    private function getContentTitles(Report $report) {

        $landingPages = OsrLandingPage::whereReportId($report->id)->get();
        $titles = [];

        // Get the first/important title from every landing page and results
        foreach($landingPages as $landingPage) {
            $titles[] = "\"" . $landingPage->title . "\"";
        }
        $titles = implode(", ",$titles);

        // Get from cache
        $hash = md5($report->device . $report->locale . $report->keyword->name . $titles);
        $apiCall = ApiCall::fromCache('chatgpt_v1', $hash, config('reports.osr.semrush.cache_timeout'));
        if($apiCall && $apiCall->hasResponse()) {
            $data = $apiCall->getResponse(true);
            return $data;
        }

        $data['titles'] = "";
        $data['keyword'] = $report->keyword->name;
        $data['locale'] = $report->locale;

        $chatGPT = new ChatGPTController();
        $prompt = implode(" ", [
            "You are an SEO copywriter.",
            "You’re helping me draft titles for a website landing page. Your task is to generate 5 titles based on the following instructions:",
            "* Titles must include the keyword \"{$data['keyword']}\".",
            "* Titles must be inspired by these titles: {$titles}.",
            "* Titles must be written in the language that corresponds to this locale: \"{$data['locale']}\".",
            "* Titles must not have more than 60 characters.",
            "* Titles must not include brand names.",
            "* Titles must be SEO optimized.",
            "Return only the titles without enumeration separated by \"|||\" and nothing else in your output."
        ]);
        $response = $chatGPT->askToChatGpt($prompt);

        $data['titles'] = explode("|||",$response);

        $apiCall = ApiCall::cache('chatgpt_v1', $hash, ['keyword' => $report->keyword->name, 'locale' => $report->locale, 'titles' => $titles]);
        $apiCall->response = json_encode($data);
        $apiCall->save();

        return $data;
    }

    /**
     * Prepares data for the OSR content distribution report (inital load, titles)
     *
     * @param Report $report
     * @return array
     */
    public function prepareContentTitlesReportData(Report $report) {
        /** @var OsrContent $content */
        $content = OsrContent::where('report_id',$report->id)->first();
        if($content == null) {
            $content = new OsrContent([]);
            $content->report_id = $report->id;
            $content->save();
        }

        if(!$content->hasTitles()) {
            $data = $this->getContentTitles($report);
            $content->titles = json_encode($data);
            $content->save();
        }

        return $content->getTitles();
    }

    /**
     * Fetches content descriptions
     *
     * @param Report $report
     * @return array|object|null
     */
    private function getContentDescriptions(Report $report)
    {
        $hash = md5($report->device . $report->locale . $report->keyword->name . "AI_DESCRIPTION_API_CALL");
        $apiCall = ApiCall::fromCache('chatgpt_v1', $hash, config('reports.osr.semrush.cache_timeout'));
        if ($apiCall && $apiCall->hasResponse()) {

            $data = $apiCall->getResponse(true);
            return $data;
        } else {
            $data['keyword'] = $report->keyword->name;
            $data['locale'] = $report->locale;

            $chatGPT = new ChatGPTController();
            $prompt = implode(" ", [
                "You are an SEO copywriter.",
                "You’re helping me draft descriptions for a website landing page. Your task is to generate 3 descriptions based on the following instructions:",
                "* Descriptions must include the keyword \"{$data['keyword']}\".",
                "* Descriptions must be written in the language that corresponds to this locale: \"{$data['locale']}\".",
                "* Descriptions must have a maximum of 160 characters or be between 810 and 920px.",
                "* Descriptions must not include brand names.",
                "* Descriptions must be SEO optimized.",
                "Return only the description without enumeration separated by \"|||\" and nothing else in your output."
            ]);
            $response = $chatGPT->askToChatGpt($prompt);

            $data['descriptions'] = explode("|||", $response);

            $apiCall = ApiCall::cache('chatgpt_v1', $hash, ['keyword' => $report->keyword->name, 'locale' => $report->locale]);
            $apiCall->response = json_encode($data);
            $apiCall->save();

            return $data;
        }
    }

    /**
     * Prepares data for the OSR content distribution report for descriptions
     *
     * @param Report $report
     * @return array
     */
    public function prepareContentDescriptionReportData(Report $report){
        /** @var OsrContent $content */
        $content = OsrContent::where('report_id',$report->id)->first();
        if($content == null) {
            $content = new OsrContent([]);
            $content->report_id = $report->id;
            $content->save();
        }

        if(!$content->hasDescriptions()) {
            $data = $this->getContentDescriptions($report);
            $content->descriptions = json_encode($data);
            $content->save();
        }

        return $content->getDescriptions();
    }

    /**
     * Prepares data for the OSR content distribution report for posts
     *
     * @param String $keyword
     * @param String $locale
     * @return array
     */
    private function getSemrushRelatedQuestions($keyword,$locale) {
        $hash = md5($locale . $keyword . "SEMRUSH_RELATED_QUESTIONS");
        $apiCall = ApiCall::fromCache('semrushv3_RelatedQuestionsForKeyword', $hash, config('reports.osr.semrush.cache_timeout'));
        if($apiCall && $apiCall->hasResponse()) {
            $data = $apiCall->getResponse(true);
            return $data;
        } else {
            $data = [];

            $relatedQuestions = SerpApiService::getRelatedQuestionsForKeyword($keyword, $locale);
            if(!empty($relatedQuestions)) {
                $data['questions'] = array_map(function($question) { return "\"" . $question->keyword . "\""; }, $relatedQuestions);
            }

            $apiCall = ApiCall::cache('semrushv3_RelatedQuestionsForKeyword', $hash, ['keyword' => $keyword, 'locale' => $locale]);
            $apiCall->response = json_encode($data);
            $apiCall->save();
        }
    }

    /**
     * Prepares data for the OSR content distribution report for posts
     *
     * @param Report $report
     * @return array
     */
    private function getContentPost(Report $report)
    {
        $hash = md5($report->device . $report->locale . $report->keyword->name . "AI_CONTENT_API_CALL");
        $apiCall = ApiCall::fromCache('chatgpt_v1', $hash, config('reports.osr.semrush.cache_timeout'));
        //$apiCall = null; // TODO remove
        if ($apiCall && $apiCall->hasResponse()) {
            $data = $apiCall->getResponse(true);
            return $data;
        } else {
            $landingPages = OsrLandingPage::whereReportId($report->id)->get();

            // Get MEDIAN word count
            $wordCounts = array_map(function ($landingPage) {
                return $landingPage['word_count'];
            }, $landingPages->toArray());
            sort($wordCounts);
            $lengthR = count($wordCounts) / 2;
            $lengthL = $lengthR - 1;
          $lengthL = $lengthL < 0 ? 0 : $lengthL;
            $medianWords = (@$wordCounts[$lengthL] + @$wordCounts[$lengthR]) / 2;

            // Get AVERAGE word count
            // $totalWords = array_reduce($landingPages->toArray(), function($carry, $landingPage) {
            //     return $carry + $landingPage['word_count'];
            // }, 0);
            // $averageWords = round($totalWords / count($landingPages), 0);

            $data['keyword'] = $report->keyword->name;
            $data['locale'] = $report->locale;
            $data['word_count'] = $medianWords;

            // Get related questions from Semrush
            $data['questions'] = [];
            $semrushData = $this->getSemrushRelatedQuestions($report->keyword->name, $report->locale);
            if (!empty($semrushData['questions'])) {
                $data['questions'] = $semrushData['questions'];
            }

            $questionsInstruction = empty($data['questions']) ? "" : "* Content must provide answers to the following questions: " . implode(", ", $data['questions']) . ".";

            $chatGPT = new ChatGPTController();
            $prompt = implode(" ", [
                "You are an SEO copywriter.",
                "Your task is to generate long-form content for the entire landing based on the following instructions:",
                "* Content is about and must include the keyword \"{$data['keyword']}\".",
                //"* Content should have the following elements: Title, Section Sectin, Hero Section, Features And Benefits Section, Proof, Calls to Action, Personalization and Segmentation.",
                $questionsInstruction,
                "* Content must be written in the language that corresponds to this locale: \"{$data['locale']}\".",
                "* Content must have more than {$data['word_count']} words.",
                "* Content must be SEO optimized and follow best practices.",
                "Do not use explicit \"Section\" labels but instead use natural headlines you’d expect to see on a landing page to separate the content. ",
                "Return just the content and nothing else in your output."
            ]);
            $response = $chatGPT->askToChatGpt($prompt);

            $data['content'] = $response;

            $apiCall = ApiCall::cache('chatgpt_v1', $hash, ['keyword' => $report->keyword->name, 'locale' => $report->locale]);
            $apiCall->response = json_encode($data);
            $apiCall->save();

            return $data;
        }
    }

    /**
     * Prepares data for the OSR content distribution report for posts
     *
     * @param Report $report
     * @return array
     */
    public function prepareContentPostsReportData(Report $report){
        /** @var OsrContent $content */
        $content = OsrContent::where('report_id',$report->id)->first();
        if($content == null) {
            $content = new OsrContent([]);
            $content->report_id = $report->id;
            $content->save();
        }

        if(!$content->haContent()) {
            $data = $this->getContentPost($report);
            $content->content = json_encode($data);
            $content->save();
        }

        return $content->getContent();
    }

    /**
     * Gets the keywords for a specific landing page
     *
     * @param Report $report
     * @return array
     */
    private function getKeywordsOverviewForBonus(Report $report) {
        $hash = md5($report->data['keyword'] . $report->locale . "KEYWORDS_OVERVIEW_API_CALL");
        $apiCall = ApiCall::fromCache('semrushv3_getKeywordsOverviewForLocale', $hash, config('reports.osr.semrush.cache_timeout'));

        if ($apiCall && $apiCall->hasResponse()) {
            return $apiCall->getResponse(true);
        }

        $data = SerpApiService::getKeywordsOverviewForLocale(
            $report->data['keyword'],
            $report->locale,
        );


        $apiCall = ApiCall::cache('semrushv3_getKeywordsOverviewForLocale', $hash, ['keyword' => $report->keyword->name, 'locale' => $report->locale]);
        $apiCall->response = json_encode($data);
        $apiCall->save();

        return $data;
    }


    /**
     * Prepares data for the OSR bonus distribution report
     *
     * @param Report $report
     * @return array
     */
    public function prepareBonusReportData(Report $report){

        dd('stop'); // milos

        /**
         *
         * Traženje slabih stranica koje se dobro rangiraju
            Dobra bi bila opcija da alat gleda jel ima stranica koja se rangira za unesenu (ili neku od povezanih) ključnu riječ, a da ima nizak DA.
            Svaka stranica koja ima DA od 1 do 5, a rangira se visoko za neku ključnu riječ koja ima više od 500 pretraživanja znači da je to lagana ključna riječ.
        Onda možemo preko SEMRush APIa vidjeti za koje se KW ta slaba stranica rangira ip dobiti još KW koji imaju nisku konkurenciju.
         */


        if($report->data['stages']['scraping'] < 2) {
            return null;
        }

        // TODO: THIS WAS NOT CACHED AT ALL!!!

        // Return array for this method
        $data = [];

        // Get search volume for the keyword from this report -> If smaller than 300, just return
        $stats = $this->getKeywordsOverviewForBonus($report);
        if($stats[0]->search_volume < 300){
            return $data;
        }

        // Get only first five domains from the results
        $domains = OsrDomain::where([['report_id', '=', $report->id] ])->limit(5)->get();


        foreach($domains as $index => &$domain) {

            if($domain->auth_score > 10){
                unset($domains[$index]);
                continue;
            }

            // Get keywords for the domain
            // TODO no cache NO SHIT
            $domain_keywords = SerpApiService::getKeywordsOverviewForLocale("", $report->locale);
            $keywords = [];

            // Get keywords into the array so we can check their stats once for all of them
            foreach($domain_keywords as $keyword){
                $keywords[] = $keyword->keyword;
            }

            // Get stats for the all keywords
            $stats = SerpApiService::getKeywordsOverviewForLocale($keywords, $report->locale);
            $i = 0;
            foreach($domain_keywords as &$keyword){
                $keyword->stats = $stats[$i];
                $planned_keyword = OsrPlannedPhrase::where('name', 'like', '%' . $keyword->keyword . '%')->where(['report_id' => $report->id])->first();

                if(!empty($planned_keyword)){
                    $keyword->planned = 'planned';
                }else{
                    $keyword->planned = 'no';
                }

                $i++;
            }
            $domain->keywords = $domain_keywords;

        }


        $data['domains'] = $domains;
        $data['search_volume'] = $stats[0]->search_volume;
        $data['keyword'] = $report->data['keyword'];
        return $data;

    }

    /**
     * Prepares data for the OSR bonus distribution report
     *
     * @param Report $report
     * @return array
     */
    public function prepareRecommendedStrategyData(Report $report) {
        // TODO implement
        return [];
    }

    /**
     * Prepares data for the OSR bonus distribution report
     *
     * @param Report $report
     * @return array
     */
    public function prepareRecommendedContentData(Report $report) {
        // TODO implement
        return [];
    }

    /**
     * Prepares data for the OSR bonus distribution report
     *
     * @param Report $report
     * @return array
     */
    public function prepareRecommendedSemanticData(Report $report) {
        // TODO implement
        return [];
    }

    /**
     * Prepares data for the OSR bonus distribution report
     *
     * @param Report $report
     * @return array
     */
    public function prepareRecommendedBacklinksData(Report $report) {
        // TODO implement
        return [];
    }
}
