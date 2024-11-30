<?php

namespace App\Traits;

trait ApiResponses
{
    protected function ok($message)
    {
        return $this->success($message);
    }
    protected function success($message, $statusCode = 200)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message
        ], $statusCode);
    }
}
