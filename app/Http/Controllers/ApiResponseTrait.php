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

    public function apiResponse($data=null,$message=null,$status,$code){


        $array=[
            'status'=>$status,
            'code'=>$code,
            'message'=>$message,
            'data'=>$data,

        ];

        return response()->json($array, $code);
    }


    public function ApiResponsePaginationTrait($paginator=null, $message=null, $status, $code)
    {
        $response = [
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => formatPagination($paginator)
        ];

        return response()->json($response, $code);
    }

}


