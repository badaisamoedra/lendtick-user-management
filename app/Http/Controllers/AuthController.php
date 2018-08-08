<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher AS hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

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

    public function auth(Request $request, hash $hash){
        $credentials = $request->only('Username', 'password'); // grab credentials from the request

        $check_pass = ($data = User::where('Username',$credentials['Username'])->where('DeletedBy')->first())?$hash->check($credentials['password'], $data->password):false;
        if(!$data) return response()->json(['error' => 'user_not_found'], 404);
        if(!$check_pass) return response()->json(['error' => 'pass_invalid'], 401);

        try {
            if (!$token = JWTAuth::attempt($credentials)) { // attempt to verify the credentials and create a token for the user
                // update last login field in [user].[User]
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500); // something went wrong whilst attempting to encode the token
        }

        return response()->json(['token' => "Bearer $token"]);
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
