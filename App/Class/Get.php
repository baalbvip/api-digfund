<?php

namespace Class\Get;

use Connection\DB;




class Get
{
    static function Test()
    {
        return DB::procedure("EXECUTE dbo.SP_WEB_REGISTRAR_CUENTA @pNombre = 'Pex', @pApellidos = 'axd', @pCorreo = 'baalb',@pDireccion = 'Caracas',@pTelefono = '+584125400',@pUsuario = 'baalbx@g',@pContrasenna = 'xd',@pTipo = 't', @pUsuario_Registro = 167545645, @pError = 'x',@nom_cuenta = 'pedrito',@fec_apertura = 1656989,@cod_ejecutivo = 0, @pNum_Portafolio = '232', @tipoOperacion = '1'");
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
                    }
                }
            }
        }


        return ['status' => $status, 'token' => $token];
    }
}
