<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher AS hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Helpers\Api;

class AuthController extends Controller
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

    /**
    * @SWG\Post(
    *     path="/auth",
    *     consumes={"multipart/form-data"},
    *     description="Login Lendtick",
    *     operationId="auth",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="Email for login Lendtick",
    *         in="formData",
    *         name="username",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Password bond to that email",
    *         in="formData",
    *         name="password",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Authentication",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function auth(Request $request, hash $hash){
        $credentials = $request->only('username', 'password'); // grab credentials from the request

        try {
            $check_pass = ($data = User::where('username',$credentials['username'])->where('deleted_by')->first())?$hash->check($credentials['password'], $data->password):false;
            // check user
            if(!$data) throw New JWTException("User Not Found", 404);
            // check password
            if(!$check_pass) throw New JWTException("Password Invalid", 401);
            // check token
            if (!$token = JWTAuth::attempt($credentials)) throw New JWTException("Invalid Credential", 401);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(Api::response(false,$e->getMessage()), $e->getCode());
        }

        // update last login field in [user].[User]

        return response()->json(Api::response(true,'Success Login',['token' => "Bearer $token"]));
    }

    public function check(Request $request){
        try {
            // set request to parser
            JWTAuth::parser()->setRequest($request);
            // validate payload
            if ($user = !(JWTAuth::parseToken()->authenticate())) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (JWTException $e) {
            if($e->getMessage()=="Token has expired"){
                try{
                    $token = JWTAuth::refresh($request->header('Authorization'));
                } catch(JWTException $e){
                    return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => null], 401);
                }
            }
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => isset($token)?['token'=>'Bearer '.$token]:null], 401);
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    public function refresh(Request $request){
        try{
            $token = JWTAuth::refresh($request->header('Authorization'));
        } catch(JWTException $e){
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => null], 401);
        }

        return response()->json(['status' => false, 'message' => 'Token refresh', 'data' => ['token'=>'Bearer '.$token]], 200);
    }
}
