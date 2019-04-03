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

    $router->delete('goals/{goal_id}/delete', 'GoalsController@destroy');

});

$router->group(['prefix' => 'api/goals/'], function () use ($router) {

    $router->get('{goal_id}/tasks', 'GoalsTasksController@index');

    $router->get('{goal_id}/tasks/{task_id}', 'GoalsTasksController@show');

    $router->post('{goal_id}/tasks/create', 'GoalsTasksController@store');

    $router->put('{goal_id}/tasks/{task_id}/edit', 'GoalsTasksController@update');

    $router->delete('{goal_id}/tasks/{task_id}/delete', 'GoalsTasksController@destroy');

});

$router->group(['prefix' => 'api/'], function () use ($router) {

    $router->get('activities', 'ActivitiesController@index');

});
