<?php
//Mostrar errores generados por algun tipo de acción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
require_once '../../app/config.php';
requireRole(['2']);
require_once '../../sql/class.php';



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$miClase = new Trabajo();

if (isset($_GET['coordinacion'])) {
    $coordinacion = $_GET['coordinacion'];
    $codigos = $miClase->obtenerCodigosVacantePorCoordinacion($coordinacion);
    
    header('Content-Type: application/json');
    echo json_encode($codigos);
    exit;
}

if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $candidatos = $miClase->obtenerCandidatosCoordinador($busqueda);
    echo json_encode($candidatos);
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'solicitar_traslado') {
    $documento_candidato = $_POST['documento_candidato'];
    $cod_vacante_actual = $_POST['cod_vacante_actual'];
    $cod_vacante_solicitado = $_POST['codigoTraslado'];
    $coordinacion_final = $_POST['coordinacionFinal'];
    $numero_documento_coordinador = $_SESSION['numero_documento'];

    error_log('Datos recibidos para traslado:');
    error_log('Documento candidato: ' . $documento_candidato);
    error_log('Código vacante actual: ' . $cod_vacante_actual);
    error_log('Código vacante solicitado: ' . $cod_vacante_solicitado);
    error_log('Coordinación Final: ' . $coordinacion_final);

    try {
        $id_coordinacion_final = $miClase->obtenerIdCoordinacionPorNombre($coordinacion_final);
        $resultado = $miClase->insertarTraslado($documento_candidato, $cod_vacante_actual, $cod_vacante_solicitado, $id_coordinacion_final, $numero_documento_coordinador);

        if ($resultado) {
            $respuesta = ['estado' => 'exito', 'mensaje' => 'Solicitud de traslado enviada correctamente.'];
        } else {
            $respuesta = ['estado' => 'error', 'mensaje' => 'Error al enviar la solicitud de traslado.'];
        }
    } catch (Exception $e) {
        $respuesta = ['estado' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()];
    }

    header('Content-Type: application/json');
    echo json_encode($respuesta);
    exit;
}

$candidatos = $miClase->obtenerCandidatosCoordinador();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta</title>
    <link rel="stylesheet" href="../../assets/css/links/candidatosCoordinador.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
        require '../../app/shareFolder/header.php';
        require '../../app/shareFolder/navbar.php';
        require '../../app/shareFolder/backButton.php';
    ?>

    <div class="contenedor">
        <div class="buscador">
            <h2 class="titulo">Buscar</h2>
            <div class="formBusqueda">
                <input type="text" id="buscar" name="buscar" class="codigo" 
                       placeholder="Buscar por documento o nombre">
            </div>
        </div>
        <div class="buscador">
            <h2 class="subTitulo">Filtros por Coordinación</h2>
            <button type="button" class="filtro-btn" data-role="Instructor">TITULADA</button>
            <button type="button" class="filtro-btn" data-role="Evaluador">COMPLEMENTARIA</button>
            <button type="button" class="filtro-btn" data-role="Control">SER</button>
        </div>
        <div class="informacionDeconsulta">
            <h2>Información de la Consulta</h2>
            <p><strong>Código:</strong>26378</p>
            <p><strong>Coordinación Inicial:</strong>ARTICULACIÓN</p>
            <p><strong>Programa:</strong>CONTABILIZACION DE OPERACIONES COMERCIALES Y FINANCIERAS.</p>
        </div><br>
        <h2>Resultados de la Consulta</h2>
        <div class="tablaGeneradaPorLaConsulta">
            
            <table>
                <thead>
                    <tr>
                        <th class="border_left">Código</th>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Estado BANIN</th>
                        <th>Coordinación Inicial</th>
                        <th>Coordinación Final</th>
                        <th>Traslado</th>
                        <th>Reclamación</th>
                        <th>Protección</th>
                        <th class="border_right" style="text-align: center;">Solicitud</th>
                    </tr>
                </thead>
                <tbody id="tablaCandidatos">

                </tbody>
            </table>
        </div>

        <div id="modalTraslado" class="modal">
            <div class="modal-content">
                <button class="close" type="button">&times;</button>
                <h2>Formulario de Traslado</h2>
                <form id="formTraslado">
                    <input type="hidden" id="documento_candidato" name="documento_candidato">
                    <label for="coordinacionInicial">Coordinación Inicial:</label>
                    <input type="text" id="coordinacionInicial" readonly>
                    
                    <label for="codigo">Código Vacante Actual:</label>
                    <input type="text" id="codigo" name="cod_vacante_actual" readonly>
        
                    <label for="coordinacionFinal">Coordinación Final:</label>
                    <select id="coordinacionFinal" name="coordinacionFinal" required>
                        <option value="">Seleccione una opción</option>
                        <option value="SER">SER</option>
                        <option value="TITULADA">TITULADA</option>
                        <option value="ARTICULACIÓN">ARTICULACIÓN</option>
                        <option value="COMPLEMENTARIA">COMPLEMENTARIA</option>
                        <option value="FIC">FIC</option>
                        <option value="VICTIMAS">VICTIMAS</option>
                    </select>
                    
                    <label for="codigoTraslado">Código de Traslado:</label>
                    <div class="codigo-traslado-container">
                        <select id="codigoTraslado" name="codigoTraslado" required disabled>
                            <option value="">Primero seleccione una coordinación</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="modal-submit-btn">Enviar Solicitud de Traslado</button>
                </form>
            </div>
        </div>
    </div>

    <?php 
    require '../shareFolder/footer.php';
    ?>
<script>
$(document).ready(function() {
    function cargarCandidatos(busqueda = '') {
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: { busqueda: busqueda },
            dataType: 'json',
            success: function(data) {
                let html = '';
                data.forEach(function(candidato, index) {
                    html += `
                        <tr id="row-${index}">
                            <td>${candidato.cod_vacante || ''}</td>
                            <td>${candidato.numero_documento}</td>
                            <td>${candidato.nombre_completo}</td>
                            <td>${candidato.estadoBANIN}</td>
                            <td>${candidato.coordinacion_inicial_nombre || ''}</td>
                            <td>${candidato.coordinacion_final_nombre || ''}</td>
                            <td>${candidato.traslado ? candidato.traslado : ''}
                                <div class="icons transfer-icon">
                                    <svg class="btnTrasladar trasladar" xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-shuffle">
                                        <polyline points="16 3 21 3 21 8"></polyline>
                                        <line x1="4" y1="20" x2="21" y2="3"></line>
                                        <polyline points="21 16 21 21 16 21"></polyline>
                                        <line x1="15" y1="15" x2="21" y2="21"></line>
                                    </svg>
                                </div>
                            </td>
                            <td>${candidato.reclamacion || ''}</td>
                            <td>${candidato.proteccion || ''}</td>
                            <td style="text-align: center;">
                                <a href="../coordinador/forms.php"><button class="perfil-btn" type="submit">Editar</button></a>
                            </td>
                        </tr>
                    `;
                });
                $('#tablaCandidatos').html(html);
            }
        });
    }

    cargarCandidatos();

    $('#buscar').on('input', function() {
        let busqueda = $(this).val();
        cargarCandidatos(busqueda);
    });

    $('.filtro-btn').click(function() {
        $(this).toggleClass('active');
    });

    const modal = document.getElementById("modalTraslado");
    const span = document.querySelector(".close");
    
    function openModal(event) {
        console.log("Abriendo modal...");
        modal.classList.add('active');
        
        const row = $(event.target).closest('tr');
        const codigo = row.find('td:first').text();
        const numero_documento = row.find('td:eq(1)').text().trim();
        const coordinacionInicial = row.find('td:eq(4)').text().trim();
        
        $('#codigo').val(codigo);
        $('#documento_candidato').val(numero_documento);
        $('#coordinacionInicial').val(coordinacionInicial);
        $('#formTraslado').data('rowId', row.attr('id'));
        updateCoordinacionFinalOptions(coordinacionInicial);
    }

    function updateCoordinacionFinalOptions(coordinacionInicial) {
        const coordinaciones = ['SER', 'TITULADA', 'ARTICULACIÓN', 'COMPLEMENTARIA', 'FIC', 'VICTIMAS'];
        const $coordinacionFinal = $('#coordinacionFinal');
        const excludeInitialCoordination = true;
        
        $coordinacionFinal.empty().append('<option value="">Seleccione una opción</option>');
        coordinaciones.forEach(coord => {
            if (!excludeInitialCoordination || coord !== coordinacionInicial) {
                $coordinacionFinal.append(`<option value="${coord}">${coord}</option>`);
            }
        });
    }
    $('#coordinacionFinal').on('change', function() {
        const selectedCoordinacion = $(this).val();
        const $codigoTraslado = $('#codigoTraslado');
        
        if (selectedCoordinacion) {
            $codigoTraslado.prop('disabled', false);
            obtenerCodigosDisponibles(selectedCoordinacion);
        } else {
            $codigoTraslado.prop('disabled', true)
                          .html('<option value="">Primero seleccione una coordinación</option>');
        }
    });

    function obtenerCodigosDisponibles(coordinacion) {
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: { coordinacion: coordinacion },
            dataType: 'json',
            success: function(codigos) {
                const $codigoTraslado = $('#codigoTraslado');
                $codigoTraslado.empty().append('<option value="">Seleccione un código</option>');
                
                codigos.forEach(codigo => {
                    $codigoTraslado.append(`<option value="${codigo}">${codigo}</option>`);
                });
            },
            error: function() {
                console.error('Error al obtener códigos');
                $('#codigoTraslado')
                    .prop('disabled', true)
                    .html('<option value="">Error al cargar códigos</option>');
            }
        });
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    $(document).on('click', '.btnTrasladar', openModal);
    
    span.onclick = closeModal;
    
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    $('#formTraslado').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            accion: 'solicitar_traslado',
            documento_candidato: $('#documento_candidato').val(),
            cod_vacante_actual: $('#codigo').val(),
            coordinacionInicial: $('#coordinacionInicial').val(),
            coordinacionFinal: $('#coordinacionFinal').val(),
            codigoTraslado: $('#codigoTraslado').val()
        };
    
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.estado === 'exito') {
                    alert(respuesta.mensaje);
                    closeModal();
                    cargarCandidatos($('#buscar').val());
                } else {
                    alert('Error: ' + respuesta.mensaje);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON ? xhr.responseJSON.mensaje : 'Error desconocido al enviar la solicitud de traslado.';
                alert(errorMessage);
            }
        });
    });

});
</script>
</body>
</html>
