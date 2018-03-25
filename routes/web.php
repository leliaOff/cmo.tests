<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WelcomeController@index');

/* Welcome */
Route::post('/login', 'WelcomeController@login');
Route::post('/registration', 'WelcomeController@registration');
Route::post('/logout', 'WelcomeController@logout');
Route::post('/frontTestsList', 'Front\TestsController@select');
Route::post('/frontGetTest', 'Front\TestsController@get');
Route::post('/frontResult', 'Front\TestsController@result');
Route::post('/frontDirectoryData', 'Front\DirectoriesController@select');

/* Tests */ 
Route::post('/testsList', 'Manager\TestsController@select');
Route::post('/testGet', 'Manager\TestsController@get');
Route::post('/testInsert', 'Manager\TestsController@insert');
Route::post('/testUpdate', 'Manager\TestsController@update');
Route::post('/testDelete', 'Manager\TestsController@delete');

/* Elements */ 
Route::post('/elementsList', 'Manager\ElementsController@select');
Route::post('/elementGet', 'Manager\ElementsController@get');
Route::post('/elementInsert', 'Manager\ElementsController@insert');
Route::post('/elementUpdate', 'Manager\ElementsController@update');
Route::post('/elementDelete', 'Manager\ElementsController@delete');
Route::post('/elementSort', 'Manager\ElementsController@sort');
Route::post('/elementsFileUpload', 'Manager\ElementsController@uploadFile');

/* Results */ 
Route::post('/resultsList',                             'Manager\ResultsController@select');
Route::post('/resultsByAnswer',                         'Manager\ResultsController@getResultStat');
Route::get('/getExcel/{id}',                            'Manager\ExcelController@get');
Route::get('/cleanResults',                             'Manager\ResultsController@cleanResults');
Route::get('/archiveResults',                           'Manager\ResultsController@archiveResults');

/* Directories */ 
Route::post('/directoriesList',                         'Manager\DirectoriesController@select');
Route::post('/directoryGet',                            'Manager\DirectoriesController@get');
Route::post('/directoryInsert',                         'Manager\DirectoriesController@insert');
Route::post('/directoryUpdate',                         'Manager\DirectoriesController@update');
Route::post('/directoryDelete',                         'Manager\DirectoriesController@delete');

/* Schools */ 
Route::post('/schoolsList',                             'Manager\SchoolsController@select');
Route::post('/schoolGet',                               'Manager\SchoolsController@get');
Route::post('/schoolInsert',                            'Manager\SchoolsController@insert');
Route::post('/schoolUpdate',                            'Manager\SchoolsController@update');
Route::post('/schoolDelete',                            'Manager\SchoolsController@delete');

/* Municipalities */ 
Route::post('/municipalitiesList',                      'Manager\MunicipalitiesController@select');
Route::post('/municipalityGet',                         'Manager\MunicipalitiesController@get');
Route::post('/municipalityInsert',                      'Manager\MunicipalitiesController@insert');
Route::post('/municipalityUpdate',                      'Manager\MunicipalitiesController@update');
Route::post('/municipalityDelete',                      'Manager\MunicipalitiesController@delete');

/* Users */
Route::post('/usersList',                               'Manager\UsersController@select');
Route::post('/userGet',                                 'Manager\UsersController@get');
Route::post('/userInsert',                              'Manager\UsersController@insert');
Route::post('/userUpdate',                              'Manager\UsersController@update');
Route::post('/userDelete',                              'Manager\UsersController@delete');

/* Test's links */ 
Route::get('/testsLinks/{testId}',                      'Manager\TestsLinksController@index');
Route::get('/testsLinkInsert/{testId}/{directoryId}',   'Manager\TestsLinksController@insert');
Route::get('/testsLinkDelete/{id}',                     'Manager\TestsLinksController@delete');
Route::get('/links/{testId}',                           'Manager\TestsLinksController@links');