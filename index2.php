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
    $filename = explode("ED_",$file);

    print_r($filename);
    
}
