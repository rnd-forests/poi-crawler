<?php

namespace App\Modules\Shared\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs;

    public function res($data = null, $message = null, $status = 200)
    {
        return new JsonResponse([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function resSuccess($data = null, $message = 'Success', $status = 200)
    {
        return $this->res($data, $message, $status);
    }

    public function resNoContent($message = 'Success', $status = 201)
    {
        return new JsonResponse([
            'message' => $message,
        ], $status);
    }

    public function resBadRequest($message = 'Bad request')
    {
        return new JsonResponse([
            'message' => $message,
        ], 400);
    }
}
