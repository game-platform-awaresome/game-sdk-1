<?php


namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionReport
{
    /**
     * Convert the given exception to an array.
     *
     * @param Exception $exception
     * @param int $status
     * @return array
     */
    public static function convertExceptionToArray(Exception $exception, int $status)
    {
        $message = self::message($exception);
        return config('app.debug') ? [
            'status' => $status,
            'message' => $message,
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'status' => $status,
            'message' => $message,
            'data' => null
        ];
    }

    /**
     * @param Exception $exception
     * @return array|string|null
     */
    protected static function message(Exception $exception)
    {
        // 如果是QueryException打印sql语句
        if ($exception instanceof QueryException) {
            Log::error($exception->getSql());
        }
        // 区分开
        if ($exception instanceof RenderException) {
            $message = __($exception->getMessage());
        } else if ($exception instanceof NotFoundHttpException) {
            $message = __('Not Found');
        } else {
            Log::error('exception message: ' . $exception->getMessage());
            $message = empty($exception->getMessage()) || !config('app.debug') ? __('Server Error') : $exception->getMessage();
        }
        Log::error('last error message: ' . $message);
        return $message;
    }
}