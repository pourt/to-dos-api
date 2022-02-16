<?php

namespace App\Services\Response;

class TaskStatisticsCollection
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collect()
    {
        return array_map(
            function ($data) {
                return (new TaskStatisticsResource($data))->map();
            },
            $this->data
        );
    }
}
