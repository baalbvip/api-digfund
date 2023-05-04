<?php

namespace Class\Get;

use Connection\DB;




class Get
{
    static function Test()
    {
        return  ExistsUser("jotitowelcome@gmail.com");
    }

    static function MyInfo()
    {
        $session = CheckSession();

        if ($session) {
            $infoUser = ExistsUser($session);
            return $infoUser;
        }
    }

    static function Token()
    {

        // El servidor de dig se encargara de enviarle la peticion a esta api
        // Lo que sucedera es que la api detectara que usuario eres mediante los parametros que les pases, no puede hacerle una consulta un usuario comun por que sera bloqueado.
        // entonces buscaremos si existe el usuario, en caso de que este todo bien le creamos una session del lado de arch con el manejo de la base de datos

        $appKey = $_POST['app_key'];
        $email = $_POST['email'];

        if (!empty($appKey) && !empty($email)) {
            if (CheckAppKey($appKey)) {
                $infoUser = ExistsUser($email);
                if ($infoUser) {
                    $token = GenerateToken();

                  
                }
            }
        }


        return ['status' => $status, 'token' => $token];
    }
}
