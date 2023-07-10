<?php
require($_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php");

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Connection\DB;

$APP_KEY = "APP_SECRET_Xxi6Jpl4UXzo0rFH2W9WPuNQKsruzDGa";

function RegisterUserDigfund($first_name, $last_name, $email, $n_portafolio, $company)
{

    $woocommerce = new Client(
        'https://staging2.dig-fund.com',
        'ck_07e03d20090f409670460cbc7f1619122a4e7c47',
        'cs_5c019fcafe60ef45f6e66cc235df524edcfa2b67',
        [
            'wp_api' => true,
            'version' => 'wc/v3',
            'query_string_auth' => true,
        ]
    );


    // Verificar la conexión con la API de WooCommerce
    try {
        $woocommerce->get('customers');
        // echo 'Conexión exitosa con la API de WooCommerce.' . PHP_EOL;
    } catch (HttpClientException $e) {
        echo 'Error de conexión con la API de WooCommerce: ' . $e->getMessage() . PHP_EOL;
        exit;
    }

    $userData = array(
        'email' => $email,
        'role' => 'customer',
        'first_name' => $first_name,
        'last_name' => $last_name,
        'billing' => array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $company,
            'address_1' => 'casa',
            /*'city' => $row[7],
            'state' => $row[8],
            'country' => $row[9],
            'phone' => $row[10],*/
            // Agregar más campos de facturación según corresponda
        ),
        'meta_data' => array(
            array(
                'key' => 'n_portafolio',
                'value' => $n_portafolio, // Ajusta el índice si el campo n_portafolio no está en la columna 11
            ),
            // Agregar más campos de metadatos según corresponda
        ),
    );
    try {
        $newCustomer = $woocommerce->post('customers', $userData);
        #NewLog("cliente creado correctamente a digfund.com $email");
        $status = true;
    } catch (Automattic\WooCommerce\HttpClient\HttpClientException $e) {
        if ($e->getCode() === 400) {
            $response = json_decode($e->getMessage());
            if (isset($response->message) && isset($response->data->status)) {
                if ($response->data->status === 'registration-error-email-exists') {
                    #NewLog("error al crear el cliente $email");
                } else {
                    #NewLog("error al crear el cliente $email");
                }
            } else {
                #NewLog("error al crear el cliente $email");
            }
        } else {
            #NewLog("error al crear el cliente $email");
        }
    }

    return $status;
}

function ExistsUser($user)
{


    if (is_numeric($user)) {
        return DB::query("SELECT * FROM dbo.UsuariosTmp WHERE Usuario = '$user' or Correo = '$user' or Num_Portafolio = '$user'")[0];
    } else {
        return DB::query("SELECT * FROM dbo.UsuariosTmp WHERE Usuario = '$user' or Correo = '$user'")[0];
    }
}


function CreateUser($params)
{
    $email = $params['billing_email'];
    $first_name = $params['billing_first_name'];
    $last_name = $params['billing_last_name'];
    $phone = $params['billing_phone'];
    $country = $params['billing_country'];
    $asesor = $params['asesor'];
    $num_port = LastNumPortafolio() + 1;

    try {

        /*
        $id = DB::insert(
            "INSERT INTO dbo.UsuariosTmp (Nombre,Apellidos,Correo,Usuario,contrasenna,Tipo,Ind_estado,Usuario_Registro,Fecha_Registro,Num_Portafolio) 
        VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$first_name, $last_name, $email, $email, "default", "E", "1", $email, date("Y-m-d"), $num_port]
        );*/

        $procedure = DB::procedure("EXECUTE dbo.SP_WEB_REGISTRAR_CUENTA_IN @pNombre = '$first_name', @pApellidos = '$last_name', @pCorreo = '$email',@pDireccion = '$country',@pTelefono = '$phone',@pUsuario = '$email',@pContrasenna = 'default',@pTipo = 'E', @pUsuario_Registro = '" . DateTime() . "', @pError = 'x',@nom_cuenta = '$first_name',@fec_apertura = '" . DateTime() . "',@cod_ejecutivo = '$asesor', @pNum_Portafolio = '$num_port', @tipoOperacion = '1'");
        $id = ExistsUser($email)['Id_Usuario'];
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

    NewLog("xd");

    if ($infoUser) {
        // Significa que este usuario existe entonces vamos a crearle la orden que requiere


        /*
        $insert = DB::procedure(
            "INSERT INTO dbo.PFI_SOLICITUD_ORDEN (cod_fondo,cod_cuenta,mon_efectivo,mon_cheques,ind_division,cod_forma_pago,cod_subcuenta,ind_pend_liquidar,num_solicitud,cod_safi,ind_estado,fec_solicitud) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            [1, $infoUser['Num_Portafolio'], $params['amount'], 10, 0, 0, 0, 0, time(), 0, 0, date("Y-m-d")]
        );*/

        NewLog(json_encode($infoUser));
        NewLog(json_encode($params));

        DB::insert("INSERT INTO dbo.WEB_ORDEN_WEB (id_order,id_page,fec_order) VALUES (?,?,?)", [$params['order_id'], 'dig', date("Y-m-d")]);


        DB::procedure("EXECUTE dbo.SP_WEB_REGISTRAR_ORDEN_SOLICITUD @cod_cuenta = '" . $infoUser['Num_Portafolio'] . "', @obs_solicitud = '" . $params['order_id'] . "', @fec_solicitud = '" . DateTime() . "', @mon_efectivo = '$params[amount]', @pError = '1'");


        NewLog("Se inserto una nueva orden al usuario $infoUser[Correo]");
    }
}

function ExistsOrder($order_id, $id_page = "dig")
{
    return count(DB::query("SELECT * FROM dbo.WEB_ORDEN_WEB WHERE id_order = ? AND id_page = ?", [$order_id, $id_page]));
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

function GenerateToken()
{
    return sha1(md5(time() * rand(0, 150) * rand(250, 500)));
}

function CheckAppKey($key)
{
    global $APP_KEY;

    // Esto verificara si es correcta la key que se le esta pasando
    return $APP_KEY == $key;
}

function CheckSession()
{
    $token = $_POST['token'];

    if (!empty($token)) {
        $result = DB::query("SELECT * FROM dbo.ksq_sessions WHERE hash = ?", [$token])[0];
        return (int) $result['user_id'];
    }
}


function DateTime()
{
    return date("Y-m-d h:i:s");
}
