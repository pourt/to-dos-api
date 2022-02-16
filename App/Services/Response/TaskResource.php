<?php

namespace App\Services\Response;

class TaskResource
{
    private $priorityLists = [
        1 => "high",
        2 => "moderate",
        3 => "low"
    ];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function map()
    {
        return [
            'id' => $this->data["id"],
            'name' =>  $this->data["name"],
            'priority' => $this->priorityLists[$this->data["priority"]],
            'status' => $this->data["status"],
        ];
    }
}
