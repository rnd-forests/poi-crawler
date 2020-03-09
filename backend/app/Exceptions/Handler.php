<?php

namespace App\Exceptions;

use Exception;
use App\Events\SlackNotifyEvent;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BadRequestException::class,
        NotFoundException::class,
        UnauthorizedException::class,
        ValidationException::class,
        ModelNotFoundException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        CommandNotFoundException::class,
        OAuthServerException::class,
    ];

//    /**
//     * A list of the inputs that are never flashed for validation exceptions.
//     *
//     * @var array
//     */
//    protected $dontFlash = [
//        'password',
//        'password_confirmation',
//    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $e
     * @return void
     * @throws Exception
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e) && config('logging.slack_error_notify')) {
            $this->notifySlack($e);
        }

        parent::report($e);
    }

//    /**
//     * Render an exception into an HTTP response.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \Exception  $e
//     * @return \Illuminate\Http\Response|JsonResponse
//     */
//    public function render($request, Exception $e)
//    {
////        if ($request->ajax() || $request->wantsJson())
////        {
////            $json = [
////                'message' => $e->getMessage(),
////                'errors' => [
////                    'code' => $e->getCode(),
////                ],
////            ];
////
////            return new JsonResponse($json, 400);
////        }
//        return parent::render($request, $e);
//    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    protected function notifySlack($e)
    {
        event(new SlackNotifyEvent(exceptionToSlackMessage($e)));
    }
}
