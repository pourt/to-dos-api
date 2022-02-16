<?php

namespace App\System;

use App\Config\Route;
use App\System\Database\SQLite;
use App\System\Traits\Response;

class Controller
{
    use Response;

    public $db;

    public $request;

    public $route;

    public function __construct()
    {
        header('Content-type', 'application/json');

        $this->cors();

        $this->db = new SQLite();
    }

    public function remap()
    {
        $this->route = $this->getRoute();

        if ($this->route == NULL) {
            throw new \Exception("Route does not exists", 404);
        }

        $controllerClass = $this->route[0];
        if (!class_exists($controllerClass)) {
            throw new \Exception("Class does not exists", 404);
        }

        $controllerMethod = $this->route[1];
        $controllerDriver = new $controllerClass;

        if (!method_exists($controllerDriver, $controllerMethod)) {
            throw new \Exception("Method does not exists", 404);
        }

        $request = new Request();

        return $controllerDriver->$controllerMethod($request);
    }


    private function getRoute()
    {
        $routes = Route::routes;

        $uri = Http::requestUri();

        $method = Http::requestMethod();


        $routing = isset($routes[$method]) && !is_null($routes[$method]) ? $routes[$method] : [];

        foreach ($routing as $key => $value) {

            $parseRegExp = addcslashes($this->parseRegExp($key), "/:?");

            $re = "/^{$parseRegExp}*$/";

            if (preg_match($re, $uri)) {
                return $value;
            }
        }
    }

    private function parseRegExp($key)
    {
        if (preg_match('/:num/', $key)) {
            return str_replace(':num', '\d', $key);
        }

        return $key;
    }

    /**
     *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
     *  origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.  For the nitty-gritty low-down, read:
     *
     *  - https://developer.mozilla.org/en/HTTP_access_control
     *  - https://fetch.spec.whatwg.org/#http-cors-protocol
     *
     */
    function cors()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }
}
