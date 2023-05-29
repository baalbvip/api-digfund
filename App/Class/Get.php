<?php

namespace Class\Get;

use Connection\DB;
use Exception;
use PDO;

class Get
{
    static function Test()
    {
        return DB::query("SELECT * FROM dbo.UsuariosTmp");
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

        if ($session) {
            $infoUser = ExistsUser($session);
            $response = DB::procedure("EXECUTE dbo.SP_WEB_TransaccionesDIG @Portafolio = " . $infoUser['Num_Portafolio'] . "");
            return $response;
        }
    }

    static function DetailOrder()
    {
        $code = urldecode($_POST['code']);

        $session = CheckSession();
        if ($session) {

            NewLog($session);
            NewLog($code);
            return DB::procedure("EXECUTE dbo.SP_POR_Detalle_Inversiones @serie = '$code' , @cod_cuenta = '$session'");
        }
    }


    static function StatusAccount()
    {

        $ftpServer = 'achieveprocessingcenter.com';
        $ftpUsername = 'integraciondig';
        $ftpPassword = '9ov%1y72DIG#';

        $remoteDirectory = 'https://achieveprocessingcenter.com/ACRepository/';

        $year = "2021";

        // Comando FTP para obtener la lista de archivos
        $command = "ftp -n $ftpServer <<END_SCRIPT
quote USER $ftpUsername
quote PASS $ftpPassword
ls -p
quit
END_SCRIPT";

        // Ejecutar el comando y capturar la salida
        $output = shell_exec($command);

        // Imprimir la salida
        $arr = explode("\n", $output);

        $months = ["01" => [], "02" => [], "03" => [], "04" => [], "05" => [], "06" => [], "07" => [], "08" => [], "09" => [], "10" => [], "11" => [], "12" => []];


        foreach ($arr as $file) {
            $str = "ED_";
            $pos = strpos($file, "ED_");

            if ($pos !== false) {
                $filename = substr($file, $pos + 3);  // Obtener la porción de la cadena después de "EC_"
                $filename = $str . $filename;
                $fileName = basename($filename);
                $archivoTipo = substr($fileName, 0, 3);
                $archivoMes = substr($fileName, 5, 2);
                $archivoAnio = substr($fileName, 7, 4);
                $archivoPortafolio = substr($fileName, 16, 5);

                if ($archivoAnio == $year) {
                    $urlArchivo = $remoteDirectory . $fileName;
                    $months[$archivoMes][] = ['url_download' => $urlArchivo];
                }
            }
        }

        return $months;
    }
}
