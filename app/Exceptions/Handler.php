<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        RenderException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * 异常处理
     * ReaderException 自定义异常，使用自定义的reader方法抛出
     * 系统自带的异常，覆盖父类的prepareJsonResponse抛出
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Exception $exception)
    {
        return new JsonResponse(
            ExceptionReport::convertExceptionToArray($exception, Code::UNKNOWN_EXCEPTION),
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
