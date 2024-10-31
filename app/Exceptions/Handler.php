<?php

namespace App\Exceptions;

use Throwable;
use ErrorException;
use App\Enums\HttpStatusEnum;
use App\Services\JsonResponseService;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $jsonResponseService = app(JsonResponseService::class);

        // Ensure JSON response if expected
        if ($request->expectsJson()) {
            $errors[] = $exception->getMessage();

            // Handle validation exceptions
            if ($exception instanceof ValidationException) {
                $errors[] = __('responses.422');

                return $jsonResponseService->jsonResponse(
                    status: HttpStatusEnum::ERROR->value(),
                    httpCode: 422,
                    errors: $errors
                );
            }

            // Handle 404 exceptions
            if ($exception instanceof ErrorException || $exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                $errors[] = __('responses.404');

                return $jsonResponseService->jsonResponse(
                    httpCode: 404,
                    status: HttpStatusEnum::ERROR->value(),
                    errors: $errors
                );
            }

            // Handle other exceptions
            $errors[] = __('responses.500');
            // Create a nicely structured log with unique query id.
            \Log::error($errors);

            return $jsonResponseService->jsonResponse(
                status: HttpStatusEnum::ERROR->value(),
                httpCode: 500,
                errors: $errors,
            );
        }

        return parent::render($request, $exception);
    }
}
