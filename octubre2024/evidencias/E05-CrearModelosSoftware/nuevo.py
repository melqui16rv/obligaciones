import xlwings as xw
import openpyxl as op
from datetime import datetime
import pandas as pd
import win32com.client


def procesoDF14(df_1, df_2, rutaConsolidado):
    try:
        df1 = pd.read_excel(df_1)
        df2 = pd.read_excel(df_2)
    except Exception as e:
        print(f"Error leyendo los archivos de Excel: {e}")
        return
    
    print(f"Columnas en {df_1}: {df1.columns}")
    print(f"Columnas en {df_2}: {df2.columns}")
    
    common_columns = df1.columns.intersection(df2.columns)
    
    if len(common_columns) == 0:
        print("No hay columnas comunes entre los archivos.")
        return
    
    df1_common = df1[common_columns]
    df2_common = df2[common_columns]

    df_consolidado = pd.concat([df1_common, df2_common], ignore_index=True)
    
    try:
        with pd.ExcelWriter(rutaConsolidado, engine='xlsxwriter') as writer:
            df_consolidado.to_excel(writer, index=False)
        print("Los archivos se han consolidado correctamente.")
    except Exception as e:
        print(f"Error al guardar el archivo Excel consolidado: {e}")

def procesoP04(file_path):
    wb = op.load_workbook(file_path)
    hoja = wb.active
    hoja.insert_cols(1)
    print(f"Se ha insertado una nueva columna en el archivo: {file_path}")

    hoja['A1'] = 'AÑO'
    ano_actual = datetime.now().year
    max_row = hoja.max_row
    while max_row > 1 and hoja[f'B{max_row}'].value is None:
        max_row -= 1
    for row in range(2, max_row + 1):
        hoja[f'A{row}'] = ano_actual
    print(f"Se ha añadido el año {ano_actual} en la primera columna del archivo: {file_path}")

    wb.save(file_path)
    print(f"Se han guardado los cambios en el archivo: {file_path}")

def ejecutar_macros(nombre_macros):
    excel = win32com.client.Dispatch("Excel.Application")
    excel.Visible = False

    libro = excel.Workbooks.Open(r"Z:\\00_PE04\\Proceso Automate\\excel\\macrosDF14-P04.xlsm")  # Cambia la ruta a tu archivo

    try:
        for macro in nombre_macros:
            if callable(macro):  # Verifica si es una función Python
                print(f"Ejecutando función: {macro.__name__}")
                macro()  # Ejecuta la función Python
            else:
                try:
                    excel.Application.Run(macro)  # Ejecuta la macro de Excel
                    print(f"Macro {macro} ejecutada con éxito.")
                except Exception as e:
                    print(f"Error al ejecutar {macro}: {e}")
    finally:
        libro.Save()  # Guarda los cambios en el libro
        libro.Close(SaveChanges=True)  # Cierra el libro de Excel
        excel.Quit()  # Cierra la aplicación de Excel
        print("Todos los procesos han finalizado. Excel se ha cerrado correctamente.")

# Rutas de los archivos
df_1 = 'Z:\\00_PE04\\Proceso Automate\\DF.xlsx'
df_2 = 'Z:\\00_PE04\\Proceso Automate\\DF2.xlsx'
rutaP04 = 'Z:\\00_PE04\\Proceso Automate\\P04.xlsx'
rutaConsolidado = 'Z:\\00_PE04\\Proceso Automate\\Reporte-DF14.xlsx'

# Lista de macros y funciones a ejecutar
nombre_macros = [
    'ImportarXMLComoExcel',
    'ImportarXMLComoExcel2',
    'ImportarXMLComoExcelP04',
    'eliminarFila',
    'EliminarHojasExceptoPunto',
    lambda: procesoDF14(df_1, df_2, rutaConsolidado),  # Pasar las funciones como lambdas
    lambda: procesoP04(rutaP04),
    'UnirDosLibros',
    'CopiarHojas',
]

# Llamar a la función para ejecutar macros
ejecutar_macros(nombre_macros)