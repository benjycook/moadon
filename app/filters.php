<?php

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
// Route::filter('logged', function()
// {
// 	$token = Request::header('token');
// 	$user = DB::table('tokens')->whereRaw('token = ?',array($token))->first();
// 	if($user)
// 	{
// 		Session::put('user', $user);
// 	}
// 	else
// 	{
// 		return Response::json('error',401);
// 	}
// });

Route::filter('TokenAuth', function ($route, $request, $value = null) {
	$header =	$request->header('authorization', null);
	if($header)
	{
		list($nop, $token) = explode('Bearer ', $header);
	}
//"token":"eyJ0eXAiOiJKV1QifQ==.eyJjbHViIjoxLCJ1c2VyIjpudWxsfQ==.YmNlNmEwMDY2YjUxMzZkYzAwNzU5MzkyZDU0ZjM5OTc=","loginType":"club"
	$parts = explode('.', $token);
	if(count($parts) != 3)
		return Response::json(['error' => 'invalid token parts'], 200);
	//verify token
	$data = $parts[0] . $parts[1];
	if(md5($data) != base64_decode($parts[2]))
		return Response::json(['error' => 'invalid token signature'], 200);
});

Route::filter('auth', function()
{
	Config::set('auth.model', 'User');
	if (Auth::guest()) return Response::json('error',401);
});

Route::filter('clientAuth', function($route)
{
	Config::set('auth.model','Client');
	if (Auth::guest()) return Response::json('error',401);
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
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
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});