<?php

namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiResponseClass
{
    private static $statusMessages = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error'
    ];

    public static function rollback($e, $message ="Something went wrong! Process not completed"){
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message ="Something went wrong! Process not completed"){
        Log::info($e);
        throw new HttpResponseException(response()->json(["message"=> $message], 500));
    }

    public static function sendResponse($data , $message ,$code=200, $merged = false){
        if($merged) {
            return $data;
        }
        $response['success'] = true;
        $response = [
            'success' => true,
            'data' => $data
        ];
        if(!empty($message)){
            $response['message'] = $message;
        } else {
            $response['message'] = self::$statusMessages[$code];
        }
        return response()->json($response, $code);
    }

}