import os
import shutil

def eliminarArchivo(ruta_archivo):
    try:
        if os.path.exists(ruta_archivo):
            shutil.move(ruta_archivo, os.path.join(os.environ.get('TEMP', '/tmp'), os.path.basename(ruta_archivo)))
            print(f"El archivo {ruta_archivo} ha sido movido a la papelera de reciclaje.")
            return True
        else:
            print(f"El archivo {ruta_archivo} no existe.")
            return False
    except Exception as e:
        print(f"Ocurrió un error al intentar mover el archivo: {e}")
        return False
def depuracion():
    archivos=[archivo3, archivo4, archivo5, archivo9]
    for archivo in archivos:
        eliminarArchivo(archivo)

archivo3="Z:\\00_PE04\\DF-14_1.xml"
archivo4="Z:\\00_PE04\\DF-14_2.xml"
archivo5="Z:\\00_PE04\\PE-04_1.xml"
archivo9="Z:\\00_PE04\\Proceso Automate\\Consolidado_DF14_P04.xlsx"
archivo9="Z:\\00_PE04\\Proceso Automate\\Reporte-DF14.xlsx"

depuracion()

