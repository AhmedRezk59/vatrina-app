<?php

namespace App\Exceptions;

use App\Http\Traits\Helpers\ApiResponseTrait;
use App\Traits\WithApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use NotificationChannels\WhatsApp\Exceptions\CouldNotSendNotification;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    use WithApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     *
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        $ignoreable_exception_messages = ['Unauthenticated or Token Expired, Please Login'];
        $ignoreable_exception_messages[] = 'The resource owner or authorization server denied the request.';
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            if (!in_array($exception->getMessage(), $ignoreable_exception_messages)) {
                app('sentry')->captureException($exception);
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof PostTooLargeException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: "Size of attached file should be less " . ini_get("upload_max_filesize") . "B",
                    code: 400
                );
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: $exception->getMessage(),
                    code: 400
                );
            }
            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: $exception->getMessage(),
                    code: 403
                );
            }

            if ($exception instanceof CouldNotSendNotification) {
                return $this->apiResponse(
                    status: 'error',
                    msg: $exception->getMessage(),
                    code: 400
                );
            }

            if ($exception instanceof AuthenticationException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: 'Unauthenticated or Token Expired, Please Login',
                    code: 401
                );
            }
            if ($exception instanceof ThrottleRequestsException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: 'Too Many Requests,Please Slow Down',
                    code: 429
                );
            }
            if ($exception instanceof ModelNotFoundException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: $exception->getMessage(),
                    code: 404
                );
            }
            if ($exception instanceof ValidationException) {

                return $this->apiResponse(
                    status: 'error',
                    msg: $exception->getMessage(),
                    data: $exception->errors(),
                    code: 422
                );
            }
            if ($exception instanceof QueryException) {

                return $this->apiResponse(
                    status: 'error',
                    msg: app()->isProduction() ? 'There was Issue with the Query' : $exception->getMessage(),
                    code: 500
                );
            }
            if ($exception instanceof JWTException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: $exception->getMessage(),
                    code: 401
                );
            }
            if ($exception instanceof NotFoundHttpException) {
                return $this->apiResponse(
                    status: 'error',
                    msg: 'Route Not found.',
                    code: 404
                );
            }
            // if ($exception instanceof HttpResponseException) {
            //     // $exception = $exception->getResponse();
            //     return $this->apiResponse(
            //         [
            //             'success' => false,
            //             'message' => "There was some internal error",
            //             'exception'  => $exception
            //         ],
            //         500
            //     );
            // }
            if ($exception instanceof \Error) {
                return $this->apiResponse(
                    status: 'error',
                    msg: app()->isProduction() ? "There was some internal error" : $exception->getMessage(),
                    code: 500
                );
            }
        }


        return parent::render($request, $exception);
    }

    /**
     * @param Throwable $e
     * @return array
     */
    protected function convertExceptionToArray(Throwable $e): array
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ] : [
            'status' => "error",
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
            "code" => $e->getCode(),
        ];
    }

}