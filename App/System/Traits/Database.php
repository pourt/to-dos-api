<?php

namespace App\System\Traits;

use App\System\Database\SQLite;

/**
 * 
 */
trait Database
{
    public static function connect()
    {
        return new SQLite();
    }
}
