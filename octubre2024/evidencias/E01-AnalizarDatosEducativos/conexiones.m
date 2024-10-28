let
    //Conectar a la carpeta de OneDrive
    Source = SharePoint.Files("https://sena4-my.sharepoint.com/personal/nombreCuenta", [ApiVersion = 15]), 
    
    //Filtrar para obtener solo archivos en la carpeta "pruebaW"
    FiltrarCarpeta = Table.SelectRows(Source, each Text.Contains([Folder Path], "/ruta/donde_Esta_el_archivo_base/") and [Extension] = ".xlsx")
in
    FiltrarCarpeta