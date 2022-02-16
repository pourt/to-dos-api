<?php

namespace App\System;

class Request
{
    public $request;

    protected $url;

    public function __construct()
    {
        mb_parse_str(file_get_contents('php://input'), $rawData);

        $this->request = (object) $rawData;

        switch (Http::requestMethod()) {
            case 'GET':
                $this->request = $this->get();
                break;
        }
    }

    public function toArray()
    {
        return (array) $this->request;
    }

    public function get($key = '')
    {
        $url_components = parse_url(Http::getCurrentUrl());

        if (isset($url_components['query']) && sizeof((array) $url_components['query'])) {

            parse_str($url_components['query'], $params);

            return $key ? $params[$key] : $params;
        }

        return [];
    }

    public function input($key = '')
    {
        return isset($this->request->$key) ? $this->request->$key : NULL;
    }

    public function mergeGet()
    {
        return (object) array_merge($this->toArray(), $this->get());
    }
}
