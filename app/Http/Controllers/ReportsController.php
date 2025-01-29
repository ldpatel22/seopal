<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\OsrLandingPage;
use App\Models\Report;
use App\Models\OsrPlannedBacklinks;
use App\Models\OsrPlannedPhrase;
use App\Repositories\OsrReportsRepository;
use App\Repositories\ReportsRepository;
use App\Services\SeRankingApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\UserPermissionsRepository;
use App\Maps\UserAction;
use PDF;

class ReportsController extends Controller
{
    /** @var ReportsRepository */
    private $reportsRepo;

    /** @var OsrReportsRepository */
    private $osrRepo;

    /**
     * Instantiates the controller
     *
     * @param ReportsRepository $reportsRepository
     * @param OsrReportsRepository $osrReportsRepository
     * @param UserPermissionsRepository $permissionsRepository
     */
    public function __construct(UserPermissionsRepository $permissionsRepository, ReportsRepository $reportsRepository,OsrReportsRepository $osrReportsRepository)
    {
        $this->reportsRepo = $reportsRepository;
        $this->osrRepo = $osrReportsRepository;
        $this->permissionsRepo = $permissionsRepository;
    }

    /**
     * Renders the "Reports" page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function allReportsPage()
    {
        $reports = project()->reports()->where('reports.deleted',false)->orderBy('created_at','desc')->get();
        $this->putJavaScript(['View' => [
            'Routes' => [
                'newReport' => route('reports.ajax.newReport'),
            ]
        ]]);
        return view('reports.all',['reports' => $reports]);
    }

    /**
     * Sets the focused project and redirects to the "Keyword" page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function singleReportPage($slug)
    {
        $reportId = $this->getIdFromSlug($slug);

        /** @var Report $report */
        $report = Report::find($reportId);
        if($report == null || $report->deleted || !user()->canAccessProject($report->keyword->project)) {
            abort(404);
        }

        // force focused project
        user()->setFocusedProject($report->keyword->project);

        $Options = [
            'deleteReport' => $this->permissionsRepo->canUser(user(), UserAction::DELETE_REPORT, ['report' => $report]),
        ];

        $this->putJavaScript(['View' => [
            'Routes' => [
                'getProgress' => route('reports.ajax.getProgress'),
                'getReportSection' => route('reports.ajax.getSection'),
                'osrBlacklistPhrase' => route('reports.osr.ajax.blacklist'),
                'osrPlanPhrase' => route('reports.osr.ajax.plan'),
                'osrPlanBacklink' => route('reports.osr.ajax.backlink'),
                'osrPlanBonus' => route('reports.osr.ajax.bonus'),
                'osrFetchPhrasesStats' => route('reports.osr.ajax.stats'),
                'osrFetchBacklinks' => route('reports.osr.ajax.backlinks'),
                'osrImportPhrases' => route('reports.osr.ajax.import'),
                'deleteReport' => route('reports.ajax.deleteReport'),
                'deletePlanner' => route('reports.ajax.deletePlanner')
            ],
            'Options' => $Options,
        ]]);
        return view('reports.single', ['report' => $report, 'options' => $Options]);
    }

    /**
     * Generates PDF report
     *
     * @param $slug
     * @return mixed
     */
    public function printReport($slug)
    {
        $reportId = $this->getIdFromSlug($slug);

        /** @var Report $report */
        $report = Report::find($reportId);
        if($report == null || $report->deleted || !user()->canAccessProject($report->keyword->project)) {
            abort(404);
        }

        // force focused project
        user()->setFocusedProject($report->keyword->project);

        // prepare sections
        $sections = [];
        foreach(['osr.landing_pages','osr.headings','osr.links','osr.alts','osr.phrases'] as $section) {
            $sections[$section] = $this->prepareReportSectionData($report, $section);
        }

        return view('pdf.osr_report', ['report' => $report, 'sections' => $sections]);

//        // go PDF
//        $pdf = PDF::loadView('pdf.osr_report', ['report' => $report]);
//        return $pdf->stream($report->name() . '.pdf');
    }

    /**
     * Fetches and returns project status and HTML for report progress bar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetReportProgress(Request $request)
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

        return new JsonResponse([
            'status' => $report->status,
            'html' => view('reports.osr.progressbar',['report' => $report])->render()
        ]);
    }

    /**
     * @param Report $report
     * @param string $section
     * @return array|array[]|null
     */
    private function prepareReportSectionData($report, $section)
    {
        switch ($section)
        {
            case 'osr.landing_pages':
                return $this->osrRepo->prepareLandingPagesReportData($report);
            case 'osr.headings':
                return $this->osrRepo->prepareHeadingsReportData($report);
            case 'osr.links':
                return $this->osrRepo->prepareLinksReportData($report);
            case 'osr.alts':
                return $this->osrRepo->prepareAltsReportData($report);
            case 'osr.keywords':
                return $this->osrRepo->prepareKeywordsReportData($report);
            case 'osr.backlinks':
                //387: Return nothing on the inital load
                return $this->osrRepo->prepareBacklinksReportData($report);
            case 'osr.phrases':
                return $this->osrRepo->preparePhrasesReportData($report);
            case 'osr.planner':
                return $this->osrRepo->preparePlannerReportData($report);
            case 'osr.content':
                return $this->osrRepo->prepareContentTitlesReportData($report);
            case 'osr.bonus':
                return $this->osrRepo->prepareBonusReportData($report);
            case 'osr.content_titles':
            case 'osr.recommendations.content_titles':
                return $this->osrRepo->prepareContentTitlesReportData($report);
            case 'osr.content_description':
            case 'osr.recommendations.content_descriptions':
                return $this->osrRepo->prepareContentDescriptionReportData($report);
            case 'osr.content_posts':
            case 'osr.recommendations.content_posts':
                return $this->osrRepo->prepareContentPostsReportData($report);
            case 'osr.recommendations.strategy':
                return $this->osrRepo->prepareRecommendedStrategyData($report);
            case 'osr.recommendations.content':
                return $this->osrRepo->prepareRecommendedContentData($report);
            case 'osr.recommendations.semantic':
                return $this->osrRepo->prepareRecommendedSemanticData($report);
            case 'osr.recommendations.backlinks':
                return $this->osrRepo->prepareRecommendedBacklinksData($report);
            default:
                return null;
        }
    }

    /**
     * 387: Very important part for loading sections!!!
     * Fetches and returns HTML for the requested report section
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetReportSection(Request $request)
    {
        $reportId = $request->get('reportId');
        $section = $request->get('section');
        if($reportId == null || $section == null) {
            return new JsonResponse(['error' => 'Missing parameters'],400);
        }

        /** @var Report $report */
        $report = project()->reports->where('id',$reportId)->first();
        if($report == null) {
            return new JsonResponse(['error' => 'Report not found'],404);
        }
		//dd($report);
        $data = $this->prepareReportSectionData($report,$section);
        if($data === null) {
            return new JsonResponse(['error' => 'No data'], 404);
        }

        return new JsonResponse(['html' => view('reports.'.$section,$data)->render()]);
    }

    /**
     * Creates a new report and schedules it for running
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxStartNewReport(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'keywordId' => 'required|int',
            'locale' => 'required|string',
            'device' => 'required|string',
        ]);

        $keyword = Keyword::find($request->get('keywordId'));
        if(!$keyword) {
            return new JsonResponse(['error' => 'Keyword not found'],404);
        }

        // TODO check locale? (maybe validator can do it)
        // TODO check device? (maybe validator can do it)

        $type = $request->get('type');
        $locale = $request->get('locale');
        $device = $request->get('device');

       // try {
            $report = $this->reportsRepo->prepareNewReport($keyword, $type, $locale, $device);
            $this->reportsRepo->scheduleReport($report);

			 
        //} catch (\Exception $e) {
            //return new JsonResponse(['error' => $e->getMessage()],500);
       // }



       // return new JsonResponse(['redirect' => $report->permalink()]);
        return new JsonResponse(['redirect' => $report->permalink()]);
    }

    /**
     * Attemtps to delete a report
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteReport(Request $request)
    {
        $request->validate([
            'reportId' => 'required',
        ]);

        $project = project();

        $report = Report::find($request->get('reportId'));
        if($report == null || $project->id !== $report->keyword->project_id) {
            return new JsonResponse(['error' => 'Invalid report.'], 404);
        }

        if(!$this->permissionsRepo->canUser(user(), UserAction::DELETE_REPORT, ['report' => $report])) {
            return new JsonResponse(['error' => 'You cannot delete this report.'], 400);
        }

        // update keyword
        $report->deleted = true;
        $report->save();

        return new JsonResponse(['redirect' => route('reports.all')]);
    }

    /**
     * Attemtps to delete an item from Planner
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeletePlanner(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required|string'
        ]);

        if($request->get('type') == "phrase"){

            $item = OsrPlannedPhrase::find($request->get('id'));
            if($item){

                $item->delete();
                return new JsonResponse();

            }

        }

        if($request->get('type') == "backlink"){

            $item = OsrPlannedBacklinks::find($request->get('id'));
            if($item){

                $item->delete();
                return new JsonResponse();

            }

        }

            return new JsonResponse(['error' => 'Error in deleting the item from the planner! Please, try again!'], 400);
    }
}
