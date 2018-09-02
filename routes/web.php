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
    // return $router->app->version();
    return redirect('/api/documentation');
});
$router->get('/api', function () use ($router) {
    // return $router->app->version();
    return redirect('/api/documentation');
});

$router->post('/auth', 'AuthController@auth');
$router->get('/auth/check', 'AuthController@check');
$router->get('/auth/refresh', 'AuthController@refresh');
$router->post('/reg', 'UsersController@register');
$router->get('/company/get', 'CompanyController@get');

$router->group(['middleware'=>['authorize']], function() use($router){
    // list approval user
    $router->get('/user/approve/list', 'UsersController@approve_list');

    $router->group(['prefix'=>'user'], function() use($router){
        // approval by HR & Koperasi
        $router->put('approve', function(){
            dd('ok');
        });
    });

    $router->post('/pu', function(Illuminate\Http\Request $request, App\Helpers\BlobStorage $blob) use($router){
        $blob::data([
            'source' => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a6/Goofy.svg/330px-Goofy.svg.png',
            'path' => 'goofy.png'
        ]);
        if(!($res=$blob::upload()))
            dd($blob::error());
        else{
            // print_r($res); die();
            return response()->json($res,200);
        }
    });

    // get address of user 
    $router->group(['prefix'=>'profile'], function() use($router){
        // address
        $router->get('get-address-by-user', 'AddressController@GetListAddressOfUser');
        $router->put('update-address-by-user', 'AddressController@UpdateAddressOfUser');
        $router->post('create-address-by-user', 'AddressController@CreateAddressOfUser');
        $router->post('delete-address-by-user', 'AddressController@DeleteAddressOfUser');

        // profile 
        $router->get('get', 'ProfileController@GetUserProfile');
    });

        // dashboard
     $router->group(['prefix'=>'dashboard'], function() use($router){
        // get
        $router->get('get-completed-by-sbu-hr', 'DashboardController@TotalUserCompletedBySBUHR');
        $router->get('get-completed-by-kopadmin', 'DashboardController@TotalUserCompletedByKopAdmin');
        $router->get('get-pending-by-sbu-hr', 'DashboardController@TotalUserPendingBySBUHR');
        $router->get('get-pending-by-kopadmin', 'DashboardController@TotalUserPendingByKoperasiAdmin');
        
    });
});

## enhance from lutfi 
// change password 
$router->put('change-password', 'ChangePasswordController@get');


