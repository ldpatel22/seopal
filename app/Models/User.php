<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    private $focusedProject = null;

    # region Attributes

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'last_name',
        'title',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    # endregion

    # region Relationships

    public function projects() { return $this->hasManyThrough(Project::class, UserProject::class, 'user_id', 'id', 'id', 'project_id'); }
    public function reports() { return $this->hasMany(Report::class); }
    public function ownedProjects() { return $this->projects()->where('user_projects.type',UserProject::TYPE_OWNER); }
    public function externalProjects() { return $this->projects()->where('user_projects.type','!=',UserProject::TYPE_OWNER); }

    # endregion

    /**
     * Returns the currently focused project
     *
     * @return null|Project
     */
    public function focusedProject()
    {
        if($this->focused_project_id == null) return null;
        if($this->focusedProject !== null && $this->focusedProject->id == $this->focused_project_id) return $this->focusedProject;
        $this->focusedProject = $this->projects()->where('project_id',$this->focused_project_id)->first();
        return $this->focusedProject;
    }

    /**
     * Updates focused project
     *
     * @param Project|int $project
     */
    public function setFocusedProject($project)
    {
        if($project instanceof Project) {
            $this->focusedProject = $project;
            $this->focused_project_id = $project->id;
        } else {
            $this->focused_project_id = $project;
            $this->focusedProject();
        }
        $this->save();
    }

    /**
     * Returns the gravatar image with optional size
     *
     * @param int $size
     * @return string
     */
    public function getAvatar($size = 50)
    {
        return "https://www.gravatar.com/avatar/" . md5($this->email) . "?s={$size}&d=mm";
    }

    /**
     * Checks if user can access a project
     *
     * @param Project $project
     * @return bool
     */
    public function canAccessProject(Project $project)
    {
        return $project->users->contains($this);
    }

    /**
     * Checks if the user has at least one project
     *
     * @return bool
     */
    public function hasProjects()
    {
        return $this->projects->count() > 0;
    }

    /**
     * Checks if the user is admin and returns true if they are
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->access_status == 2;
    }
}
