from ftplib import FTP

ftpServer = 'achieveprocessingcenter.com'
ftpUsername = 'integraciondig'
ftpPassword = '9ov%1y72DIG#'

# Conectarse al servidor FTP
ftp = FTP(ftpServer)
ftp.login(ftpUsername, ftpPassword)

# Obtener la lista de archivos en el directorio actual
files = []
ftp.retrbinary('LIST', files.append)

# Imprimir la lista de archivos
for file in files:
    print(file.decode('iso-8859-1'))

# Cerrar la conexión FTP
ftp.quit()
