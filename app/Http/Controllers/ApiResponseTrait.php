<?php
namespace App\Http\Controllers;

trait ApiResponseTrait{

    public function apiRespose($data=null,$message=null,$status,$code){


        $array=[
            'status'=>$status,
            'code'=>$code,
            'message'=>$message,
            'data'=>$data,

        ];

        return response()->json($array, $code);
    }
}


