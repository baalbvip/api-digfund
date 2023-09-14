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

function ExistsContract()
{
}

function ExistsUser($email, $user = null)
{

    if ($user == null) {
        $user = "xoo32p232k3o23o3232o";
    }

    if (is_numeric($email)) {
        $response = DB::query("SELECT * FROM dbo.UsuariosTmp WHERE Usuario = '$user' or Correo = '$email' or Num_Portafolio = '$email'")[0];
    } else {

        $response = DB::query("SELECT * FROM dbo.UsuariosTmp WHERE Usuario = '$user' or Correo = '$email'")[0];
    }


    if ($response) {
        NewLog("Se consigui el usuario " . json_encode($response));
        return $response;
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

        $procedure = DB::procedure("EXECUTE dbo.SP_WEB_REGISTRAR_CUENTA_IN @pNombre = '$first_name', @pApellidos = '$last_name', @pCorreo = '$email',@pDireccion = '$country',@pTelefono = '$phone',@pUsuario = '$email',@pContrasenna = 'default',@pTipo = 'E', @pUsuario_Registro = 'WEB-DIG', @pError = 'x',@nom_cuenta = '$first_name',@fec_apertura = '" . DateTime() . "',@cod_ejecutivo = '$asesor', @pNum_Portafolio = '$num_port', @tipoOperacion = '1'");
        $id = ExistsUser($email)['Id_Usuario'];

        NewLog("Usuario creado con exito $email");
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

function CreateContractRenew($namefile)
{

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
    $phone = $_POST['phone'];
    $usa = $_POST['usa'];
    $id = $_POST['id'];


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




    $file = file_get_contents("./Files/BodyContractRenew.html");
    $file = str_replace('$name', $name, $file);
    $file = str_replace('$dni', $dni, $file);
    $file = str_replace('$birthday', $birthday, $file);
    $file = str_replace('$birthzone', $birthzone, $file);
    $file = str_replace('$home', $home, $file);
    $file = str_replace('$work', $work, $file);
    $file = str_replace('$direction', $direction, $file);
    // $file = str_replace('$phone', $phone, $file);
    $file = str_replace('$email', $email, $file);
    //$file = str_replace('$initial', $initial, $file);
    $file = str_replace('$bank', $bankname, $file);
    $file = str_replace('$directionbank', $directionbank, $file);
    $file = str_replace('$codeswift', $codeswift, $file);
    $file = str_replace('$codeaba', $codeaba, $file);
    $file = str_replace('$account', $account, $file);
    $file = str_replace('$phone', $phone, $file);



    // BENEFICIARIOS

    $file = str_replace('$bname', $bname, $file);
    $file = str_replace('$bemail', $bemail, $file);
    $file = str_replace('$bdni', $bdni, $file);
    $file = str_replace('$bpercent', $bpercent, $file);

    $file = str_replace('$bname2', $bname2, $file);
    $file = str_replace('$bemail2', $bemail2, $file);
    $file = str_replace('$bdni2', $bdni2, $file);
    $file = str_replace('$bpercent2', $bpercent2, $file);

    $file = str_replace('$bname3', $bname3, $file);
    $file = str_replace('$bemail3', $bemail3, $file);
    $file = str_replace('$bdni3', $bdni3, $file);
    $file = str_replace('$bpercent3', $bpercent3, $file);

    if ($usa == "true") {
        $file = str_replace('$yesusa', "x", $file);
        $file = str_replace('$nousa', "", $file);
    } else {
        $file = str_replace('$yesusa', "", $file);
        $file = str_replace('$nousa', "x", $file);
    }

    try {
        $create = fopen("./Files/Contract/$namefile.html", "w");
        fwrite($create, $file);
        fclose($create);
        $response = ['status' => true, "route" => "./Files/Contract/$namefile.html"];
    } catch (Exception $err) {
        print_r($err);
        $response = ['status' => false];
    }

    return $response;
}
