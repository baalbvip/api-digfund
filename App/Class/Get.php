<?php

namespace Class\Get;

use Connection\DB;
use Exception;
use PDO;



class Get
{

    static function FileReinvertion()
    {
        $token = $_GET['token'];
        $filename = $_GET['filename'];

        if ($token) {

            $_POST['token'] = $token;

            $session = CheckSession();

            if ($session) {
                $infoUser = ExistsUser($session);
                $filename = "./Files/Contract/" . $filename . ".html";
                $file = file_get_contents($filename);

                header("Content-Type: application/octet-stream");
                header('Content-Disposition: attachment; filename="downloaded.html"');
                header("Content-Length: " . filesize($filename));

                print $file;
            }
        }

        return "Xd";
    }

    static function InvertionExpireds()
    {
        $token = $_POST['token'];

        $session = CheckSession($token);
        $result = [];

        if ($session) {

            $infoUser = ExistsUser($session);
            $result = DB::procedure("EXECUTE dbo.SP_WEB_Vencimientos_DIG @Cuenta = '" . $infoUser['Num_Portafolio'] . "'");
        }

        return $result;
    }

    static function Test()
    {


        $months = ["01" => [], "02" => [], "03" => [], "04" => [], "05" => [], "06" => [], "07" => [], "08" => [], "09" => [], "10" => [], "11" => [], "12" => []];


        return $months;
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
        $user = $_POST['user'];

        NewLog("entraste todo bien");

        if (!empty($appKey) && !empty($email)) {
            if (CheckAppKey($appKey)) {
                $infoUser = ExistsUser($email, $user);
                if ($infoUser) {
                    $token = GenerateToken();

                    NewLog("Se creo la session con el usuario con el email $email y el usuario $user");

                    $insert = DB::insert("INSERT INTO dbo.ksq_sessions (ip,user_id,hash,time_add,time_expire) 
                    VALUES 
                    (?,?,?,?,?)", [$_SERVER['REMOTE_ADDR'], $infoUser['Num_Portafolio'], $token, time(), strtotime("+2 Days")]);

                    if ($insert) {
                        setcookie("token", $token, strtotime("+20 days"), "/");
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

        NewLog($session);

        if ($session) {
            $infoUser = ExistsUser($session);

            NewLog(json_encode($infoUser));
            $response = DB::procedure("EXECUTE dbo.SP_WEB_TransaccionesDIG @Portafolio = " . $infoUser['Num_Portafolio'] . "");
            return $response;
        }
    }

    static function DetailOrder()
    {
        $code = urldecode($_POST['code']);

        $session = CheckSession();

        if ($session) {
            return DB::procedure("EXECUTE dbo.SP_POR_Detalle_Inversiones @serie = '$code' , @cod_cuenta = '$session'");
        }
    }


    static function StatusAccount()
    {
        $months = ["01" => [], "02" => [], "03" => [], "04" => [], "05" => [], "06" => [], "07" => [], "08" => [], "09" => [], "10" => [], "11" => [], "12" => []];

        try {




            // Ejecutar el comando y capturar la salida
            $output = shell_exec("python3 App/Class/ftpConnect.py");
            $arr = explode("\n", $output);
            $remoteDirectory = 'https://achieveprocessingcenter.com/ACRepository/';
            $year = empty($_GET['year']) == true ? $year = date("Y") : $_GET['year'];

            $session = CheckSession();

            if ($session) {
                $infoUser = ExistsUser($session);

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
                        $paddedNumPortafolio = str_pad($infoUser['Num_Portafolio'], 5, '0', STR_PAD_LEFT);


                        if ($archivoAnio == $year && $archivoPortafolio == $paddedNumPortafolio) {
                            $urlArchivo = $remoteDirectory . $fileName;
                            $months[$archivoMes][] = ['url_download' => $urlArchivo];
                        }
                    }
                }
            } else {
                print "no session";
            }

            // Comando FTP para obtener la lista de archivos

        } catch (Exception $e) {
            print_r($e);
        }


        return $months;
    }
}
