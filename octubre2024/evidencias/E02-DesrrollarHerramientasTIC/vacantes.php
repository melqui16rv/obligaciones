<?php
require_once '../../app/config.php';
require_once '../../sql/class.php';
requireRole(['2']);

//Se valida de que halla una sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se verifica en base si el usuario logueado esta en base
if (!isset($_SESSION['numero_documento'])) {
    //Si no está en la sesión, redirige al usuario al login o se muestra error
    die("Error: No se ha iniciado sesión correctamente.");
}

$result1 = new Trabajo();
$datos1 = $result1->obtenerVacantesCoordinador();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_vacante = $_POST['codigo'];
    $nombre_vacante = $_POST['nombre_vacante'];
    $perfil_vacante = $_POST['perfil_vacante'];
    $nro_instr_req = $_POST['nro_instr_req'];
    $num_doc_candidato = $_POST['num_doc_evaluador'];
    $Id_tipoF = $_POST['Id_tipoF'];
    $result2 = $result1->crearvacante($cod_vacante, $nombre_vacante, $perfil_vacante, $nro_instr_req, $num_doc_candidato, $Id_tipoF);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/code.jquery.com_jquery-3.7.1.min.js"></script>
    <link rel="icon" href="../../assets/img/logos/logoSena_2.png">
    <link rel="stylesheet" href="../../assets/css/links/vacantes.css">
    <title>BANIN</title>
</head>
<body>
    <?php
        require '../../app/shareFolder/header.php';
        require '../../app/shareFolder/navbar.php';
    ?>
    <div id="loadingOverlay" class="overlay">
        <div class="spinner-container">
            <div class="spinner"></div>
        </div>
    </div>
    <div class="contenedor">
        <div class="container">
            <h2>VACANTES ASIGNADAS</h2>
        
            <button id="mostrarFormulario">+</button>
            <div id="formulario" style="display: none; margin-top: 20px;">
                <h3>Agregar Nueva Vacante</h3>
                <form action="./vacantes.php" method="POST">
                    <label for="codigo">Codigo Vacante:</label>
                    <input type="text" id="codigo" name="codigo" required>
                    
                    <label for="nombre_vacante">Nombre Vacante</label>
                    <input type="text" id="nombre_vacante" name="nombre_vacante" required>
                    
                    <label for="perfil_vacante">Perfil Vacante</label>
                    <input type="text" id="perfil_vacante" name="perfil_vacante" required>
                    
                    <label for="nro_instr_req">Instructores requeridos</label>
                    <input type="number" id="nro_instr_req" name="nro_instr_req" required>

                    <label for="num_doc_evaluador">Documento Candidato</label>
                    <input type="text" id="num_doc_evaluador" name="num_doc_evaluador" required>
                    
                    <label for="Id_tipoF">Tipo Formación:</label>
                    <input type="text" id="Id_tipoF" name="Id_tipoF" required>

                    <input type="submit" value="Agregar Vacante">
                    <button type="button" id="cancelarFormulario">Cancelar</button>
                </form>
            </div>

            <div class="cards-container">
                <?php foreach($datos1 as $row){ 
                    //Obtener los conteos dinámicos
                    $numero_candidatos = $result1->contarCandidatosPorVacante($row['cod_vacante']);
                    $numero_vacantes = isset($row['nro_instr_req']) ? $row['nro_instr_req'] : '0';
                    
                ?>
                    <div class="card">
                        <p><strong>Código:</strong> <?php echo htmlspecialchars($row['cod_vacante']); ?></p>
                        <p><strong>Coordinación:</strong> <?php echo htmlspecialchars($row['nombre_c']); ?></p>
                        <p><strong>Nombre de Vacante:</strong> <?php echo htmlspecialchars($row['nombre_vacante']); ?></p>
                        <p><strong>Tipo de Formación:</strong> <?php echo htmlspecialchars($row['tipo_formacion']); ?></p>
                        <p><strong>Número de Candidatos:</strong> <?php echo htmlspecialchars($numero_candidatos); ?></p>
                        <p><strong>Número de Vacantes:</strong> <?php echo htmlspecialchars($numero_vacantes); ?></p>
                        <p><strong>Evaluados:</strong> 13</p>
                        <p><strong>Por Evaluar:</strong> 27</p>
                        <a href="./asignacionInstructor.php?cod_vacante=<?php echo urlencode($row['cod_vacante']); ?>"><button>Asignar</button></a>
                        <a href="./listaCandidatos.php?cod_vacante=<?php echo urlencode($row['cod_vacante']); ?>">
                            <button>VER..</button>
                        </a>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
    <?php
        require '../shareFolder/footer.php';
    ?>
<script>
    $(document).ready(function() {
        //Funsion para mostar u oculta el formulario al hacer clic
        $('#mostrarFormulario').click(function() {
            $('#formulario').toggle();
        });

        $('#cancelarFormulario').click(function() {
            $('#formulario').hide();
        });
    });
</script>
<script src="../../assets/js/animaCarga.js"></script>
</body>
</html>
