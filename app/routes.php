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

function RegisterRouteForDomain($base)
{ 
	Route::group(array('domain' => $base), function(){
		Route::get('v{id}', 'SiteOrdersController@showOrder');
		Route::get('/', 'SiteIndexController@index');
		Route::get('site/options', 'SiteClubsController@options');
		Route::post('login', 'SiteClubsController@login');
		Route::get('logout', 'SiteClubsController@logout');
		Route::get('home','SiteClubsController@suppliers');
		Route::post('account/login', 'SiteClientController@login');
		Route::post('account/register', 'SiteClientController@register');
		Route::post('account/restore', 'SiteClientController@passReminder');
		Route::get('account/logout', 'SiteClientController@logout');

		Route::group(array('before' => 'ClubAuth'), function(){
			Route::get('supplier/{id}','SiteClubsController@supplier');
			Route::get('search', 'SiteClubsController@search');
			Route::post('cart','SiteCartController@cart');
			Route::post('register', 'SiteClientController@register');
			//Route::post('remined/password', 'SiteClientController@passReminder');

			Route::get('newsuppliers', 'SiteClubsController@newsuppliers');
			Route::get('mostviewed', 'SiteClubsController@mostviewed');
			Route::get('hotdeals', 'SiteClubsController@hotdeals');

			Route::group(array('before' => 'ClubClientAuth'), function(){

				Route::resource('orders', 'SiteOrdersController');
				Route::get('info', 'SiteClientController@userInfo');
				Route::post('update/info', 'SiteClientController@updateInfo');

			});
			
		});

	});
}

RegisterRouteForDomain("{subdomain}.$domain");
RegisterRouteForDomain("$domain");

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
		Route::get('suppliersReport','AdminReportsController@suppliersReport');
		Route::resource('pages','AdminPagesController');
		Route::resource('settings','AdminSettingsController');
	});

});


Route::group(array('prefix' => 'suppliers'), function()
{

	Route::get('/', function(){ return View::make('supplier.index'); });
	
	Route::post('login',	'SupplierLoginController@login');
	Route::get('logout',	'SupplierLoginController@logout');
	Route::post('restore',	'SupplierLoginController@restore');
	Route::get('options','SupplierOptionsController@index');

	Route::group(array('before' => 'auth_supplier'), function() 
	{
		Route::get('order/{id}','SupplierOrderController@order');
		Route::post('realize','SupplierOrderController@realize');
		Route::get('realizations','SupplierReportsController@realizations');
	});

});


Route::get('v{key}','SiteOrdersController@viewOrder');

Route::get('orders/report','AccountingController@orders');