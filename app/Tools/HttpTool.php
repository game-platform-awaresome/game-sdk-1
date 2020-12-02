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
     * POST请求 二要素检查请求
     * application/json
     *
     * @param string $url
     * @param array $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function identifyTwoFactor(string $url, array $data)
    {
        try {
            $sendData['body'] = "idcard={$data['id_number']}&name={$data['id_name']}";
            Log::channel('third')->info('send identify param: ' . var_export($data, true));

            $client = new Client(['timeout' => 10]);
            $resp = $client->post($url, [
                'form_params' => $sendData
            ]);

            $result = (string)$resp->getBody();
            Log::channel('third')->info('identify response: ' . $result);

            $res = json_decode($result, true);
            if (isset($res["result"]["result"]["res"]) && $res["result"]["result"]["res"] == 1) {
                return true;
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