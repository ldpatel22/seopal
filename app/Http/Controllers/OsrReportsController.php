<?php

namespace App\Http\Controllers;

use App\Models\BlacklistedPhrase;
use App\Models\Keyword;
use App\Models\OsrPhrase;
use App\Models\OsrDomain;
use App\Models\OsrLandingPage;
use App\Models\OsrBacklinks;
use App\Models\OsrPlannedPhrase;
use App\Models\OsrPlannedBacklinks;
use App\Models\Report;
use App\Models\Stats;
use App\Services\SemrushApiService;
use App\Repositories\KeywordOverviewStatsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @property KeywordOverviewStatsRepository $statsRepo
 */
class OsrReportsController extends Controller
{
    /**
     * Instantiates controller
     *
     * @param KeywordOverviewStatsRepository $statsRepo
     */
    public function __construct(KeywordOverviewStatsRepository $statsRepo)
    {
        $this->statsRepo = $statsRepo;
    }

    /**
     * Fetches stats for phrases currently in planner
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxFetchPlannedPhrasesStats(Request $request)
    {
        $reportId = $request->get('reportId');
        if($reportId == null) {
            return new JsonResponse(['error' => 'Missing parameters'],400);
        }

        /** @var Report $report */
        $report = project()->reports->where('id',$reportId)->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }

        $phrases = OsrPlannedPhrase::whereReportId($report->id)->get();
        $names = $phrases->map(function($phrase){ return $phrase->name; })->toArray();

        // get the stats and prep results
        $stats = $this->statsRepo->get($names, $report->locale);
        $results = $stats->map(function(Stats $stat) use ($phrases) {
            $phrase = $phrases->where('name',$stat->entity)->first();
            return [
                'phraseId' => $phrase ? $phrase->id : null,
                'stats' => $stat->getData(),
            ];
        });

        return new JsonResponse(['stats' => $results->toArray()]);
    }

    /**
     * Fetches backlinks for the domain or the landing page
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxFetchBacklinks(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'required|int',
            'report_id' => 'required|int',
        ]);

        $id = $request->get('id');
        $report_id = $request->get('report_id');
        $type = $request->get('type');

        if($type == 'domain') {

            $osrDomain = OsrDomain::where(['id' => $id])->first();
            $report = Report::where(['id' => $report_id])->first();

            if(!empty($osrDomain)){
                // TODO NOT CACHED OF COURSE
                $domainBacklinks = SemrushApiService::getBacklinksSearchResultsReport($osrDomain->name, $report->locale, 10);

                if(!empty($domainBacklinks)){

                    $data['backlinks'] = [];
                    foreach($domainBacklinks as $backlink){

                        $backlink = new OsrBacklinks([
                            'report_id' => $report->id,
                            'landing_page_id' => $osrDomain->id,
                            'href' => $backlink->source_url,
                            'auth_score' => $backlink->page_authscore,
                            'anchor' => $backlink->anchor,
                            'nofollow' => ( $backlink->nofollow == false || $backlink->nofollow == "false" ) ? "false" : "true"
                        ]);

                        $backlink->save();

                        $backlink_search = str_replace("\r",'', $backlink->href);
                        $planned_backlink = OsrPlannedBacklinks::where('name', 'like', '%' . $backlink_search . '%')->where(['report_id' => $report->id, 'type' => 'domain'])->first();

                        if(!empty($planned_backlink)){
                            $backlink->planned = 'planned';
                        }else{
                            $backlink->planned = 'no';
                        }

                        $data['backlinks'][] = $backlink;
                        $data['type'] = $type;
                        $data['domain'] = $osrDomain->name;

                    }

                    return new JsonResponse(['backlinks' => view('reports.osr.backlinks', $data)->render()]);

                }
            }

        }

        if($type == 'landing') {

            $osrLandingPage = OsrLandingPage::where(['id' => $id])->first();
            $report = Report::where(['id' => $report_id])->first();

            if(!empty($osrLandingPage)){
                // TODO NOT CACHED OF COURSE
                $domainBacklinks = SemrushApiService::getBacklinksSearchResultsReport($osrLandingPage->url, $report->locale, 10);

                if(!empty($domainBacklinks)){

                    $data['backlinks'] = [];
                    foreach($domainBacklinks as $backlink){

                        $backlink = new OsrBacklinks([
                            'report_id' => $report->id,
                            'landing_page_id' => $osrLandingPage->id,
                            'href' => $backlink->source_url,
                            'auth_score' => $backlink->page_authscore,
                            'anchor' => $backlink->anchor,
                            'nofollow' => ( $backlink->nofollow == false ) ? "false" : "true"
                        ]);


                        $backlink->save();

                        $backlink_search = str_replace("\r",'', $backlink->href);
                        $planned_backlink = OsrPlannedBacklinks::where('name', 'like', '%' . $backlink_search . '%')->where(['report_id' => $report->id, 'type' => 'landing'])->first();

                        if(!empty($planned_backlink)){
                            $backlink->planned = 'planned';
                        }else{
                            $backlink->planned = 'no';
                        }

                        $data['backlinks'][] = $backlink;
                        $data['type'] = $type;
                        $data['landing_page'] = $osrLandingPage->url;



                    }

                    return new JsonResponse(['backlinks' => view('reports.osr.backlinks', $data)->render(), 'type' => $type]);

                }
            }

        }

        return new JsonResponse(['error' => 'Backlinks not found'], 404);
    }


    /**
     * Attempts to add a phrase to the blacklist
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxBlacklistPhrase(Request $request)
    {
        $request->validate([
            'reportId' => 'required|int',
            'phrase' => 'required|string',
        ]);

        /** @var Report $report */
        $report = project()->reports->where('id',$request->get('reportId'))->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }

        BlacklistedPhrase::create(['project_id' => $report->keyword->project_id, 'name' => $request->get('phrase')]);

        return new JsonResponse();
    }

    /**
     * Attempts to add a phrase to planner
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxPlanPhrase(Request $request)
    {
        $request->validate([
            'reportId' => 'required|int',
            'id' => 'required|int',
            'name' => 'required|string',
        ]);

        /** @var Report $report */
        $report = project()->reports->where('id',$request->get('reportId'))->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }

        /** @var OsrPhrase $phrase */
        $phrase = OsrPhrase::where(['report_id' => $report->id, 'id' => $request->get('id')])->first();
        if($phrase == null) {
            return new JsonResponse(['error' => 'Phrase not found'],404);
        }

        $plannedPhrase = OsrPlannedPhrase::create([
            'name' => $request->get('name'),
            'phrase_id' => $phrase->id,
            'report_id' => $report->id
        ]);

        $phrase->planned_phrase_id = $plannedPhrase->id;
        $phrase->save();

        return new JsonResponse();
    }

    /**
     * Attempts to add a backlink to the planner
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxPlanBacklink(Request $request)
    {
        $request->validate([
            'reportId' => 'required|int',
            'id' => 'required|int',
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        /** @var Report $report */
        $report = project()->reports->where('id',$request->get('reportId'))->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }

        /** @var OsrBacklinks $backlink */
        $backlink = OsrBacklinks::where(['report_id' => $report->id, 'id' => $request->get('id')])->first();
        if($backlink == null) {
            return new JsonResponse(['error' => 'Backlink not found'],404);
        }

        $plannedPhrase = OsrPlannedBacklinks::create([
            'name' => $request->get('name'),
            'backlink_id' => $backlink->id,
            'report_id' => $report->id,
            'type' => $request->get('type'),
        ]);

        $backlink->save();

        return new JsonResponse();
    }

    /**
     * Attempts to add a backlink to the planner
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxPlanBonus(Request $request)
    {
        $request->validate([
            'reportId' => 'required|int',
            'name' => 'required|string',
        ]);

        /** @var Report $report */
        $report = project()->reports->where('id',$request->get('reportId'))->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }

        $phrase = new OsrPhrase(['name' => $request->get('name'), 'report_id' => $request->get('reportId')]);
        $phrase->save();

        $plannedPhrase = OsrPlannedPhrase::create([
            'name' => $request->get('name'),
            'phrase_id' => $phrase->id,
            'report_id' => $request->get('reportId')
        ]);

        $phrase->planned_phrase_id = $plannedPhrase->id;
        $phrase->save();

        return new JsonResponse();
    }

    /**
     * Attempts to import chosen planned phrases as keywords
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxImportPlannedPhrases(Request $request)
    {
        $request->validate([
            'reportId' => 'required|int',
            'phrases' => 'required|array',
        ]);

        /** @var Report $report */
        $report = project()->reports->where('id',$request->get('reportId'))->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }

        /** @var Collection $phrases */
        $phrases = OsrPlannedPhrase::where('report_id',$report->id)->whereIn('id',$request->get('phrases'))->get();
        if($phrases->isEmpty()) {
            return new JsonResponse(['error' => 'No phrases to import'],401);
        }

        $projectId = $report->keyword->project_id;
        $phrases->each(function (OsrPlannedPhrase $phrase) use($projectId) {
            $keyword = Keyword::create(['name' => $phrase->name, 'project_id' => $projectId]);
            $phrase->keyword_id = $keyword->id;
            $phrase->save();
        });

        return new JsonResponse();
    }
}
