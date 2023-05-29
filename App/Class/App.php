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


            // Permitir peticiones desde cualquier origen
            header("Access-Control-Allow-Origin: *");

            // Permitir métodos de solicitud específicos
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

            // Permitir encabezados específicos
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


            print json_encode(call_user_func(array($class, $functions[1])));
        }
    }
}
