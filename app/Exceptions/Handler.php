<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
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
        \Log::error($exception);
        // if ($request->wantsJson() && $exception instanceof ModelNotFoundException) {
        //     return response()->json($exception->getMessage(), 404);
        // }

        // dd($exception);

        try{
            if($exception->getStatusCode() == 404)
                return response()->json('404 not found.', $exception->getStatusCode());
            
            else if($exception->getMessage() == '') 
                return response()->json($exception->getTrace(), $exception->getStatusCode());
                    
            return response()->json($exception->getMessage(), $exception->getStatusCode());
        }catch(\Throwable $e){
            return response()->json($exception->getMessage(), 500);
        }
    }
}
