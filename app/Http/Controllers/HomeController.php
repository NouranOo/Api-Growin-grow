<?php

namespace App\Http\Controllers;

class HomeController extends Controller
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
 * @OA\Info(title="Home controller api", version="0.1")
 * 
 */

/**
 * @OA\Get(
 *     path="/index",
 * 
 *     summary="say hello for user by id",
 *   
 * 
 *     @OA\Response(response="200", description="hey ")
 * )
 */
    public function sayHay(){
     return "hey";
    }
}
