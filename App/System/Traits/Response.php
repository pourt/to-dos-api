<?php

namespace App\System\Traits;

trait Response
{
    public static function success($data, $message = null, $code = 200)
    {
        $responseHeader = $_SERVER["SERVER_PROTOCOL"] . " " . $code . " " . ucwords($message);
        header($responseHeader, true, $code);

        echo json_encode([
            'status' => 'Success',
            'message' => $message,
            'data' => $data
        ], $code);
        exit;
    }

    public static function error($message = null, $code)
    {
        $code = !$code ? 412 : $code;
        $code = $code > 500 ? 412 : $code;
        $code = $code == "HY000" ? 500 : $code;
        $status = $code == "HY000" ? "Database Error" : "Error";

        $responseHeader = $_SERVER["SERVER_PROTOCOL"] . " " . $code . " " . ucfirst($message);
        header($responseHeader, true, $code);

        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => null
        ], $code);
        exit;
    }
}
