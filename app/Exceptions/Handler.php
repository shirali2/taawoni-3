<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        // Never expose stack traces or debug info to non-admin sessions
        if (!session()->has('admin') && config('app.debug')) {
            \Illuminate\Support\Facades\Log::error('Non-admin exception: ' . get_class($exception) . ' - ' . $exception->getMessage(), [
                'url'   => $request->fullUrl(),
                'db'    => config('database.connections.mysql.database'),
                'trace' => $exception->getTraceAsString(),
            ]);

            $statusCode = $this->isHttpException($exception)
                ? $exception->getStatusCode()
                : 500;

            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [], $statusCode);
            }

            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }
}
