<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: token');

$domain = getenv('ROOTDOMAIN');
if(empty($domain))
	$domain = 'moadonofesh.co.il';

Route::group(array('domain' => "{subdomain}.$domain"), function(){
	Route::get('/', 'SiteIndexController@index');
	Route::get('options', 'SiteClubsController@options');
	Route::get('supplier/{id}','SiteClubsController@supplier');
	Route::get('search', 'SiteClubsController@search');

	Route::post('login', 'SiteClubsController@login');
	Route::get('logout', 'SiteClubsController@logout');
	Route::post('cart','SiteCartController@cart');

});

Route::group(array('prefix' => 'admin'), function()
{

	Route::get('/', function(){ return View::make('admin.index'); });
	
	Route::post('login',	'AdminLoginController@login');
	Route::get('logout',	'AdminLoginController@logout');
	Route::post('restore',	'AdminLoginController@restore');
	Route::get('options','AdminOptionsController@index');

	Route::group(array('before' => 'auth'), function() 
	{
		Route::resource('clubs','AdminClubsController');
		Route::resource('members','AdminMembersController');
		Route::resource('suppliers','AdminSuppliersController');
		Route::resource('items','AdminItemsController');
		Route::resource('users','AdminUsersController');
		Route::resource('cities','AdminCitiesController');
		Route::resource('orders','AdminOrdersController');
		Route::resource('clients','AdminClientsController');
		Route::resource('categories','AdminCategoriesController');
		Route::resource('regions','AdminRegionsController');
		Route::resource('sitedetails','AdminSiteDetailsController');
		Route::post('sitedetails/minisite/{id}','AdminSiteDetailsController@miniSite');
		Route::post('{id}/uploadImage','AdminImagesController@uploadImage');

	});
<<<<<<< HEAD
=======
});

Route::group(array('prefix' => 'clubs'), function()
{
	//temp 
	
	Route::get('search','ClubsController@search');

	Route::group(array('before' => 'club_auth'), function() 
	{
>>>>>>> d617108301e0b5d60700c5c5749414ee14d291e3

});

