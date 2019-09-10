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
//Authorization For Admin
$router->post('api/admin/signin', 'AdminSignInController@check');

//Authentication Unauthorize Access
$router->post('api/signup', 'UserSignUpController@store');

$router->post('api/signin', 'UserSignInController@check');

$router->get('api/tokendestroy', 'GeneralTokenDestroyController@tokenDestroy');

$router->get('api/confirmation/{confirmtoken}', 'UserConfirmationController@confirm');

$router->post('api/verify/email', 'UserVerifyTokenController@validateUser');

$router->put('api/reset/password', 'UserConfirmationController@resetPassword');

//WorkSpaces Authourize Access Users
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {

    $router->post('workspace/request', 'UserWorkSpacesController@request');

    $router->post('workspace/{company_id}/create', 'UserWorkSpacesController@store');

    $router->put('workspace/edit', 'UserWorkSpacesController@update');

    $router->get('workspaces', 'UserWorkSpacesController@show');

    $router->get('workspace/{id}', 'UserWorkSpacesController@showOne');

});
//Company Authourize Access Users
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {

    $router->post('company/request', 'UserCompaniesController@request');

    $router->post('company/create', 'UserCompaniesController@store');

    $router->put('company/edit', 'UserComapaniesController@update');

    $router->get('company', 'UserComapaniesController@show');

    $router->get('company/{id}', 'UserComapaniesController@showOne');

});
//categories
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {
//Admin 
    $router->post('category/create', 'AdminCategoriesController@create');

    $router->get('categories', 'AdminCategoriesController@showAll');

    $router->get('category/{id}', 'AdminCategoriesController@show');


    $router->put('category/edit/{id}', 'AdminCategoriesController@update');

    $router->delete('category/delete/{id}', 'AdminCategoriesController@destroy');
//User

    $router->get('user/categories', 'UserCategoriesController@showAll');

    $router->get('user/category/{id}', 'UserCategoriesController@show');

});
//interest
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {
//Admin

    $router->post('interest/create/{category_id}', 'AdminInterestController@create');

    $router->put('interest/edit/{category_id}/{id}', 'AdminInterestController@update');

    $router->delete('interest/delete/{id}', 'AdminInterestController@destroy');
//User
    $router->post('interest/select', 'UserInterestController@select');
});

//Projects Routes
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {
    $router->get('projects', 'UserProjectController@showAll');

    $router->get('project/{workspace_id}/{company_id}/{id}', 'UserProjectController@showOne');

    $router->post('project/{workspace_id}/{company_id}/create', 'UserProjectController@create');

    $router->put('project/{id}/edit', 'UserProjectController@update');

    $router->delete('project/{id}/delete',  'UserProjectController@destroy');
});

//Add Member Routes
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {
    $router->post('add/member/{workspace_id}/{company_id}/{member_id}', 'UserWorkSpaceMemberController@addMember');
});
//Search plannerr Generally
$router->group(['middleware' => 'auth', 'prefix' => 'api/'], function () use ($router) {
    $router->post('search/table/?{search_param}', 'UserWorkSpaceMemberController@addMember');
});
//
// //Get request to show authourize dashboard
//
// $router->group(['prefix' => 'api/'], function () use ($router) {
//
//     $router->get('profile', 'ProfileController@index');
//
//     $router->put('profile/edit', 'ProfileController@update');
//
//     $router->delete('profile/delete', 'ProfileController@destroy');
//
//     //Imgae Upload
//
//     $router->post('profile/upload', 'ImagesController@upload');
//
// });
//
// $router->group(['prefix' => 'api/'], function () use ($router) {
//
//     $router->get('goals', 'GoalsController@index');
//
//     $router->get('goals/{goal_id}', 'GoalsController@show');
//
//     $router->post('goals/create', 'GoalsController@store');
//
//     $router->put('goals/{goal_id}/edit', 'GoalsController@update');
//
//     $router->put('goals/{goal_id}/status', 'GoalsController@updateGoalStatus');
//
//     $router->delete('goals/{goal_id}/delete', 'GoalsController@destroy');
//
// });
//
// $router->group(['prefix' => 'api/goals/'], function () use ($router) {
//
//     $router->get('{goal_id}/tasks', 'GoalsTasksController@index');
//
//     $router->get('{goal_id}/tasks/{task_id}', 'GoalsTasksController@show');
//
//     $router->post('{goal_id}/tasks/create', 'GoalsTasksController@store');
//
//     $router->put('{goal_id}/tasks/{task_id}/edit', 'GoalsTasksController@update');
//
//     $router->put('{goal_id}/tasks/{task_id}/status', 'GoalsTasksController@updateTaskStatus');
//
//     $router->delete('{goal_id}/tasks/{task_id}/delete', 'GoalsTasksController@destroy');
//
// });
//
// $router->group(['prefix' => 'api/'], function () use ($router) {
//
//     $router->get('activities', 'ActivitiesController@index');
//
//     $router->get('count/goals', 'ActivitiesController@goalsCount');
//
//     $router->get('count/tasks', 'ActivitiesController@tasksCount');
//
//     $router->get('count/goal/tasks/{goal_id}', 'ActivitiesController@goalTasksCount');
//
//     $router->get('count/members', 'ActivitiesController@allTeamMemberCount');
//
//     $router->get('count/members/{team_id}', 'ActivitiesController@allSigleTeamMemberCount');
//
//     $router->get('count/assigned/members', 'ActivitiesController@assignedTaskCount');
//
//     $router->get('refresh/chat/status/{member_id}', 'ActivitiesController@refreshChatStatus');
//
// });
//
// $router->group(['prefix' => 'api/teams'], function () use ($router) {
//
//     $router->get('show', 'TeamsController@index');
//
//     $router->get('{team_id}/show', 'TeamsController@showOne');
//
//     $router->post('create', 'TeamsController@storeTeam');
//
//     $router->put('{team_id}/edit', 'TeamsController@updateTeam');
//
//     $router->delete('{team_id}/delete', 'TeamsController@destroy');
//
// });
//
// $router->group(['prefix' => 'api/teams'], function () use ($router) {
//
//     $router->post('members/search', 'TeamMembersController@searchTeamMember');
//
//     $router->get('{team_id}/members/show', 'TeamMembersController@getteamMembers');
//
//     $router->get('show/team/makers', 'TeamMembersController@getTeamMakers');
//
//     $router->post('{team_id}/member/add', 'TeamMembersController@addMember');
//
//     $router->delete('member/{team_member_id}/delete', 'TeamMembersController@destroyTeamMember');
//
// });
//
// $router->group(['prefix' => 'api/'], function () use ($router) {
//
//     $router->put('task/assign', 'AssignTaskController@assignTask');
//
//     $router->put('task/revert', 'AssignTaskController@removeTask');
//
//     $router->get('show/assigned/task/to', 'AssignTaskController@showAssignedTo');
//
//     $router->get('show/assigned/task/from', 'AssignTaskController@showAssignedFrom');
//
//
// });
