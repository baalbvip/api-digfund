<?php

namespace Class\Upload;

use Connection\DB;
use PDO;

class Upload
{
    static function Order()
    {
        // esta funcion sera la encargada de subir el usuario y la orden
        // el usuario se subira en caso de que se detecte que no se ha creado

        $params = $_POST['cart'];
        $auth = $_POST['token'];
        $ind = $_POST['ind'];

        $getOrder = OrderData($ind, "get");
        $params['amount'] = $getOrder->amount;
        OrderData($ind, 'insert', $params);

        if (!ExistsUser($params['billing_email'])) {
            $idUser = CreateUser($params);

            if ($idUser) {
                // Si recibes un ID USER, significa que entonces si se registro el usuario entonces creale la orden con su ID asociada.
                CreateOrder($params['billing_email'], $params);
            }
            NewLog("Se creo el usuario $params[billing_email]");
        } else {
            CreateOrder($params['billing_email'], $params);
        }


        NewLog(json_encode(OrderData($ind, 'get')));
        NewLog(json_encode($_POST));
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
}
