<?php

namespace Class\Upload;

use Connection\DB;
use Exception;
use PDO;

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

            try {
                CreateContractRenew($infoUser['Num_Portafolio'] . "-" . "reinvertion-$id");

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
            } catch (Exception $err) {
                print_r($err);
            }



            $status = true;
        }


        return ['status' => $status];
    }
}
