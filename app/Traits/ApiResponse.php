<?php


namespace App\Traits;

use App\Exceptions\Code;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ApiResponse
{
    /**
     * @param int $status
     * @param array|null $data
     * @param string $message
     * @return JsonResponse
     */
    public function respJson(array $data = null, int $status = Code::SUCCESS, string $message = 'success')
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], FoundationResponse::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function respWechat()
    {
        return response(
            arrayToXml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']),
            FoundationResponse::HTTP_OK
        );
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function respAlipay()
    {
        return response(
            'success',
            FoundationResponse::HTTP_OK
        );
    }
}
