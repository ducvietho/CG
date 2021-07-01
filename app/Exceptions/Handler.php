<?php

namespace App\Exceptions;

use Exception;
use ErrorException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
//        Log::info($exception);
//         dd($exception);
        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exits any instance of {$model} with the given", 404);
        }
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessages(), 403);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse("Please login again", 401);
        }
        if ($exception instanceof ValidationException) {

            return $this->errorResponse($exception->validator->errors()->getMessages(), 414);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {

            return $this->errorResponse("Method not allowed", 405);
        }
        if ($exception instanceof QueryException) {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        if ($exception instanceof TokenInvalidException) {
            return $this->errorResponse("Token valid", 463);
        }
        if ($exception instanceof TokenExpiredException) {
            return $this->errorResponse("Token expired", 406);
        }
        if ($exception instanceof JWTException) {
            return $this->errorResponse($exception->getMessage(), 410);
        }
        if ($exception instanceof UnauthorizedHttpException) {
            return $this->errorResponse($exception->getMessage(), 406);
        }
        if ($exception instanceof FatalErrorException) {
            return $this->errorResponse($exception->getMessage(), 407);
        }
        if ($exception instanceof MassAssignmentException) {
            return $this->errorResponse($exception->getMessage(), 408);
        }
        if ($exception instanceof ErrorException) {
            return $this->errorResponse($exception->getMessage(), 409);
        }
        if ($exception instanceof InvalidArgumentException) {
            return $this->errorResponse($exception->getMessage(), 411);
        }
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse("URL not found", 415);
        }
        if (env('APP_DEBUG') == false) {
            return parent::render($request, $exception);
        }
        return $this->errorResponse("Unexpected error. Try later", 500);
    }
}
