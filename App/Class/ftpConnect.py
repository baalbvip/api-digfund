from ftplib import FTP
import json

ftpServer = 'achieveprocessingcenter.com'
ftpUsername = 'integraciondig'
ftpPassword = '9ov%1y72DIG#'

# Conectarse al servidor FTP
ftp = FTP(ftpServer)
ftp.login(ftpUsername, ftpPassword)

# Obtener la lista de archivos en el directorio actual
files = []
ftp.retrbinary('LIST', files.append)

# Decodificar la respuesta del servidor FTP utilizando 'iso-8859-1'
decoded_files = []
for file in files:
    decoded_file = file.decode('iso-8859-1')
    decoded_files.append(decoded_file)

# Convertir la lista de archivos a formato JSON
json_files = json.dumps(decoded_files)

# Imprimir la lista de archivos en formato JSON
print(json_files)

# Cerrar la conexión FTP
ftp.quit()
