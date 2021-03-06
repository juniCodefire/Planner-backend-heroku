<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class WorkSpaceToMember extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = "workspacestomembers";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'id', 'owner_id', 'workspace_id', 'member_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function owner() {

      return $this->belongsTo('App\User', 'owner_id', 'id');
    }
    public function members() {

      return $this->belongsTo('App\User', 'member_id', 'id');
    }
    public function companies() {

      return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function projects() {

      return $this->hasMany('App\Project', 'workspace_id', 'id');
    }
    public function workspace() {

      return $this->belongsTo('App\WorkSpace', 'workspace_id', 'id');
    }
}