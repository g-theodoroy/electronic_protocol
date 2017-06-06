<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
	$allowregister = True;
	$file = storage_path('conf/.denyregister');
	if (file_exists($file ))$allowregister = False;
	return view('welcome', compact('allowregister'));
});

//Auth::routes();
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
$file = storage_path('conf/.denyregister');
if (file_exists($file )){
    Route::get('register', 'Auth\LoginController@showLoginForm')->name('login');
}else{
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
}
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
// End Auth::routes();


Route::get('users', 'UserController@index');
Route::post('users', 'UserController@store');
Route::get('users/{user}', 'UserController@index');
Route::post('users/{user}', 'UserController@update');
Route::get('users/del/{user}', 'UserController@delete');

Route::get('keep', 'KeepvalueController@index');
Route::post('keep', 'KeepvalueController@store');
Route::get('keep/{keepvalue}', 'KeepvalueController@index');
Route::post('keep/{keepvalue}', 'KeepvalueController@update');
Route::get('keep/del/{keepvalue}', 'KeepvalueController@delete');

Route::get('/home','ProtocolController@index');
Route::get('/home/list','ProtocolController@indexList');
Route::get('/home/{protocol}','ProtocolController@index');
Route::get('getFileInputs/{num}', 'ProtocolController@getFileInputs');
Route::get('getKeep4Fakelos/{fakelos}', 'ProtocolController@getKeep4Fakelos');

Route::post('/home', 'ProtocolController@store');
Route::post('/home/{protocol}','ProtocolController@update');
Route::get('/delprotocol/{protocol}','ProtocolController@delprotocol');

Route::get('/attach/del/{attachment}','ProtocolController@attachDelete');
Route::get('/goto/{etos}/{protocolnum}','ProtocolController@gotonum');
Route::get('/download/{attachment}','ProtocolController@download');

Route::get('/find','ProtocolController@find');
Route::get('/getFindData','ProtocolController@getFindData');

Route::get('/print','ProtocolController@printprotocols');
Route::post('/printed','ProtocolController@printed');

Route::get('/receipt/{protocol}','ProtocolController@receipt');
Route::get('/about','ProtocolController@about');



Route::get('/config','ConfigController@index');
Route::post('/config','ConfigController@store');

Route::get('/backups','ConfigController@backups');
Route::get('/backup','ConfigController@backup');
Route::get('/downloadBackup/{name}','ConfigController@downloadBackup');
Route::get('/deleteBackup/{name}','ConfigController@deleteBackup');

Route::get('/arxeio','ConfigController@arxeio');
Route::get('/expired','ConfigController@expired');
Route::get('/delExpired','ConfigController@delExpired');
Route::get('/delDeleted','ConfigController@delDeleted');

