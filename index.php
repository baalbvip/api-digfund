<?php
//ED_31122022_079_00364_10_(Luz Maria Aranda).pdf
$tipo = 'ED_';
$mes = '12';
$anio = '2022';
$portafolio = '00364';

// Configuración de conexión FTP
$ftpServer = 'achieveprocessingcenter.com';
$ftpUsername = 'integracion';
$ftpPassword = 'Yky$4m485D1ms4#';
$remoteDirectory = 'https://achieveprocessingcenter.com/ACRepository/';

// Establecer conexión FTP
$conn = ftp_connect($ftpServer);
if (!$conn) {
    die("No se pudo conectar al servidor FTP");
}

// Iniciar sesión FTP
if (!ftp_login($conn, $ftpUsername, $ftpPassword)) {
    die("Error de inicio de sesión FTP");
}


// Obtener lista de archivos en el directorio remoto
$fileList = ftp_nlist($conn, ".");
if (!$fileList) {
    die("No se pudo obtener la lista de archivos");
}

// Filtrar y mostrar archivos como hipervínculos
foreach ($fileList as $file) {
    $fileName = basename($file);
    $archivoTipo = substr($fileName, 0, 3);
    $archivoMes = substr($fileName, 5, 2);
    $archivoAnio = substr($fileName, 7, 4);
    $archivoPortafolio = substr($fileName, 16, 5);
    
    // Filtrar archivos basado en las variables
    if ($archivoTipo === $tipo &&
        $archivoMes === $mes &&
        $archivoAnio === $anio &&
        $archivoPortafolio === $portafolio) {
        
        // Generar URL del archivo FTP
        $urlArchivo = $remoteDirectory . $fileName;        

        // Mostrar el nombre del archivo como hipervínculo
        echo '<a href="' . $urlArchivo . '" target="_blank">' . $fileName . '</a><br>';
    }
}

// Cerrar conexión FTP
ftp_close($conn);
?>