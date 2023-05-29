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


// Procesar la salida para obtener solo los nombres de archivo
$files = [];
$lines = explode("\n", $output);
foreach ($lines as $line) {
    if (!empty($line)) {
        // Eliminar la fecha y el tamaño usando awk y sed
        $file = trim(shell_exec("echo '$line' | awk '{$1=$2=$3=\"\"; print}' | sed 's/^[[:space:]]*//'"));
        
        $files[] = $file;
    }
}

// Imprimir la lista de nombres de archivo
foreach ($files as $file) {
    echo $file . "\n";
}