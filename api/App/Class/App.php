<?php

namespace Class\App;

use Class\Get\Get;
use Class\Upload\Upload;


class App
{
    static function Route($route, $functions)
    {
        if ($_SERVER['REQUEST_URI'] == $route) {

            $class = new $functions[0]();

            header("Content-type: application/json");

            print json_encode(call_user_func(array($class, $functions[1])));
        }
    }
}
