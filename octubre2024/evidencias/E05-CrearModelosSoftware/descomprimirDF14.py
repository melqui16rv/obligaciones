import zipfile
import os

def descomprimir_zip(ruta_zip, ruta_destino):
    if not os.path.exists(ruta_destino):
        os.makedirs(ruta_destino)

    with zipfile.ZipFile(ruta_zip, 'r') as archivo_zip:
        archivo_zip.extractall(ruta_destino)

    print(f"Archivos extraídos a: {ruta_destino}")

ruta_zip = 'Z:\\00_TRA\\Descargas\\_DF-14_.zip'  
ruta_destino = 'Z:\\00_PE04'



descomprimir_zip(ruta_zip, ruta_destino)