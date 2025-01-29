<?php

namespace App\Http\Controllers;

use App\Maps\UserAction;
use App\Models\ApiCall;
use App\Models\Keyword;
use App\Models\Stats;
use App\Services\SerpApiServiceService;
use App\Repositories\KeywordOverviewStatsRepository;
use App\Repositories\UserPermissionsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @property KeywordOverviewStatsRepository $statsRepo
 * @property UserPermissionsRepository $permissionsRepo
 */
class KeywordsController extends Controller
{
    /**
     * Instantiates controller
     *
     * @param UserPermissionsRepository $permissionsRepository
     * @param KeywordOverviewStatsRepository $statsRepo
     */
    public function __construct(UserPermissionsRepository $permissionsRepository, KeywordOverviewStatsRepository $statsRepo)
    {
        $this->statsRepo = $statsRepo;
        $this->permissionsRepo = $permissionsRepository;
    }

    /**
     * Renders the "Keywords" page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function allKeywordsPage()
    {
        $keywords = project()->keywords->where('deleted',0)->sortBy('name');
        $this->putJavaScript(['View' => [
            'Routes' => [
                'addKeywords' => route('keywords.ajax.addKeywords'),
                'getStats' => route('keywords.ajax.getStats'),
                'getKeyword' => route('keywords.ajax.getKeyword'),
                'getRelatedKeywords' => route('keywords.ajax.getRelatedKeywords'),
            ]
        ]]);
        return view('keywords.all',['keywords' => $keywords]);
    }

    /**
     * Renders a single keyword page
     *
     * @param $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function singleKeywordPage($slug)
    { 
        $keywordId = $this->getIdFromSlug($slug);

        /** @var Keyword $keyword */
        $keyword = Keyword::find($keywordId);
        if($keyword == null || $keyword->deleted || !user()->canAccessProject($keyword->project)) {
            abort(404);
        }

        $user = user();
        $user->setFocusedProject($keyword->project); // force focused project

        $reports = $keyword->reports()->where('deleted',false)->orderBy('created_at','desc')->get();

        $Options = [
            'deleteKeyword' => $this->permissionsRepo->canUser($user, UserAction::DELETE_KEYWORD, ['keyword' => $keyword]),
        ];

        $this->putJavaScript(['View' => [
            'Routes' => [
                'newReport' => route('reports.ajax.newReport'),
                'deleteKeyword' => route('projects.ajax.deleteKeyword'),
            ],
            'Options' => $Options,
        ]]);
        return view('keywords.single',['keyword' => $keyword, 'reports' => $reports, 'options' => $Options]);
    }


    /**
     * Get newly added single keyword from the database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetKeyword(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string',
        ]);

        /** @var Keyword $keyword */
        $keyword = Keyword::find($request->get('keyword'));
        if($keyword == null || $keyword->deleted || !user()->canAccessProject($keyword->project)) {
            abort(404);
        }

        return new JsonResponse(['keyword_data' => $keyword, 'keyword_permalink' => $keyword->permalink()]);
    }


    /**
     * Attemtps to create a new project for the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxAddKeywords(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string',
        ]);

        $projectId = project()->id;

        // Split keywords by both commas and newlines
        $rawKeywords = preg_split('/[\r\n,]+/', $request->get('keywords'));

        $keywords = [];
        foreach($rawKeywords as $rawKeyword) {
            $rawKeyword = e(trim($rawKeyword));
            if(!project()->hasKeyword($rawKeyword) && !empty($rawKeyword)) {
                $keywords[] = new Keyword([
                    'name' => $rawKeyword,
                    'project_id' => $projectId,
                ]);
            }
        }

        foreach($keywords as $keyword) $keyword->save();

        return new JsonResponse(['redirect' => project()->permalink()]);
    }


    /**
     * Attemtps to delete a keyword
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteKeyword(Request $request)
    {
        $request->validate([
            'keywordId' => 'required',
        ]);

        $project = project();

        $keyword = Keyword::find($request->get('keywordId'));
        if($keyword == null || $project->id !== $keyword->project_id) {
            return new JsonResponse(['error' => 'Invalid keyword.'], 404);
        }

        if(!$this->permissionsRepo->canUser(user(), UserAction::DELETE_KEYWORD, ['keyword' => $keyword])) {
            return new JsonResponse(['error' => 'You cannot delete this keyword.'], 400);
        }

        // update keyword
        $keyword->deleted = true;
        $keyword->save();

        return new JsonResponse(['redirect' => route('keywords.all')]);
    }

    /**
     * Gets stats for given keywords/locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetStats(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'locale' => 'string'
        ]);


        $project = project();

        // determine locale
        $locale = $request->has('locale') ? $request->get('locale') : $project->locale;

        // determine keywords to update
        $keywords = $project->keywords()->whereIn('id',$request->get('ids'))->get();
        $names = $keywords->map(function($keyword){ return $keyword->name; })->toArray();

        // get the stats and prep results
        $stats = $this->statsRepo->get($names, $locale);
        $results = $stats->map(function(Stats $stat) use ($keywords) {
            //$keyword = $keywords->filter(function($keyword) use($stat) { return $keyword->name == $stat->entity; })->first();
            $keyword = $keywords->where('name',$stat->entity)->first();
            return [
                'keywordId' => $keyword ? $keyword->id : null,
                'stats' => $stat->getData(),
            ];
        });

        return new JsonResponse(['stats' => $results->toArray()]);
    }

    /**
     * Gets related keywords for given keyword
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetRelatedKeywords(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        $keyword = Keyword::find($request->get('id'));
        if($keyword == null || $keyword->deleted || !user()->canAccessProject($keyword->project)) {
            return new JsonResponse(['error' => "Keyword doesn't exist or it's not accesible!"]);
        }

        $hash = md5($keyword->name . $keyword->project->locale . 'RELATED_KEYWORDS');
        $apiCall = ApiCall::fromCache('semrushv3_RelatedKeywords',$hash,config('reports.osr.semrush.cache_timeout'));

        // get related keywords
        if($apiCall && $apiCall->hasResponse()) {
            $searchResults = $apiCall->getResponse();
        } else {
            $apiCall = ApiCall::cache('semrushv3_RelatedKeywords',$hash,['keyword' => $keyword->name, 'locale' => $keyword->project->locale]);
            $searchResults = SerpApiService::getRelatedKeywordsForKeyword($keyword->name, $keyword->project->locale);
            $apiCall->response = json_encode($searchResults);
            $apiCall->save();
        }

        // remove results for existing keywords
        $results = [];
        foreach($searchResults as $result) {
            if($keyword->project->hasKeyword($result->keyword)) continue;
            $results[] = $result;
        }

        return new JsonResponse(['keywords' => $results]);
    }
}
