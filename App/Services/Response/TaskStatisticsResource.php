<?php

namespace App\Services\Response;

class TaskStatisticsResource
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function map()
    {
        return [
            'total' => $this->data["total"]
        ];
    }
}
