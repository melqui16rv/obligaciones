import zipfile
import os

def descomprimir_zip(ruta_zip, ruta_destino):
    if not os.path.exists(ruta_destino):
        os.makedirs(ruta_destino)

    with zipfile.ZipFile(ruta_zip, 'r') as archivo_zip:
        archivo_zip.extractall(ruta_destino)

    print(f"Archivos extraídos a: {ruta_destino}")

ruta_zipP04 = 'Z:/00_TRA/Descargas/_PE-04_.zip'  
ruta_destinoP04 = 'Z:/00_PE04'



descomprimir_zip(ruta_zipP04, ruta_destinoP04)


