<?php


namespace App\Exceptions;


use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Exception;
use ReflectionClass;

class RenderException extends Exception
{
    protected $header = [];

    /**
     * ExceptionHandle constructor.
     * @param int $code
     * @param string|null $message
     * @param array $header
     * @param \Throwable|null $previous
     */
    public function __construct(int $code, string $message = 'Server Error', array $header = [], \Throwable $previous = null)
    {
        $this->header = $header;
        // 若无自定义错误信息，则显示默认型错误
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return JsonResponse
     */
    public function render()
    {
        return new JsonResponse(
            ExceptionReport::convertExceptionToArray($this, $this->getCode()),
            200,
            method_exists($this, 'getHeaders') ? $this->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return $this->header;
    }
}