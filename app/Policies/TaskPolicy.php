<?php

namespace App\Policies;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Goal;
use App\Task;


class TaskPolicy
{

    /**
     *
     * @param  \App\User  $user
     * @param  \App\Goal  $Goal
     * @return mixed
     */
    public function taskValidate($due_date, $goalData)
    {

         $task_due_date   = date('Y-m-d', strtotime($due_date));

         //Converting the Goal string time to time for checking

         $goal_due_date   = date('Y-m-d',strtotime($goalData->due_date));

        if ($task_due_date > $goal_due_date ) {

            return true;
        }
        return false;
      
    }

   
}
