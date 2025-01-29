<?php

namespace App\Jobs\OsrReport;

use App\Jobs\ReportJobStage;
use App\Models\Keyword;
use App\Models\OsrAlt;
use App\Models\OsrHeading;
use App\Models\OsrKeyword;
use App\Models\OsrLandingPage;
use App\Models\OsrLink;
use App\Models\OsrPhrase;
use App\Models\Report;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AnalyseLandingPagesJob extends ReportJobStage
{
    /**
     * @var Collection
     */
    private $phrases;

    /**
     * Create a new job instance.
     *
     * @param Report $report
     * @return void
     */
    public function __construct($report)
    {
        parent::__construct($report,'analysing');
        $this->phrases = new Collection();
    }

    private function matchHeadingWithKeywords(OsrHeading $heading)
    {
        $compare = mb_strtolower($heading->name);
        foreach($this->report->keyword->project->keywords as $keyword) { /** @var Keyword $keyword */
            $index = strpos($compare,mb_strtolower($keyword->name));
            if($index !== false) {
                $kwd = new OsrKeyword([
                    'heading_id' => $heading->id,
                    'keyword_id' => $keyword->id,
                    'name' => $keyword->name,
                    'level' => $heading->level,
                    'report_id' => $this->report->id,
                    'index' => $index,
                ]);
                $kwd->save();
            }
        }
    }

    private function matchAltWithKeywords(OsrAlt $alt)
    {
        $compare = mb_strtolower($alt->alt);
        foreach($this->report->keyword->project->keywords as $keyword) { /** @var Keyword $keyword */
            $index = strpos($compare,mb_strtolower($keyword->name));
            if($index !== false) {
                $kwd = new OsrKeyword([
                    'alt_id' => $alt->id,
                    'keyword_id' => $keyword->id,
                    'name' => $keyword->name,
                    'level' => 0,
                    'report_id' => $this->report->id,
                    'index' => $index,
                ]);
                $kwd->save();
            }
        }
    }

    private function matchLinkWithKeywords(OsrLink $link)
    {
        $compare = mb_strtolower($link->name);

        foreach($this->report->keyword->project->keywords as $keyword) { /** @var Keyword $keyword */
            $index = strpos($compare,mb_strtolower($keyword->name));
            if($index !== false) {
                $kwd = new OsrKeyword([
                    'link_id' => $link->id,
                    'keyword_id' => $keyword->id,
                    'name' => $keyword->name,
                    'level' => 0,
                    'report_id' => $this->report->id,
                    'index' => $index,
                ]);
                $kwd->save();
            }
        }
    }

    /**
     * Returns a phrase for the given (unsanitized) name
     *
     * @param string $name
     * @return OsrPhrase|mixed
     */
    private function getPhrase($name)
    {
        $name = mb_strtolower($name);
        //if(count(explode(' ', $name)) < 2) return null;

        $phrase = OsrPhrase::where(['name' => $name, 'report_id' => $this->report->id])->first();
        if(!$phrase) {
            $phrase = new OsrPhrase(['name' => $name, 'report_id' => $this->report->id]);
            $phrase->save();
        }

        return $phrase;
    }

    /**
     * Performs the job
     *
     * @throws \Exception
     * @return void
     */
    protected function perform()
    {
        $landingPages = OsrLandingPage::whereReportId($this->report->id)->get();
        foreach ($landingPages as $landingPage) {
            $data = json_decode($landingPage->data, true);

            // headings (titles)
            if(isset($data['titles'])) foreach($data['titles'] as $heading) {
                if(empty($heading['text'])) continue;

                $osrHeading = new OsrHeading([
                    'landing_page_id' => $landingPage->id,
                    'name' => $heading['text'],
                    'level' => $heading['level'],
                    'report_id' => $this->report->id,
                ]);

                /* 387: Don't need this anymore
                $phrase = $this->getPhrase($osrHeading->name);
                if($phrase) {
                    $osrHeading->phrase_id = $phrase->id;
                }
                */

                $osrHeading->save();
                $this->matchHeadingWithKeywords($osrHeading);
            }

            // links
            if(isset($data['links'])) foreach($data['links'] as $link) {
                if(!$link['href']) continue;
                if(!$link['host']) continue; // TODO consider adding host (from domain)
                if(!$link['text'] && empty($link['text'])) continue;
                if(!$link['external']) continue;

                $osrLink = new OsrLink([
                    'landing_page_id' => $landingPage->id,
                    'name' =>       Str::limit($link['text'], 252),
                    'href' =>       Str::limit($link['href'], 252),
                    'host' =>       Str::limit($link['host'], 252),
                    'parent_tag' => Str::limit($link['parent_tag'], 252),
                    'report_id' =>  $this->report->id,
                ]);

                /* 387: Don't need this anymore
                $phrase = $this->getPhrase($osrLink->name);
                if($phrase) {
                    $osrLink->phrase_id = $phrase->id;
                }
                */

                $osrLink->save();
                $this->matchLinkWithKeywords($osrLink);
            }

            // alts (images)
            if(isset($data['images'])) foreach($data['images'] as $image) {
                if(!$image['alt'] || empty($image['alt'])) continue;
                if(!$image['src']) continue;

                if(str_starts_with('/',$image['src'])) {
                    $image['src'] = $landingPage->domain . $image['src'];
                }

                $osrAlt = new OsrAlt([
                    'landing_page_id' => $landingPage->id,
                    'alt' => Str::limit($image['alt'], 252),
                    'src' => Str::limit($image['src'], 252),
                    'report_id' => $this->report->id,
                ]);

                /* 387: Don't need this anymore
                $phrase = $this->getPhrase($osrAlt->alt);
                if($phrase) {
                    $osrAlt->phrase_id = $phrase->id;
                }
                */
                
                $osrAlt->save();
                $this->matchAltWithKeywords($osrAlt);
            }
        }
    }
}
