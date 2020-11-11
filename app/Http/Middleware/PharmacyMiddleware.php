<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\pharmacy;
use Illuminate\Http\Request;
use validator;

class PharmacyMiddleware
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
        $rules = [
        
            'ApiToken'=>'required'

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, 'code'=>'403' , "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }
        
        $pharmacy=pharmacy::where('ApiToken',$request->ApiToken)->first();
        $error=array('status'=>'false','code'=>'403','message'=>'UnAtuorized..');
        
        if(empty($pharmacy)){
            return response()->json($error);
        }


        return $next($request);
    }
}
