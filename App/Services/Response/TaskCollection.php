<?php

namespace App\Services\Response;

class TaskCollection
{
    public function __construct($data)
    {
        $this->tasks = $data;
    }

    public function collect()
    {
        return array_map(
            function ($data) {
                return (new TaskResource($data))->map();
            },
            $this->tasks
        );
    }
}
