<?php
namespace App\Http\Controllers;

trait ApiResponsePaginationTrait{

    public function ApiResponsePaginationTrait($data=null,$message=null,$status,$code){


        $array=[

            'status'=>$status,
            'code'=>$code,
            'message'=>$message,
            'data'=>$data,
           'pagination'=>formatPagination($data)

        ];

        return response()->json($array, $code);

    }
}


