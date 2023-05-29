<?php


$ftpServer = 'achieveprocessingcenter.com';
$ftpUsername = 'integraciondig';
$ftpPassword = '9ov%1y72DIG#';
$remoteDirectory = 'https://achieveprocessingcenter.com/ACRepository/';


$conn = ftp_connect($ftpServer);


if (!$conn) {
    die("Error al conectarse con el servidor");
}


if (!ftp_login($conn, $ftpUsername, $ftpPassword)) {
    die("Error de inicio de sesión FTP");
} else {
    print "ok Todo bien";
}
