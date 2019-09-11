<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Cache;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'team_permit', 'api_token', 'phone_number', 'verify_code', 'account_type', 'user_image', 'confirm_token', 'status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'confirm_token', 'status', 'verify_code','team_permit',
    ];

    public function workspaces() {
      return $this->hasMany('App\WorkSpace', 'owner_id', 'id');
    }
    public function company() {
      return $this->hasMany('App\Company', 'owner_id', 'id');
    }
    public function isOnline() {
        return Cache::has('useronline'. $this->id);
    }

     public function interest()
    {
        return $this->belongsToMany('App\Interest', 'user_interests', 'owner_id', 'interest_id');
    }
    public function workspaceToMember()
    {
        return $this->belongsTo(WorkSpacesToMember::class);
    }
}
