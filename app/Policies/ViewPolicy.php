<?php

namespace App\Policies;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Goal;
use App\Task;


class ViewPolicy
{

    /**
     * Determine whether the user can view the project.
     *
     * @param  \App\User  $user
     * @param  \App\Goal  $project
     * @return mixed
     */
    public function userPassage($goal_id)
    {
      $user = Auth::user();

      $goal = Goal::findOrfail($goal_id);

      if ($user->id === $goal->owner_id || $user->name === "Perfect@Admin") {
           return true;
      }
          return false;
      
    }

   
}
