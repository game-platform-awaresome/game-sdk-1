<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ClientErrorService
{
    protected static $client_fields = [
        'os', 'sdk_version', 'api', 'error_no', 'error_msg', 'file', 'error_line', 'request_data',
    ];

    public function info(array $data)
    {
        $msg = $this->buildMessage($data, self::$client_fields);
        Log::channel('client')->error($msg);
    }

    protected function buildMessage($data, $fields)
    {
        $msg = "";
        foreach ($fields as $field) {
            $temp = $data[$field] ?? "";
            $msg .= $field . ": " . $temp . " | ";
        }

        return $msg;
    }
}
