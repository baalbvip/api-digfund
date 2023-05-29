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
    print "Conexión FTP exitosa\n";
}

// Obtener lista de archivos en el directorio remoto
$fileList = ftp_nlist($conn, "/");

if ($fileList === false) {
    die("Error al obtener la lista de archivos");
}

// Imprimir la lista de archivos
foreach ($fileList as $file) {
    echo $file . "\n";
}

// Cerrar la conexión FTP
ftp_close($conn);
?>
