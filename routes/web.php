<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//Post request for regstration
$router->post('api/register', 'RegistrationController@store');
//Post request for the login
$router->post('api/login', 'LoginController@login');
//Post request for the login
$router->get('api/tokendestroy', 'TokenDestroyController@tokenDestroy');

$router->get('api/confirmation/{token}', 'ConfirmationController@confirmUser');

$router->post('api/verify/email', 'VerifyTokenController@validateUser');

$router->put('api/reset/password', 'ConfirmationController@resetPassword');


//User Profile Routes
//Get request to show authourize dashboard

$router->group(['prefix' => 'api/'], function () use ($router) {

    $router->get('profile', 'ProfileController@index');

    $router->put('profile/edit', 'ProfileController@update');

    $router->delete('profile/delete', 'ProfileController@destroy');

    //Imgae Upload

    $router->post('profile/upload', 'ImagesController@upload');

});

$router->group(['prefix' => 'api/'], function () use ($router) {

    $router->get('goals', 'GoalsController@index');

    $router->get('goals/{goal_id}', 'GoalsController@show');

    $router->post('goals/create', 'GoalsController@store');

    $router->put('goals/{goal_id}/edit', 'GoalsController@update');

    $router->put('goals/{goal_id}/status', 'GoalsController@updateGoalStatus');

    $router->delete('goals/{goal_id}/delete', 'GoalsController@destroy');

});

$router->group(['prefix' => 'api/goals/'], function () use ($router) {

    $router->get('{goal_id}/tasks', 'GoalsTasksController@index');

    $router->get('{goal_id}/tasks/{task_id}', 'GoalsTasksController@show');

    $router->post('{goal_id}/tasks/create', 'GoalsTasksController@store');

    $router->put('{goal_id}/tasks/{task_id}/edit', 'GoalsTasksController@update');

    $router->put('{goal_id}/tasks/{task_id}/status', 'GoalsTasksController@updateTaskStatus');

    $router->delete('{goal_id}/tasks/{task_id}/delete', 'GoalsTasksController@destroy');

});

$router->group(['prefix' => 'api/'], function () use ($router) {

    $router->get('activities', 'ActivitiesController@index');

    $router->get('count/goals', 'ActivitiesController@goalsCount');

    $router->get('count/tasks', 'ActivitiesController@tasksCount');

    $router->get('count/goal/tasks/{goal_id}', 'ActivitiesController@goalTasksCount');

});

$router->group(['prefix' => 'api/teams'], function () use ($router) {

    $router->get('show', 'TeamsController@index');

    $router->get('{team_id}/show', 'TeamsController@showOne');

    $router->post('create', 'TeamsController@storeTeam');

    $router->put('{team_id}/edit', 'TeamsController@updateTeam');

    $router->delete('{team_id}/delete', 'TeamsController@destroy');

});

$router->group(['prefix' => 'api/teams'], function () use ($router) {

    $router->post('members/search', 'TeamMembersController@searchTeamMember');

    $router->get('{team_id}/members/show', 'TeamMembersController@getteamMembers');

    $router->get('show/team/makers', 'TeamMembersController@getTeamMakers');

    $router->post('{team_id}/member/add', 'TeamMembersController@addMember');

    $router->delete('teamMember/{teamMember_id}/delete', 'TeamMembersController@destroyTeamMember');

});



