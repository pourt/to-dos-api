<?php

include "vendor/autoload.php";

use App\System\Controller;
use App\System\Traits\Response;

try {
    (new Controller)->remap();
} catch (\Exception $e) {
    Response::error($e->getMessage(), $e->getCode());
}
