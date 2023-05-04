<?php

use Connection\DB;


function ExistsUser($user)
{
    print $user;
    return DB::query("SELECT * FROM dbo.UsuariosTmp WHERE Usuario = '$user' or Correo = '$user'")[0];
}

function CreateUser($params)
{
    $email = $params['billing_email'];
    $first_name = $params['billing_first_name'];
    $last_name = $params['billing_last_name'];
    $phone = $params['billing_phone'];
    $num_port = LastNumPortafolio() + 1;

    try {
        $id = DB::insert(
            "INSERT INTO dbo.UsuariosTmp (Nombre,Apellidos,Correo,Usuario,contrasenna,Tipo,Ind_estado,Usuario_Registro,Fecha_Registro,Num_Portafolio) 
        VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$first_name, $last_name, $email, $email, "default", "E", "1", $email, date("Y-m-d"), $num_port]
        );

        DB::insert(
            "INSERT INTO dbo.CLI_CUENTAS 
            (cod_cuenta,cod_subcuenta,cod_ejecutivo,por_comision,ind_exento,ind_estado,can_dias_ley_psicotropicos,ind_forma_pago_dividendos,ind_empleado,ind_cobro_intereses,cod_tipo_instruccion,cod_tipo_cliente,ind_origen_fondo_deposito_inicial,nom_cuenta,fec_apertura) 
        VALUES 
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$num_port, 0, 0, 0, 'N', 'A', 0, 'T', 'N', 'S', 1, 1, 1, $first_name . " " . $last_name, date("Y-m-d h:i:s", time())]
        );
    } catch (Exception $err) {
        NewLog($err);
    }

    return $id;
}


function NewLog($text)
{
    $file = fopen("./Logs/log.txt", "a");
    fwrite($file, $text . "\n");
    fclose($file);
}


function CreateOrder($user, $params)
{

    $infoUser = ExistsUser($user);

    if ($infoUser) {
        // Significa que este usuario existe entonces vamos a crearle la orden que requiere
        $insert = DB::insert(
            "INSERT INTO dbo.PFI_SOLICITUD_ORDEN (cod_fondo,cod_cuenta,mon_efectivo,mon_cheques,ind_division,cod_forma_pago,cod_subcuenta,ind_pend_liquidar,num_solicitud,cod_safi,ind_estado,fec_solicitud) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            [1, $infoUser['Id_Usuario'], $params['amount'], 10, 0, 0, 0, 0, time(), 0, 0, date("Y-m-d")]
        );

        NewLog("Se inserto una nueva orden al usuario $infoUser[Correo]");
    }
}

function OrderData($ind, $action, $values = "")
{

    switch ($action) {
        case 'insert':
            $file = fopen("./Orders/$ind.json", "w+d");

            fwrite($file, json_encode($values, true));
            fclose($file);

            break;

        case 'get':
            $contents = json_decode(file_get_contents("./Orders/$ind.json"));
            return $contents;
            break;
    }
}

function LastNumPortafolio()
{
    $query = DB::query("SELECT TOP(1) * FROM dbo.UsuariosTmp ORDER BY Num_Portafolio DESC");
    $lastId = null;


    if ($query) {
        $lastId = $query[0]['Num_Portafolio'];
    }


    return $lastId;
}
