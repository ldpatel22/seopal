<?php

namespace App\Repositories;

use App\Maps\UserAction;
use App\Models\Keyword;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProject;

class UserPermissionsRepository {

    /**
     * Checks User -> Project permissions
     *
     * @param User $user
     * @param string $action
     * @param array $objects
     * @return bool
     */
    private function canUserWithProject(User $user,$action,$objects)
    {
        $project = $objects['project'] ?? null;
        if(!($project instanceof Project)) return false;

        $relation = UserProject::where(['project_id' => $project->id, 'user_id' => $user->id])->first();
        if(!$relation) return false;

        switch ($action)
        {
            case UserAction::EDIT_PROJECT:
            case UserAction::DELETE_PROJECT:
            case UserAction::VIEW_PROJECT_EXTERNAL_USERS:
                return $relation->type == UserProject::TYPE_OWNER;
            case UserAction::RUN_PROJECT_REPORT:
            case UserAction::ADD_KEYWORDS_TO_PROJECT:
            case UserAction::DELETE_KEYWORD:
                return $relation->type = UserProject::TYPE_OWNER || $relation->type = UserProject::TYPE_MANAGER;
        }

        return false;
    }

    /**
     * Checks if a user an perform a certain action
     * optionally with given objects
     *
     * @param User $user
     * @param string $action
     * @param array $objects (optional)
     * @return bool
     */
    public function canUser(User $user, $action, $objects = [])
    {
        switch ($action)
        {
            case UserAction::EDIT_PROJECT:
            case UserAction::DELETE_PROJECT:
            case UserAction::VIEW_PROJECT_EXTERNAL_USERS:
            case UserAction::RUN_PROJECT_REPORT:
            case UserAction::ADD_KEYWORDS_TO_PROJECT:
                return $this->canUserWithProject($user,$action,$objects);
            case UserAction::DELETE_KEYWORD:
                /** @var Keyword $keyword */
                $keyword = $objects['keyword'];
                $relation = UserProject::where(['project_id' => $keyword->project_id, 'user_id' => $user->id])->first();
                return $relation && ($relation->type === UserProject::TYPE_OWNER || $relation->type === UserProject::TYPE_MANAGER);
            case UserAction::DELETE_REPORT:
                $report = $objects['report'];
                $relation = UserProject::where(['project_id' => $report->keyword->project_id, 'user_id' => $user->id])->first();
                return $relation && ($relation->type === UserProject::TYPE_OWNER || $relation->type === UserProject::TYPE_MANAGER);
        }
        return false;
    }

}
