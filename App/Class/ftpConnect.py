from ftplib import FTP

ftpServer = 'achieveprocessingcenter.com'
ftpUsername = 'integraciondig'
ftpPassword = '9ov%1y72DIG#'

# Conectarse al servidor FTP
ftp = FTP(ftpServer)
ftp.login(ftpUsername, ftpPassword)

# Obtener la lista de archivos en el directorio actual
files = []
ftp.retrlines('NLST', files.append)

# Filtrar y procesar los archivos
filtered_files = []
for file in files:
    file = file.decode('iso-8859-1')
    if file.startswith('ED_'):
        filtered_files.append(file)

# Cerrar la conexión FTP


# Imprimir la lista de archivos en formato de texto normal
for file in filtered_files:
    print(file)

ftp.quit()