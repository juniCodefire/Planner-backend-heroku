<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class WorkSpaceCollaborateWorkSpace extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = "workspacetoWorkspaces";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'id', 'collaborator_initiator_workspace_id', 'collaborator_initiatee_workspace_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function collaborator_initiator_workspaces() {
      return $this->belongsTo('App\Comapany', 'collaborator_initiator_workspace_id', 'id');
    }
    
    public function collaborator_initiatee_workspaces() {
      return $this->belongsTo('App\Comapany', 'collaborator_initiatee_workspace_id', 'id');
    }
}
