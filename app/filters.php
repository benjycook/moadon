<?php


App::before(function($request)
{
});


App::after(function($request, $response)
{
});


Route::filter('ClubAuth', function(){
	try{
		$token = TokenAuth::getToken();
		if(!$token)
			return Response::json('token_invalid', 401);

		$token = $token->get();

		$payload = TokenAuth::getPayload($token);
	}
	catch(Exception $e)
	{
		return Response::json('token_invalid', 401);
	}

});

Route::filter('ClubClientAuth', function(){
	try{
		$token = TokenAuth::getToken();
		if(!$token)
			return Response::json('token_invalid', 401);

		$token = $token->get();

		$payload = TokenAuth::getPayload($token);
		$payloadArray = $payload->toArray();
		if(!($payloadArray['user'] > 0))
			return Response::json('token_invalid_user', 401);
	}
	catch(Exception $e)
	{
		return Response::json('token_invalid', 401);
	}

});


Route::filter('auth', function()
{
	Config::set('auth.model', 'User');
	if (Auth::guest()) return Response::json('error',401);
});


Route::filter('auth_supplier', function()
{
	Config::set('auth.model', 'Supplier');
	if (Auth::guest()) return Response::json('error',401);
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
});


Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});


Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});