<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class AuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $token = $request->header('Authorization');
            if(!$token)
                throw New Exception('Unauthorized',401);
                // $r = (object) RestCurl::exec('GET',env('AUTH_URI',null),[],$token);
                $r = (object) RestCurl::exec('GET','https://lentick-api-user-management-dev.azurewebsites.net/auth/check',[],$token);
            if($r->status !== 200){
                $head = explode("\r\n",$r->header);
                throw New Exception(substr($head[0],13),$r->status);
            }  
        } catch(Exception $e){
            $status = $e->getCode() ? $e->getCode() : 500; 
            return response()->json(Api::response(false, $e->getMessage()),$status);
        }
        return $next($request);
    }
}
