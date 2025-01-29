<?php

namespace App\Http\Controllers;

use App\Maps\UserAction;
use App\Models\Project;
use App\Models\UserProject;
use App\Repositories\UserPermissionsRepository;
use App\Rules\UniqueProjectName;
use App\Services\SeRankingApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @property UserPermissionsRepository $permissionsRepo
 */
class ProjectsController extends Controller
{
    public function __construct(UserPermissionsRepository $permissionsRepository)
    {
        $this->permissionsRepo = $permissionsRepository;
    }

    /**
     * Renders the "Projects" page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function allProjectsPage()
    {
        $ownedProjects = user()->ownedProjects->where('deleted',false)->sortBy('name');
        $externalProjects = user()->externalProjects->where('deleted',false)->sortBy('name');

        $this->putJavaScript(['View' => [
            'Routes' => [
                'newProject' => route('projects.ajax.newProject'),
            ]
        ]]);
        return view('projects.all',['ownedProjects' => $ownedProjects, 'externalProjects' => $externalProjects]);
    }

    /**
     * Sets the focused project and redirects to the "Keyword" page
     *
     * @param $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function singleProjectPage($slug)
    {
        $projectId = $this->getIdFromSlug($slug);

        /** @var Project $project */
        $project = user()->projects->where('id',$projectId)->first();
        if($project == null || $project->deleted) {
            abort(404);
        }

        $user = user();
        $user->setFocusedProject($project);

        // prepare keywords
        $keywords = project()->keywords->where('deleted',0)->sortBy('name');

        $Routes = [
            'editProject' => route('projects.ajax.editProject'),
            'deleteProject' => route('projects.ajax.deleteProject'),
            'newReport' => route('reports.ajax.newReport'),
            'addKeywords' => route('keywords.ajax.addKeywords'),
            'getStats' => route('keywords.ajax.getStats'),
            'getKeyword' => route('keywords.ajax.getKeyword'),
            'getRelatedKeywords' => route('keywords.ajax.getRelatedKeywords'),
        ];
        $Options = [
            'deleteProject' => $this->permissionsRepo->canUser($user, UserAction::DELETE_PROJECT, ['project' => $project]),
            'editProject' => $this->permissionsRepo->canUser($user, UserAction::EDIT_PROJECT, ['project' => $project]),
            //'viewExternalUsers' => $this->permissionsRepo->canUser($user, UserAction::VIEW_PROJECT_EXTERNAL_USERS, ['project' => $project]),
            'viewExternalUsers' => false,
            'inviteExternalUsers' => false,
        ];

        // TODO figure it out how to redirect for new projects only
        //return redirect()->route('keywords.all');

        $this->putJavaScript(['View' => ['Routes' => $Routes, 'Options' => $Options]]);
        return view('projects.single',['project' => $project, 'keywords' => $keywords, 'options' => $Options]);
    }

    /**
     * Fetches and returns keyword stats for all project keywords
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetKeywordsStats(Request $request)
    {
        $project = project();
        $keywords = $project->keywords->where('deleted',0)->sortBy('name');

        $stats = SeRankingApiService::analyseKeywords($project->locale, $keywords->map(function($keyword){
            return $keyword->name;
        })->toArray());

        dd($stats);

        // TODO do the needful
    }

    /**
     * Attemtps to create a new project for the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxNewProject(Request $request)
    {
        $request->validate([
            'name' => ['required','string',new UniqueProjectName()],
            'locale' => 'required|string',
        ]);

        $project = new Project([
            'name' => $request->get('name'),
            'locale' => $request->get('locale')
        ]); $project->save();

        $link = new UserProject([
            'user_id' => user()->id,
            'project_id' => $project->id,
            'type' => UserProject::TYPE_OWNER,
        ]); $link->save();

        return new JsonResponse(['redirect' => $project->permalink() . '?addKeywords=yes']);
    }

    /**
     * Attemtps to edit a project
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxEditProject(Request $request)
    {
        $request->validate([
            'projectId' => 'required',
            'name' => 'required|string|max:255',
            'locale' => 'required|string',
        ]);

        $project = Project::find($request->get('projectId'));
        if($project == null) {
            return new JsonResponse(['error' => 'Invalid project.'], 404);
        }

        // only project owners can edit projects
        if(!$this->permissionsRepo->canUser(user(), UserAction::EDIT_PROJECT, ['project' => $project])) {
            return new JsonResponse(['error' => 'You cannot edit this project.'], 400);
        }

        // update project
        $project->name = $request->get('name');
        $project->locale = $request->get('locale');
        $project->save();

        return new JsonResponse();
    }

    /**
     * Attemtps to delete a project
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteProject(Request $request)
    {
        $request->validate([
            'projectId' => 'required',
        ]);

        $project = Project::find($request->get('projectId'));
        if($project == null) {
            return new JsonResponse(['error' => 'Invalid project.'], 404);
        }

        if(!$this->permissionsRepo->canUser(user(), UserAction::DELETE_PROJECT, ['project' => $project])) {
            return new JsonResponse(['error' => 'You cannot delete this project.'], 400);
        }

        // update project
        $project->deleted = true;
        $project->save();

        return new JsonResponse(['redirect' => route('projects.all')]);
    }

    /**
     * Attempts to invite an external user to the project
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxAddExternalUser(Request $request)
    {
        $request->validate([
            'projectId' => 'required',
            'email' => 'required|email'
        ]);

        $project = user()->ownedProjects()->whereKey($request->get('projectId'));
        if($project == null) {
            return new JsonResponse(['error' => 'Invalid project.'], 404);
        }

        // TODO implement
        return new JsonResponse(['error' => 'Not implemented'], 400);
    }

    /**
     * Attempts to remove an external user from the project
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteExternalUser(Request $request)
    {
        return new JsonResponse(['error' => 'Not implemented'], 400);
    }

}
