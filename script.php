<?php

$host = "radarsystems.net";
$user = "radar";
$password = "R4d4r++2021**..";
$database = "radarprueba";
$port = 5432;



$conn = new PDO("pgsql:host=$host;port=$port;dbname=$database", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$sql = $conn->prepare("SELECT * FROM ex_admin LIMIT 100");
$sql->execute();

$results = $sql->fetchAll(PDO::FETCH_ASSOC);


$admins_contacts = [];

$admins_contacts = array_map(function ($result) {
    return [
        'id_admin' => $result['id_admin'],
        'usuario' => $result['usuario']
    ];
}, $results);



for ($i = 0; $i < count($admins_contacts); $i++) {


    $results_contacts = [];

    try {

        $contacts = $conn->prepare("SELECT * FROM ex_tbl_" . $admins_contacts[$i]['usuario']);
        $contacts->execute();
        $results_contacts = $contacts->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $err) {
        print "err";
    }

    if (count($results_contacts) >= 1) {

        // HAY QUE HACER EL INSERT Y LUEGO LA ELIMINACION DE TABLA
        print "ok";
    }
}
