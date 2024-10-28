<?php
//Mostrar errores generados por algun tipo de acción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
require_once '../../sql/class.php';
require_once '../../app/config.php';
requireRole(['1']);
$trabajo = new Trabajo();
$datos = $trabajo->buscar_usuario('');

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

// Manejo de solicitudes AJAX para búsqueda de candidatos
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $candidatos = $miClase->obtenerCandidatos_admin($busqueda);
    echo json_encode($candidatos);
    exit;
}
$candidatos = $miClase->obtenerCandidatos_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta</title>
    <link rel="stylesheet" href="../../assets/css/links/usuariosAdmin.css">
    <link rel="stylesheet" href="../../assets/css/shareInFolder/styleTabla.css">
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

    <div id="mainContent" class="contenedor">
        <label class="toggle-switch">
            <input type="checkbox" id="viewToggle">
            <span class="slider">
                <span class="slider-text left">Usuarios</span>
                <span class="slider-text right">Candidatos</span>
            </span>
        </label>
        <div class="modalActualiarDatos">
            <button id="openModalButton" class="icon-button">&#9881;</button>
            <button id="mostrarFormulario">&#x271A;</button>
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

            
            <div id="modalActualizar" class="modal">
                <div class="modal-content">
                    <span class="close-button" id="closeModalButton">&times;</span>
                    <h1>Actualizar Datos BANIN</h1>
                    <div class="info">
                        <p class="fecha">Fecha de última actualización: 19/09/2024</p>
                        <p class="hora">Hora de última actualización: 11:30am</p>
                    </div>
                    <div class="botones">
                        <button class="boton actualizar">ACTUALIZAR</button>
                        <button class="boton descargar">DESCARGAR DATOS BANIN</button>
                    </div>
                    <div class="estado">
                        <p>Estado: Datos actualizados</p>
                        <p>Vacantes: 150</p>
                        <p>Nuevas Vacantes desde la última actualización: 5</p>
                    </div>
                </div>
            </div>
        </div>


        
        <div class="buscador">
            <h2 class="titulo">Buscar</h2>
            <div class="formBusqueda">
                <input type="text" id="buscar" name="buscar" class="codigo" 
                       placeholder="Buscar por documento o nombre">
            </div>
        </div>
        <div id="usuariosView">
            
            <div class="buscador">
                <h2 class="subTitulo">Filtros por roles</h2>
                <button type="button" class="filtro-btn" data-role="Instructor">Instructor</button>
                <button type="button" class="filtro-btn" data-role="Evaluador">Evaluador</button>
                <button type="button" class="filtro-btn" data-role="Control">Control</button>
            </div>
            <!-- Se espera agregar la funcionalidad de mostrar la información dependiendo de la busqueda  -->
            <!--
            <div class="infoConsulta">
                <h2 class="tituloConsulta">Información de la Consulta</h2>
                <p><strong>Número de documento:</strong> 1072645387</p>
                <p><strong>Nombre:</strong> No especificado</p>
                <p><strong>Rol:</strong> No especificado</p>
            </div>-->
            <br>
            <a href="<?php echo BASE_URL; ?>app/administrador/formAgregarUsuario.php">
                <button class="perfil-btn" type="button">Agregar Usuario</button>
            </a>
            <h2>Resultados de la Consulta</h2>
            <div class="tablaGeneradaPorLaConsulta">
                <table>
                    <thead>
                        <tr>
                            <th class="border_left">Id rol</th>
                            <th>Número de documento</th>
                            <th>Tipo de documento</th>
                            <th>Nombre completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th style="text-align: center;">Editar</th>
                            <th class="border_right" style="text-align: center;">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($datos as $row) { ?>
                        <tr>
                            <td><?php echo $row['id_rol']; ?></td>
                            <td><?php echo $row['numero_documento']; ?></td>
                            <td><?php echo $row['tipo_doc']; ?></td>
                            <td><?php echo $row['nombre_completo']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td><?php echo $row['nombre_rol']; ?></td>
                            <td style="text-align: center;">
                                <a href="../administrador/formEditarUsuario.php?numero=<?php echo $row['numero_documento']; ?>">
                                    <button class="editar-btn">&#9998;</button>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a onclick="return confirmacion()" 
                                   href="../administrador/eliminar.php?numero=<?php echo $row['numero_documento']; ?>">
                                    <button class="eliminar-btn">Eliminar</button>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="candidatosView">
            <div class="buscador">
                <h2 class="subTitulo">Filtros por Coordinación</h2>
                <button type="button" class="filtro-btn" data-role="Instructor">TITULADA</button>
                <button type="button" class="filtro-btn" data-role="Evaluador">COMPLEMENTARIA</button>
                <button type="button" class="filtro-btn" data-role="Control">SER</button>
            </div>
            <!-- Se espera agregar la funcionalidad de mostrar la información dependiendo de la busqueda  -->

            <!-- <div class="infoConsulta">
                <h2 class="tituloConsulta">Información de la Consulta</h2>
                <p><strong>Número de documento:</strong> 1072645387</p>
                <p><strong>Nombre:</strong> No especificado</p>
                <p><strong>Rol:</strong> No especificado</p>
            </div>-->
            <br>
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
        </div>
    </div>
    
    <?php 
        require '../shareFolder/footer.php';
    ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('buscar');
        const tableBody = document.querySelector('.tablaGeneradaPorLaConsulta tbody');
        let typingTimer;
        const doneTypingInterval = 500;

        function actualizarTabla(searchTerm = '') {
            fetch(`buscar_usuarios.php?termino=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    data.forEach(user => {
                        const row = `
                            <tr>
                                <td>${user.id_rol}</td>
                                <td>${user.numero_documento}</td>
                                <td>${user.tipo_doc}</td>
                                <td>${user.nombre_completo}</td>
                                <td>${user.email}</td>
                                <td>${user.telefono}</td>
                                <td>${user.nombre_rol}</td>
                                <td style="text-align: center;">
                                    <a href="../administrador/formEditarUsuario.php?numero=${user.numero_documento}">
                                        <button class="editar-btn">&#9998;</button>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <a onclick="return confirmacion()" 
                                       href="../administrador/eliminar.php?numero=${user.numero_documento}">
                                        <button class="eliminar-btn">Eliminar</button>
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                actualizarTabla(this.value);
            }, doneTypingInterval);
        });

    });

function confirmacion() {
        return confirm('¿Desea borrar el registro?');
    }
</script>

<script>
function confirmacion(){
    var respuesta=confirm('¿Desea borrar el registro?');
    return respuesta;
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('loadingOverlay');
    const mainContent = document.getElementById('mainContent');
    
    setTimeout(() => {
        overlay.style.display = 'none';
        
        mainContent.classList.add('loaded');
    }, 2000);
});
</script>

<script>
document.getElementById('viewToggle').addEventListener('change', function() {
    const usuariosView = document.getElementById('usuariosView');
    const candidatosView = document.getElementById('candidatosView');
    
    if (this.checked) {
        usuariosView.style.display = 'none';
        candidatosView.style.display = 'block';
    } else {
        usuariosView.style.display = 'block';
        candidatosView.style.display = 'none';
    }
});
</script>

<!-- -- -->
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
                if (data.length === 0) {
                    html = '<tr><td colspan="10" style="text-align:center;">No se encontraron resultados.</td></tr>';
                } else {
                    data.forEach(function(candidato, index) {
                        html += `
                            <tr id="row-${index}">
                                <td>${candidato.cod_vacante || ''}</td>
                                <td>${candidato.numero_documento}</td>
                                <td>${candidato.nombre_completo}</td>
                                <td>${candidato.estadoBANIN}</td>
                                <td>${candidato.coordinacion_inicial_nombre || ''}</td>
                                <td>${candidato.coordinacion_final_nombre || ''}</td>
                                <td>${candidato.traslado ? candidato.traslado : 'Esperando confirmación.'}</td>
                                <td>${candidato.reclamacion || ''}</td>
                                <td>${candidato.proteccion || ''}</td>
                                <td style="text-align: center;">
                                    <a href="../administrador/traslado.php?documento=${candidato.numero_documento}">
                                        <button class="editar-btn">&#9998;</button>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#tablaCandidatos').html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                $('#tablaCandidatos').html('<tr><td colspan="10" style="text-align:center;">Error al cargar los datos.</td></tr>');
            }
        });
    }


    cargarCandidatos();

    //Se maneja la busqueda en tiempo real
    $('#buscar').on('input', function() {
        let busqueda = $(this).val();
        cargarCandidatos(busqueda);
    });

    $('.filtro-btn').click(function() {
        $(this).toggleClass('active');
    });


    function updateCoordinacionFinalOptions(coordinacionInicial) {
        const coordinaciones = ['SER', 'TITULADA', 'ARTICULACIÓN', 'COMPLEMENTARIA'];
        const $coordinacionFinal = $('#coordinacionFinal');
        
        //Variable de configuración: Cambia a 'false' para permitir seleccionar la misma coordinación Al contrario true
        const excludeInitialCoordination = false;

        $coordinacionFinal.empty().append('<option value="">Seleccione una opción</option>');
        
        coordinaciones.forEach(coord => {
            if (!excludeInitialCoordination || coord !== coordinacionInicial) {
                $coordinacionFinal.append(`<option value="${coord}">${coord}</option>`);
            }
        });
    }


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

});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalActualizar');
        const openModalButton = document.getElementById('openModalButton');
        const closeModalButton = document.getElementById('closeModalButton');

        //Abrir el modal
        openModalButton.addEventListener('click', function() {
            modal.style.display = 'block';
        });

        //Cerrar el modal
        closeModalButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        //Cerrar el modal si se hace clic fuera del contenido del modal
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>
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
</body>
</html>
