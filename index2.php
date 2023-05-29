<?php
$ftpServer = 'achieveprocessingcenter.com';
$ftpUsername = 'integraciondig';
$ftpPassword = '9ov%1y72DIG#';

$remoteDirectory = 'https://achieveprocessingcenter.com/ACRepository/';

// Comando FTP para obtener la lista de archivos
$command = "ftp -n $ftpServer <<END_SCRIPT
quote USER $ftpUsername
quote PASS $ftpPassword
ls -p
quit
END_SCRIPT";

// Ejecutar el comando y capturar la salida
$output = shell_exec($command);

// Imprimir la salida
$arr = explode("\n", $output);


foreach ($arr as $file) {
    $file = substr($file, 20);
    $file = explode(" ", $file);
    $file = $file[14];

    $fileName = basename($file);
    $archivoTipo = substr($fileName, 0, 3);
    $archivoMes = substr($fileName, 5, 2);
    $archivoAnio = substr($fileName, 7, 4);
    $archivoPortafolio = substr($fileName, 16, 5);

    // Filtrar archivos basado en las variables

    // Generar URL del archivo FTP
    $urlArchivo = $remoteDirectory . $fileName;

    // Mostrar el nombre del archivo como hipervínculo
    echo '<a href="' . $urlArchivo . '" target="_blank">' . $fileName . '</a><br>';
}
