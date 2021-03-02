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
     * @param array $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function identify(array $data)
    {
        // 地址
        $url = config('services.wlc.identity_url');
        // 原始报文体
        $body = ['ai' => '100000000000000001', 'name' => $data['id_name'], 'idNum' => $data['id_number']];
        // 加密报文体
        $body = ['data' => CryptTool::aes128gcm($body)];
        // 报文头
        $headers = ['appId' => config('services.wlc.app_id'), 'bizId' => $data['biz_id'], 'timestamps' => StringTool::microtime()];
        // 报文头生成签名
        $headers['sign'] = SignTool::generateWlcSign($headers, $body);
        try {
            // 客户端初始化
            $client = new Client();
            dump(json_encode($headers, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), json_encode($body, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $resp = $client->post($url, [
                'timeout' => 5.0,
                'headers' => $headers,
                'json' => $body
            ]);
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

    /**
     * POST请求 实名认证查询
     * application/json
     *
     * @param array $data
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function identifyQuery(array $data)
    {
        // 地址
        $url = config('services.wlc.identity_query_url');
        // 原始query
        $original_query = ['ai' => $data['open_id']];
        // 报文头
        $header = ['appId' => config('services.wlc.app_id'), 'bizId' => $data['biz_id'], 'timestamps' => StringTool::microtime()];
        // 报文头生成签名
        $header['sign'] = SignTool::generateWlcSign($header, $original_query);
        try {
            // 初始化客户端
            $client = new Client(['timeout' => 10]);
            $resp = $client->get($url, [
                'headers' => $header,
                'query' => $original_query
            ]);
            // 解析和打印日志
            $result = (string)$resp->getBody();
            Log::channel('third')->info('identify query response: ' . $result);
            // 处理返回结果
            $result = json_decode($result, true);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $result['data']['result'];
            } else {
                return false;
            }
        } catch (ClientException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('third')->error("identify query fail, client error: " . $body);
            return false;
        } catch (ServerException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('third')->error("identify query fail, third server error: " . $body);
            return false;
        } catch (\Exception $exception) {
            Log::channel('third')->error("identify query fail, system error: " . $exception->getMessage());
            return false;
        }
    }

}