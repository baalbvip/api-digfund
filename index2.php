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
    $str = "";
    $pos = strpos($file, "ED_") .  $str = "ED_";  

  
    $pos == 0 ?  $pos = strpos($file, "ED_") . $str = "ED_" : '';

    if ($pos !== false) {
        $filename = substr($file, $pos + 3);  // Obtener la porción de la cadena después de "EC_"
        echo $str . $filename . "\n";
    }
}
