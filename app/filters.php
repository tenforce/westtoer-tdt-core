<?php

use Tdt\Core\Auth;
/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

/**
*  DataHub specific authentication. User must have 'datahub.view' permission to get access to private datasets
*/
Route::filter('auth.tdt', function()
{
        \LOG::warning('test user');
	$user = Sentry::getUser();
        \LOG::warning($user);
	$superadmin = \Sentry::findGroupByName('superadmin');
        $permissions = 'datahub.view';
        if (!$user) return Redirect::to('api/admin/login?return=' . Request::path());
	if (!$user->hasAccess($permissions) and !$user->inGroup($superadmin)) App::abort(403, 'The authenticated user hasn\'t got the permissions for this action.');
});

/**
* 
*/

Route::filter('auth.tdt2', function()
{
        \LOG::warning('filter (auth.tdt2) called');
	$user = Sentry::getUser();
        \LOG::warning($user);
        $user = strtolower(\Request::header('PHP_AUTH_USER'));
        $password = \Request::header('PHP_AUTH_PW');
        $auth_header = \Request::header('Authorization');

        $debugallheaders=getallheaders(); 
        \Log::warning("Authentication request.");
        \Log::warning($auth_header);
        \Log::warning($debugallheaders);
        if (!empty($auth_header)) {
            list($user, $password) = explode(':', base64_decode(substr($auth_header, 6)));}
        \LOG::warning('filter (auth.tdt2) testing user account');
        \LOG::warning($user);
	$superadmin = \Sentry::findGroupByName('superadmin');
        \LOG::warning($superadmin);
	$myuser = \Sentry::findUserByLogin($user);
        \LOG::warning($myuser);
        $permissions = 'datahub';
        if (!$user) return Redirect::to('api/admin/login?return=' . Request::path());
	if (!$myuser->hasAccess($permissions) and !$myuser->inGroup($superadmin)) {
	   \LOG::warning('testing of user access permissions and group membership failed');
	   App::abort(403, 'The authenticated user hasn\'t got the permissions for this action.');
        };
});

/**
*  Require authentication on all dataset routes except the ones starting with 'open' and root '/'
*  Authentication on routes starting with 'api', 'discovery' and 'spectql' is handled by their specific controllers
*/
//Route::whenRegex('/^(?!api|discovery|spectql|open|\/)(.*)$/', 'auth.tdt');
Route::whenRegex('/^(?!api|discovery|spectql|open|\/)(.*)$/', 'auth.tdt2');

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('auth.basic2', function()
{
	return Auth::basicAuth();
});
/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token')) {
		throw new Illuminate\Session\TokenMismatchException;
	}
});
