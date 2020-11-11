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

$router -> get('/', function ()use ($router) {
return "Grow Api"; 
}); 

/**
 * Auth
 */

$router -> group(['prefix' => 'Api'], function ()use ($router) {
$router -> post('/Register', 'PharmacyController@Register'); 
$router -> post('/Login', 'PharmacyController@Login'); 
$router -> post('/LogOut', 'PharmacyController@LogOut'); 



}); 


$router -> group(['prefix' => 'Api/pharmacy', 'middleware' => 'pharmacyAuth'], function ()use ($router) {

$router -> post('/index', 'HomeController@sayHay'); 

//show one company
$router -> post('/company', 'PharmacyController@showCompany'); 
//show all companies
$router -> post('/companies/all', 'PharmacyController@allCompanies'); 
//show one order for pharmacy with carts
$router -> post('/order', 'PharmacyController@showOrder'); 
//show all orders with carts
$router -> post('/orders/all', 'PharmacyController@allOrders'); 
//search for companies by product
$router -> post('/search', 'PharmacyController@CompanySearch'); 
//compare
$router -> post('/compare', 'PharmacyController@compareCompanies'); 
//store cart
$router -> post('carts/add', 'PharmacyController@StoreCart'); 

// Search for products in company     
$router -> post('company/product/search', 'PharmacyController@SeachInCompany'); 
// get all  products     
$router -> post('products/All', 'PharmacyController@AllProducts'); 
// get all common products for compare
$router -> post('company/commonProduct', 'PharmacyController@CommonProducts'); 
//Add order
$router -> post('order/add', 'PharmacyController@addOrder'); 

}); 

$router -> group(['prefix' => 'Api/pharmacy'], function ()use ($router) {
//show news
$router -> post('news', 'PharmacyController@getNews'); 

$router -> post('cities', 'PharmacyController@cites'); 
$router -> post('area', 'PharmacyController@getareasByCityId'); 
}); 