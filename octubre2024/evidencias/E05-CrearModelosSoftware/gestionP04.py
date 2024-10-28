import xlwings as xw
import os

def ejecutarMacros(rutaMacro, nombreMacros):
    app = xw.App(visible=True)
    wb = None
    try:
        wb = app.books.open(rutaMacro)
        for macro in nombreMacros:
            try:
                wb.macro(macro).run()
                print(f"La macro '{macro}' se ejecutó correctamente.")
            except Exception as e:
                print(f"Se produjo un error al ejecutar la macro '{macro}':", e)
    finally:
        if wb:
            wb.close()
        app.quit()

nombreMacros = ['CrearPE04Final', 'Hoja_Articulacion', 'Hoja_Complementaria','arreglarP04']
ruta = 'Z:\\00_PE04\\Gestión PE04 - copia.xlsm'

ejecutarMacros(ruta, nombreMacros)
