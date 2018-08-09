<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Api;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(){
        return response()->json(Api::response(true,'Success'),200);
    }
}
