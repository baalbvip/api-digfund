<?php

namespace Class\Get;

use Connection\DB;
use PDO;

class Get
{
    static function Test()
    {
        $id = CheckSession();

        if ($id) {
            return ExistsUser($id);
        } else {
            return "No has iniciado session";
        }
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

                    $insert = DB::insert("INSERT INTO dbo.ksq_sessions (ip,user_id,hash,time_add,time_expire) 
                    VALUES 
                    (?,?,?,?,?)", [$_SERVER['REMOTE_ADDR'], $infoUser['Num_Portafolio'], $token, time(), strtotime("+2 Days")]);

                    if ($insert) {
                        $status = true;
                    } else {
                        $token = null;
                    }
                }
            }
        }


        return ['status' => $status, 'token' => $token];
    }


    static function Consolidated()
    {
        $session = CheckSession();

        if ($session = true) {

            $response = DB::procedure("EXECUTE SP_WEB_Transacciones");

            print_r($response);
        }
    }
}
