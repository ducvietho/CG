<?php
namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{

    public function successResponse($data, $code = Response::HTTP_OK)
    {

        return response($data, $code)->header('Content-Type', 'application/json');
    }

    public function errorResponse($message, $code)
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => new \stdClass(),
        ]);
    }

    public function errorMessage($message, $code)
    {
        return response($message, $code)->header('Content-Type', 'application/json');
    }

    public function successResponseMessage($data, $code = Response::HTTP_OK, $message)
    {

        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
