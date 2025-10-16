<?php
namespace App\Helpers;

class ResponseModel {
    public static function success($data = []) {
        return response()->json([
            'data' => $data,
            'error' => null
        ]);
    }

    public static function error($msg, $code = 500) {
        return response()->json([
            'data' => null,
            'error' => $msg
        ], $code);
    }
}
