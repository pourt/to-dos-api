<?php

namespace App\System;

class Http
{
    public static function requestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function requestUri()
    {
        $uri = $_SERVER['REQUEST_URI'];

        return preg_match('/\?/', $uri) ? explode("?", $uri)[0] . "?" : $uri;
    }

    public static function baseUrl()
    {
        //PHP GET base directory
        $base_dir = __DIR__;

        //PHP GET Your server protocol
        $protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';

        //PHP GET domain name
        $domain = $_SERVER['SERVER_NAME'];

        //PHP GET base url
        $base_url = preg_replace("!^$" . self::getDocRoot() . "!", '', $base_dir);

        //PHP GET server port
        $port = $_SERVER['SERVER_PORT'];
        $disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";

        //PHP GET put em all together to get the complete base URL
        $url = "${protocol}://${domain}${disp_port}";

        return $url; // = https://www.pakainfo.com/path/directory
    }

    public static function getDocRoot()
    {
        return preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);
    }

    public static function getCurrentUrl()
    {

        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}
