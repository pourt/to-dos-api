<?php

namespace App\Config;

use App\Controller\Migrate;
use App\Controller\Todo;

class Route
{
    const routes = [
        'POST' => [
            '/task' => [Todo::class, 'create']
        ],
        'GET' => [
            '/task' => [Todo::class, 'get'],
            '/task?' => [Todo::class, 'get'],
            '/task/show/?' => [Todo::class, 'show'],
            '/task/statistics?' => [Todo::class, 'statistics']
        ],
        'PUT' => [
            '/task/?' => [Todo::class, 'update'],
            '/task' => [Todo::class, 'update'],
            '/task/priority/?' => [Todo::class, 'setPriority'],
            '/task/complete/?' => [Todo::class, 'setCompleted'],
        ],
        'DELETE' => [
            '/task?' => [Todo::class, 'destroy']
        ],
    ];
}
