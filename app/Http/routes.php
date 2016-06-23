<?php
/**
 * Copyright (c) Unknown Worlds Entertainment, 2016. 
 * Created by Lukas Nowaczek <lukas@unknownworlds.com> <@lnowaczek>
 * Visit http://unknownworlds.com/
 * This file is a part of proprietary software. 
 */

// Frontend - public
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('translations', 'TranslationsController@index');
Route::get('pages/{name}', 'PagesController@index');
Route::get('theme/{name}', 'ThemesController@index');

// Backend
Route::resource('users', 'UsersController');
Route::resource('languages', 'LanguagesController');
Route::resource('roles', 'RolesController');
Route::resource('projects', 'ProjectsController');
Route::get('tools/file-import', 'ToolsController@fileImport');
Route::post('tools/file-import', 'ToolsController@processFileImport');

// Auth
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

// API
Route::group(array('prefix' => 'api'), function () {
	// Frontend
	Route::get('/base-strings', 'TranslationsController@baseStrings');
	Route::post('/base-strings', 'TranslationsController@storeBaseString');
	Route::post('/base-strings/trash', 'TranslationsController@trashBaseString');
	Route::get('/strings', 'TranslationsController@strings');
	Route::get('/check-privileges', 'TranslationsController@checkPrivileges');
	Route::post('/strings/store', 'TranslationsController@store');
	Route::post('/strings/trash', 'TranslationsController@trash');
	Route::post('/strings/accept', 'TranslationsController@accept');
	Route::post('/strings/vote', 'TranslationsController@vote');
	Route::get('/strings/history', 'TranslationsController@translationHistory');
	Route::get('/strings/users', 'TranslationsController@users');
	Route::get('/strings/admins', 'TranslationsController@admins');
	Route::get('/admin-whiteboard/{project_id}/{language_id}', 'AdminWhiteboardsController@find');
	Route::post('/admin-whiteboard', 'AdminWhiteboardsController@store');

	// Backend
	Route::post('/strings/translation-file', 'TranslationFilesController@storeInputFile');
	Route::get('/strings/translation-file', 'TranslationFilesController@processOutputFiles');
});
