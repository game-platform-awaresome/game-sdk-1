<?php

namespace App\Tools;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

class HttpTool
{
    /**
     * POST请求 游戏回调
     * application/json
     *
     * @param string $url
     * @param array $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function notifyGame(string $url, array $data)
    {
        try {
            Log::channel('cp_notify')->info('send game url: ' . $url);
            Log::channel('cp_notify')->info('send game param: ' . var_export($data, true));

            $client = new Client(['timeout' => 10]);
            $resp = $client->post($url, [
                'form_params' => $data
            ]);

            $result = (string)$resp->getBody();
            Log::channel('cp_notify')->info('game response: ' . $result);
            Log::channel('pay')->info('game response: ' . $result);

            return $result;
        } catch (ClientException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('cp_notify')->error("notify fail, client error: " . $body);
            return false;
        } catch (ServerException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('cp_notify')->error("notify fail, game server error: " . $body);
            return false;
        } catch (\Exception $exception) {
            Log::channel('cp_notify')->error("notify fail, system error: " . $exception->getMessage());
            return false;
        }
    }

    /**
     * POST请求 实名认证接口
     * application/json
     *
     * @param string $url
     * @param array $headers
     * @param array $body
     * @param string $method
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function identify(string $url, array $headers, array $body, string $method = 'post')
    {
        try {
            // 客户端初始化
            $client = new Client(['timeout' => 5.0]);
            // headers补充Content-Type
            $headers['Content-Type'] = 'application/json';
            // 根据get或post请求选择提交方式
            switch ($method) {
                case 'get':
                    $resp = $client->get($url, [
                        'headers' => $headers,
                        'query' => $body
                    ]);
                    break;
                default:
                    $resp = $client->request('post', $url, [
                        'headers' => $headers,
                        'body' => json_encode($body, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
                    ]);
            }

            // 解析和打印日志
            $result = (string)$resp->getBody();

            Log::channel('third')->info('identify response: ' . $result);
            // 处理返回结果
            $result = json_decode($result, true);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $result['data']['result'];
            } else {
                return false;
            }
        } catch (ClientException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('third')->error("identify fail, client error: " . $body);
            return false;
        } catch (ServerException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('third')->error("identify fail, third server error: " . $body);
            return false;
        } catch (\Exception $exception) {
            Log::channel('third')->error("identify fail, system error: " . $exception->getMessage());
            return false;
        }
    }

}