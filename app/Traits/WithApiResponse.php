<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait WithApiResponse
{
    protected function apiResponse($data = null, int $code = 200, string $status = 'success', string $msg = ''): JsonResponse
    {
        $arr = [];

        $arr['status'] = $status;

        if ((is_array($data) && count($data) > 0) || (!is_array($data) && isset($data))) {
            $arr['data'] = $data;
        }

        if ($msg != '') {
            $arr['msg'] = $msg;
        }
        
        return response()->json(
            $arr,
            $code
        );
    }
}