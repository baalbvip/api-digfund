<?php

namespace Class\Upload;

use Connection\DB;
use Exception;
use PDO;

require("./Functions/SendEmail.php");

error_reporting(E_ALL);

class Upload
{
    static function RegisterNewUser()
    {

        $key = $_POST['key'];
        $case = $_POST['case'];
        $email = $_POST['email'];
        $n_portafolio = $_POST['n_portafolio'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $company = $_POST['company'];
        if (CheckAppKey($key)) {
            switch ($case) {
                case 'all':
                    break;
                case 'dig':
                    $status = RegisterUserDigfund($first_name, $last_name, $email, $n_portafolio, $company);
                    break;
            }
        } else {
            $msg = "secrect key is invalid";
        }



        return ['status' => $status, 'msg' => $msg];
    }

    static function Order()
    {
        // esta funcion sera la encargada de subir el usuario y la orden
        // el usuario se subira en caso de que se detecte que no se ha creado

        $params = $_POST;

        NewLog(json_encode($_POST));

        if (!empty($_POST['billing_email'])) {

            if (!ExistsUser($params['billing_email'])) {
                $idUser = CreateUser($params);

                NewLog("No existe el usuario $params[billing_email]");

                // Si recibes un ID USER, significa que entonces si se registro el usuario entonces creale la orden con su ID asociada.
                CreateOrder($params['billing_email'], $params);

                NewLog("Se creo el usuario $params[billing_email]");
            } else {

                NewLog("Existe el usuario $params[billing_email]");


                if (!ExistsOrder($params['order_id'])) {
                    CreateOrder($params['billing_email'], $params);
                }
            }
        }

        print "ok";
    }


    static function OrderPrepare()
    {
        // Esta entrada se encargara de preparar la orden que tienes.
        $ind = $_POST['ind'];
        $amount = $_POST['amount'];

        NewLog(json_encode($_POST));

        if (isset($ind)) {
            OrderData($ind, "insert", ['amount' => $amount]);
            $status = true;

            NewLog(json_encode($_SESSION));
        } else {
            $msg = "Opps necesitas un indentificador.";
        }

        return ['status' => $status, 'msg' => $msg];
    }

    static function RenewContract()
    {

        $session = CheckSession();



        if ($session) {
            $infoUser = ExistsUser($session);

            $name = $_POST['name'];
            $dni = $_POST['dni'];
            $birthday = $_POST['birthday'];
            $birthzone = $_POST['birthzone'];
            $home = $_POST['home'];
            $work = $_POST['work'];
            $direction = $_POST['direction'];
            $email = $_POST['email'];
            $bankname = $_POST['bankname'];
            $directionbank = $_POST['directionbank'];
            $codeswift = $_POST['codeswift'];
            $codeaba = $_POST['codeaba'];
            $account = $_POST['account'];
            $id = $_POST['id'];
            $usa = $_POST['usa'];

            $bname = $_POST['bname'];
            $bdni = $_POST['bdni'];
            $bemail = $_POST['bemail'];
            $bpercent = $_POST['bpercent'];

            $bname2 = $_POST['bname2'];
            $bdni2 = $_POST['bdni2'];
            $bemail2 = $_POST['bemail2'];
            $bpercent2 = $_POST['bpercent2'];

            $bname3 = $_POST['bname3'];
            $bdni3 = $_POST['bdni3'];
            $bemail3 = $_POST['bemail3'];
            $bpercent3 = $_POST['bpercent3'];

            try {
                $fileCreate = CreateContractRenew($infoUser['Num_Portafolio'] . "-" . "reinvertion-$id-hash-" . md5(time()));

                DB::procedure("
                EXECUTE dbo.SP_WEB_REGISTRAR_CONTRATO 
                @pFechaAceptacion = '" . DateTime() . "', 
                @pNum_Portafolio = '" . $infoUser['Num_Portafolio'] . "',
                @pNombreCliente = '$name',
                @pIdentificacion = '$dni', 
                @pFechaNacimiento = '$birthday',
                @pLugarNacimiento = '$birthzone',
                @pUsa = '$id',
                @pCorreo = '$email',
                @pDireccionFisica = '$direction',
                @pDireccionResidencia = '$home',
                @pBanco = '$bankname',
                @pDireccionBanco = '$directionbank',
                @pCuentaBanco = '$account',   
                @pCodigoSwift = '$codeswift',
                @pCodigoAba = '$codeaba',
                @pInfo = 'xd', 
                @pError = '0',
                @pUsuario_Registro = '" . $infoUser['Correo'] . "'
                ");


                for ($i = 1; $i <= 3; $i++) {

                    $search = $i;
                    if ($i == 1) {
                        $search = "";
                    }



                    if ($_POST['bname' . $search]) {
                        $_POST['bdni' . $search];

                        DB::procedure("
                        EXECUTE dbo.SP_WEB_REGISTRAR_BENEFICIARIOS
                        @pNum_Portafolio = '" . $infoUser['Num_Portafolio'] . "',
                        @pNombreBen = '" . $_POST['bname' . $search] . "',
                        @pIdentificacionBen = '" . $_POST['bdni' . $search] . "',
                        @pCorreoBen = '" . $_POST['bemail' . $search] . "',
                        @pPorcentajeBen = '" . $_POST['bpercent' . $search] . "',
                        @pUsuario_Registro = '" . $infoUser['Correo'] . "',
                        @pError = 'false'
                        ");
                    }
                }
            } catch (Exception $err) {
                print_r($err);
            }



            $status = true;

            if ($status) {
                if ($fileCreate['status']) {
                    $source = file_get_contents("./Files/notificacion.html");
                    $newText = str_replace('$id', $id, $source);
                    $newText = str_replace('$link', "https://api.dig-fund.com/api/get/filereinvertion?id=$id&token=" . $_POST['token'], $newText);
                    sendEmail("Has hecho una reinversion", $newText, $email);
                }
            }
        }


        return ['status' => $status];
    }
}
