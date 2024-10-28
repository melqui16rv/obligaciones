<?php
require_once "bd.php";

class metodosEvaluador extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion=$this->conexion->obtenerConexion();
    }

    public function obtenerDatosEstructuradosPorNumeroDocumento($numero_documento) {
        $sql = "
            SELECT 
                /* Datos de candidato */
                candidato.id_coordinacion_inicial AS 'candidato.id_coordinacion_inicial',
                candidato.id_coordinacion_final AS 'candidato.id_coordinacion_final',
                candidato.traslado AS 'candidato.traslado',
                candidato.id_reclamacion AS 'candidato.id_reclamacion',
                candidato.id_protecion AS 'candidato.id_proteccion',
                candidato.id_aspiracion AS 'candidato.id_aspiracion',
                candidato.tipo_doc AS 'candidato.tipo_doc',
                candidato.numero_documento AS 'candidato.numero_documento',
                candidato.nombre_completo AS 'candidato.nombre_completo',
                candidato.estadoBANIN AS 'candidato.estadoBANIN',
                candidato.cod_vacante AS 'candidato.cod_vacante',
                candidato.link_APE AS 'candidato.link_APE',
                candidato.correo AS 'candidato.correo',
                candidato.telefono AS 'candidato.telefono',
                candidato.telefono2 AS 'candidato.telefono2',
                candidato.departamento AS 'candidato.departamento',
                candidato.contrato AS 'candidato.contrato',
                candidato.observacion AS 'candidato.observacion',
                candidato.id_calificacion AS 'candidato.id_calificacion',
                
                /* Datos de vacante */
                vacante.cod_vacante AS 'vacante.cod_vacante',
                vacante.id_coordinacion AS 'vacante.id_coordinacion',
                vacante.nombre_vacante AS 'vacante.nombre_vacante',
                vacante.perfil_vacante AS 'vacante.perfil_vacante',
                vacante.nro_instr_req AS 'vacante.nro_instr_req',
                vacante.modalidad AS 'vacante.modalidad',
                vacante.centro_formacion AS 'vacante.centro_formacion',
                vacante.nivel AS 'vacante.nivel',
                vacante.competencia_a_desarrollar AS 'vacante.competencia_a_desarrollar',
                vacante.honorario_mensual AS 'vacante.honorario_mensual',
                vacante.valor_dia AS 'vacante.valor_dia',
                vacante.modalidad_contratacion AS 'vacante.modalidad_contratacion',
                vacante.tiempo_contratacion AS 'vacante.tiempo_contratacion',
                vacante.meses AS 'vacante.meses',
                vacante.dias AS 'vacante.dias',
                vacante.fecha_inicio_contratacion AS 'vacante.fecha_inicio_contratacion',
                vacante.fecha_fin_contratacion AS 'vacante.fecha_fin_contratacion',
                vacante.costo_total AS 'vacante.costo_total',
                
                /* Datos de coordinación */
                coordinacion.id_coordinacion AS 'coordinacion.id_coordinacion',
                coordinacion.nombre_c AS 'coordinacion.nombre_c',
                
                /* Datos de tipo formación */
                tipo_formacion.id_tipof AS 'tipo_formacion.id_tipof',
                tipo_formacion.descripcion AS 'tipo_formacion.descripcion'
            FROM 
                candidato
            LEFT JOIN 
                vacante ON vacante.cod_vacante = candidato.cod_vacante
            LEFT JOIN 
                asignacion_instructores ON asignacion_instructores.cod_vacante = vacante.cod_vacante
            LEFT JOIN 
                usuario ON usuario.numero_documento = asignacion_instructores.numero_documento_instructor
            LEFT JOIN 
                coordinacion ON coordinacion.id_coordinacion = vacante.id_coordinacion
            LEFT JOIN 
                tipo_formacion ON tipo_formacion.id_tipof = vacante.id_tipof
            WHERE 
                candidato.numero_documento = :numero_documento
        ";

        $consult = $this->conexion->prepare($sql);
        $consult->bindParam(':numero_documento', $numero_documento);
        $consult->execute();
        $result = $consult->fetch(PDO::FETCH_ASSOC);
        
        if ($result === false || empty($result)) {
            return null; // Devuelve null si no hay resultados
        }
    
        // Estructuración de los datos por tablas
        $datos_estructurados = [
            'candidato' => [],
            'vacante' => [],
            'coordinacion' => [],
            'tipo_formacion' => []
        ];
    
        // Llenado de los datos estructurados
        foreach ($result as $key => $value) {
            $parts = explode('.', $key);
            if (count($parts) === 2) {
                $tabla = $parts[0];
                $campo = $parts[1];
                $datos_estructurados[$tabla][$campo] = $value;
            }
        }
    
        return $datos_estructurados;
    }

}