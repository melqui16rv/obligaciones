import xlwings as xw
import openpyxl as op
from datetime import datetime
import pandas as pd

def ejecutarMacros(rutaMacro, nombreMacros):
    app = xw.App(visible=True)  
    wb = None
    try:
        wb = app.books.open(rutaMacro)
        for macrosDF in range(2):
            try:
                wb.macro(nombreMacros[macrosDF]).run()
                print(f"La macro '{nombreMacros[macrosDF]}' se ejecutó correctamente.")
            except Exception as e:
                print(f"Se produjo un error al ejecutar la macro '{nombreMacros[macrosDF]}':", e)
        procesoDF14(df_1,df_2,rutaConsolidado)
        for macros in range(2,6):
            try:
                wb.macro(nombreMacros[macros]).run()
                print(f"La macro '{nombreMacros[macros]}' se ejecutó correctamente.")
            except Exception as e:
                print(f"Se produjo un error al ejecutar la macro '{nombreMacros[macros]}':", e)
        procesoP04(rutaP04)
        for macros in range(6, len(nombreMacros)):
            try:
                wb.macro(nombreMacros[macros]).run()
                print(f"La macro '{nombreMacros[macros]}' se ejecutó correctamente.")
            except Exception as e:
                print(f"Se produjo un error al ejecutar la macro '{nombreMacros[macros]}':", e)
        #wb.save()
    except Exception as e:
        print(f"Se produjo un error al abrir el archivo: {e}")
    finally:
        if wb:
            wb.close()
        app.quit()

def procesoDF14(df_1, df_2, rutaConsolidado, n_filas_a_eliminar=3):
    df1 = df_1
    df2 = df_2
    
    df_consolidado = pd.concat([df1, df2], ignore_index=True)
    
    df_consolidado.to_excel(rutaConsolidado, index=False)
    print("Los archivos se han consolidado correctamente")

def procesoP04(file_path):
    # Insertar una nueva columna en la primera posición
    wb = op.load_workbook(file_path)
    hoja = wb.active
    hoja.insert_cols(1)
    print(f"Se ha insertado una nueva columna en el archivo: {file_path}")


    hoja['A1'] = 'AÑO'
    print(f"Se ha actualizado la celda A1 con 'AÑO' en el archivo: {file_path}")

    ano_actual = datetime.now().year
    max_row = hoja.max_row
    while max_row > 1 and hoja[f'B{max_row}'].value is None:
        max_row -= 1
    for row in range(2, max_row + 1):
        hoja[f'A{row}'] = ano_actual
    print(f"Se ha añadido el año {ano_actual} en la primera columna del archivo: {file_path}")

    # Guardar los cambios
    wb.save(file_path)
    print(f"Se han guardado los cambios en el archivo: {file_path}")

nombreMacros = ['ImportarXMLComoExcel', 'ImportarXMLComoExcel2', 'eliminarFila', 'ImportarXMLComoExcelP04', 'EliminarFilas3', 'eliminarFila2','UnirDosLibros', 'EliminarPrimeraHoja', 'CopiarHojas']

df_1 = 'C:\\Users\\jcpinerosz\\Documents\\00_PE04\\Proceso Automate\\DF.xlsx'
df_2 = 'C:\\Users\\jcpinerosz\\Documents\\00_PE04\\Proceso Automate\\DF2.xlsx'
ruta = 'C:\\Users\\jcpinerosz\\Documents\\00_PE04\\Proceso Automate\\excel\\macrosDF14-P04.xlsm'
rutaP04 = 'C:\\Users\\jcpinerosz\\Documents\\00_PE04\\Proceso Automate\\P04.xlsx'
rutaConsolidado = 'C:\\Users\\jcpinerosz\\Documents\\00_PE04\\Proceso Automate\\Reporte-DF14.xlsx'

ejecutarMacros(ruta, nombreMacros)
procesoDF14(df_1, df_2, rutaConsolidado)