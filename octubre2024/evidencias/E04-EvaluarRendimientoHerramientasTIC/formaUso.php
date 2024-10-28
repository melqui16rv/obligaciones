<?php
require_once 'metodos_evaluador.php'; //se puede modificar dependiendo de donde se encuentre el archivo con el metodo(consulta)

if (isset($_GET['num_doc'])) { //para hacer un ejemplo se puede poner directamente un numero de documento de un candidato
    $numero_documento = $_GET['num_doc'];
    $result = new metodosEvaluador();
    $datos = $result->obtenerDatosEstructuradosPorNumeroDocumento($numero_documento);
    if (empty($datos)) {
        $datos = null;
    }
} else {
    $datos = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación del Candidato</title>
    <link rel="icon" href="../../assets/img/logos/logosena.png">
    <link rel="stylesheet" href="../../assets/css/links/evaluar.css">
</head>
<body>
    <div class="info-container" id="infoContainer">
        <h3>Información Vacante</h3><br>
        <!-- a continuación se trae la informaición poniendo el nombre de tabla y el campo, sino se encuentran datos mostrara un mensaje diciendo 'No disponible' -->
        <p><strong>Codigo Vacante:</strong> <?php echo isset($datos['candidato']['cod_vacante']) ? $datos['candidato']['cod_vacante'] : 'No disponible'; ?></p>
        <p><strong>Coordinación:</strong> <span id="coordinacion"><?php echo isset($datos['coordinacion']['nombre_c']) ? $datos['coordinacion']['nombre_c'] : 'No disponible'; ?></span></p>
        <p><strong>Centro de Formación:</strong> <?php echo isset($datos['vacante']['centro_formacion']) ? $datos['vacante']['centro_formacion'] : 'No disponible'; ?></p>
        <p><strong>Modalidad de Contratación:</strong> <?php echo isset($datos['vacante']['modalidad_contratacion']) ? $datos['vacante']['modalidad_contratacion'] : 'No disponible'; ?></p>
        <p><strong>Tiempo de Contratación:</strong> <?php echo isset($datos['vacante']['tiempo_contratacion']) ? $datos['vacante']['tiempo_contratacion'] : 'No disponible'; ?></p>
        <p><strong>Tiempo de Contratación:</strong> <?php echo isset($datos['tipo_formacion']['descripcion']) ? $datos['tipo_formacion']['descripcion'] : 'No disponible'; ?></p>
    </div>
</body>