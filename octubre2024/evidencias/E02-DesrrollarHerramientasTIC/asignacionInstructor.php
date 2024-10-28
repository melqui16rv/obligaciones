<?php
//Mostrar errores generados por algun tipo de acción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php

require_once '../../app/config.php';
require_once '../../sql/class.php';
requireRole(['2']);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$result1 = new Trabajo();
$evaluadores = $result1->obtenerEvaluadores();
$cod_vacante = isset($_GET['cod_vacante']) ? $_GET['cod_vacante'] : '';
$actualizacionExitosa = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['asignar_instructor'])) {
        $num_doc_evaluador = $_POST['num_doc_evaluador'];
        $cod_vacante = $_POST['cod_vacante'];
        $coordinador_doc = $_SESSION['numero_documento'];

        if (empty($cod_vacante) || empty($num_doc_evaluador)) {
            $respuesta = ['success' => false, 'message' => 'Faltan datos requeridos.'];
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            exit;
        }

        try {
            $observaciones = "Asignado por " . $coordinador_doc;

            if ($result1->insertarAsignacionInstructor($cod_vacante, $num_doc_evaluador, $observaciones)) {
                $respuesta = ['success' => true, 'message' => 'Instructor asignado exitosamente.'];
            } else {
                $respuesta = ['success' => false, 'message' => 'Error al asignar el instructor.'];
            }
        } catch (Exception $e) {
            error_log('Error al asignar instructor: ' . $e->getMessage());
            $respuesta = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }
}

if (isset($_POST['ajax']) && $_POST['ajax'] === 'nombre_evaluador') {
    $num_doc_evaluador = $_POST['num_doc_evaluador'];
    $nombre_completo = $result1->obtenerNombreEvaluador($num_doc_evaluador);
    if ($nombre_completo === null) {
        echo json_encode(['success' => false, 'message' => 'Error: No se pudo obtener el nombre del evaluador']);
    } else {
        echo json_encode(['success' => true, 'nombre' => $nombre_completo]);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Evaluador</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/links/asignarInstructor.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
        require '../../app/shareFolder/header.php';
        require '../../app/shareFolder/navbar.php';
        require '../../app/shareFolder/backButton.php';
    ?>
    <div class="modalS"></div>
    <div class="container">
        <h2>Asignar Evaluador</h2>
        <div id="evaluator-forms">
        <form id="formAsignarInstructor" action="./asignacionInstructor.php?cod_vacante=<?php echo htmlspecialchars($cod_vacante); ?>" method="POST" class="evaluator-form">
            <input type="hidden" id="cod_vacante" name="cod_vacante" value="<?php echo htmlspecialchars($cod_vacante); ?>" required>
            
            <div class="form-group">
                <label for="num_doc_evaluador">Número de Documento del Instructor</label>
                <select id="num_doc_evaluador" name="num_doc_evaluador" required>
                    <option value="">Seleccione un evaluador</option>
                    <?php foreach ($evaluadores as $evaluador): ?>
                        <option value="<?php echo $evaluador['numero_documento']; ?>"><?php echo $evaluador['numero_documento']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo del Instructor</label>
                <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Nombre completo del instructor" readonly>
            </div>
            
            <input type="submit" value="Asignar">
        </form>

        </div>
        <div class="evaluator-buttons">
            <button type="button" class="add-evaluator" onclick="addEvaluatorForm()"><span>+</span> Agregar otro evaluador</button>
            <button type="button" class="remove-evaluator" onclick="removeEvaluatorForm()"><span>-</span> Quitar evaluador</button>
        </div>
    </div>
    <?php 
    require '../shareFolder/footer.php';
    ?>
<script>
$(document).ready(function() {
    let formCount = 1;
    const MAX_FORMS = 50;
    let evaluadoresAsignados = new Set();
    let evaluadoresSeleccionados = new Set();

    function actualizarNombreEvaluador(selectElement) {
        var num_doc = $(selectElement).val();
        var form = $(selectElement).closest('.evaluator-form');
        var nombreInput = form.find('[id^=nombre_completo]');
        
        if (num_doc !== '') {
            $.ajax({
                url: './asignacionInstructor.php',
                type: 'POST',
                data: { 
                    num_doc_evaluador: num_doc, 
                    ajax: 'nombre_evaluador' 
                },
                dataType: 'json',
                success: function(response){
                    if (response.success) {
                        nombreInput.val(response.nombre);
                    } else {
                        alert(response.message);
                        nombreInput.val('');
                    }
                },
                error: function() {
                    alert('Error al obtener el nombre del evaluador');
                    nombreInput.val('');
                }
            });
        } else {
            nombreInput.val('');
        }
    }

    function actualizarOpcionesDisponibles() {
        $('[id^=num_doc_evaluador]').each(function() {
            var selectorActual = $(this);
            var valorActual = selectorActual.val();

            selectorActual.find('option').each(function() {
                var opcion = $(this);
                if (opcion.val() && opcion.val() !== valorActual) {
                    opcion.prop('disabled', evaluadoresAsignados.has(opcion.val()) || 
                                            (evaluadoresSeleccionados.has(opcion.val()) && opcion.val() !== valorActual));
                }
            });
        });
    }

    $(document).on('change', '[id^=num_doc_evaluador]', function() {
        var valorAnterior = $(this).data('valor-anterior');
        var valorActual = $(this).val();

        if (valorAnterior) {
            evaluadoresSeleccionados.delete(valorAnterior);
        }

        if (valorActual) {
            evaluadoresSeleccionados.add(valorActual);
        }

        $(this).data('valor-anterior', valorActual);

        actualizarNombreEvaluador(this);
        actualizarOpcionesDisponibles();
    });

    window.addEvaluatorForm = function() {
        if ($('.evaluator-form').length >= MAX_FORMS) {
            alert("Para asignar más instructores a esta vacante, debe guardar los 50 y volver a abrir el formulario de asignación.");
            return;
        }

        formCount++;
        const newForm = $('.evaluator-form').first().clone();
        newForm.find('input, select').each(function() {
            if (this.name === 'cod_vacante') {
                //Mantener el valor de cod_vacante
                $(this).attr('id', this.id + formCount);
                //No limpiar el valor
            } else {
                let newId = this.id + formCount;
                $(this).attr('id', newId).val('').data('valor-anterior', '');
            }
        });

        newForm.find('input[type="submit"]').val('Asignar');

        $('#evaluator-forms').append(newForm);
        actualizarOpcionesDisponibles();
    }

    window.removeEvaluatorForm = function() {
        if (formCount > 1) {
            var ultimoForm = $('.evaluator-form').last();
            var ultimoSelect = ultimoForm.find('[id^=num_doc_evaluador]');
            var valorUltimoSelect = ultimoSelect.val();

            if (valorUltimoSelect) {
                evaluadoresSeleccionados.delete(valorUltimoSelect);
                evaluadoresAsignados.add(valorUltimoSelect);
            }

            ultimoForm.remove();
            formCount--;
            actualizarOpcionesDisponibles();
        } else {
            alert("Debe haber al menos un formulario de evaluador.");
        }
    }

    $(document).on('submit', '.evaluator-form', function(e) {
        e.preventDefault();

        const form = $(this);
        const num_doc_evaluador = form.find('[name=num_doc_evaluador]').val();
        const cod_vacante = form.find('[name=cod_vacante]').val();

        if (!num_doc_evaluador || !cod_vacante) {
            alert("Error: Faltan datos requeridos.");
            return;
        }

        const formData = {
            asignar_instructor: 1,
            num_doc_evaluador: num_doc_evaluador,
            cod_vacante: cod_vacante
        };

        $.ajax({
            url: './asignacionInstructor.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    
                    evaluadoresAsignados.add(num_doc_evaluador);
                    evaluadoresSeleccionados.delete(num_doc_evaluador);
                    
                    if ($('.evaluator-form').length === 1) {
                        form.find('[name=num_doc_evaluador]').val('');
                        form.find('[name=nombre_completo]').val('');
                        form.find('[id^=num_doc_evaluador]').data('valor-anterior', '');
                    } else {
                        form.remove();
                    }

                    formCount = $('.evaluator-form').length;
                    
                    if (formCount === 0) {
                        addEvaluatorForm();
                    }
                    
                    actualizarOpcionesDisponibles();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido al procesar la asignación.';
                alert(errorMessage);
            }
        });
    });

    actualizarOpcionesDisponibles();
});
</script>
</body>
</html>
