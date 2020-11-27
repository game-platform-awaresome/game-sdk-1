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
            Log::channel('cp')->info('game notify url: ' . $url);
            Log::channel('cp')->info('game notify param: ' . json_encode($data));

            $client = new Client(['timeout' => 10]);
            $resp = $client->post($url, [
                'json' => $data
            ]);

            $result = (string)$resp->getBody();
            Log::channel('cp')->info('game notify result: ' . $result);

            return $result;
        } catch (ClientException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('cp')->error("notify fail, client error: " . $body);
            return false;
        } catch (ServerException $exception) {
            $body = (string)$exception->getResponse()->getBody();
            Log::channel('cp')->error("notify fail, game server error: " . $body);
            return false;
        } catch (\Exception $exception) {
            Log::channel('cp')->error("notify fail, system error: " . $exception->getMessage());
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
            Log::channel('third')->info('identifyTwoFactor param: ' . json_encode($sendData));

            $client = new Client(['timeout' => 10]);
            $resp = $client->post($url, [
                'form_params' => $sendData
            ]);

            $result = (string)$resp->getBody();
            Log::channel('third')->info('identify result: ' . $result);

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