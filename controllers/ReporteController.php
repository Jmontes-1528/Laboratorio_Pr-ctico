<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class ReporteController {
 private PDO $db;

 public function __construct() {
 $this->db = Database::getConnection();
 }

 public function obtenerMetricas(array $filtros = []): array {
 $where = [];
 $params = [];

 if (!empty($filtros['desde'])) {
 $where[] = "d.fecha_recepcion >= :desde";
 $params[':desde'] = $filtros['desde'];
 }
 if (!empty($filtros['hasta'])) {
 $where[] = "d.fecha_recepcion <= :hasta";
 $params[':hasta'] = $filtros['hasta'];
 }

 $sqlWhere = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

 $sqlEstatus = "
 SELECT
 e.id_estatus,
 e.nombre AS estatus,
 e.color_badge,
 COUNT(d.id_documento) AS total
 FROM estatus_documento e
 LEFT JOIN documentos d ON e.id_estatus = d.id_estatus
 " . (!empty($filtros['desde']) ? "AND d.fecha_recepcion >= :d_desde " : "") . "
 " . (!empty($filtros['hasta']) ? "AND d.fecha_recepcion <= :d_hasta " : "") . "
 GROUP BY e.id_estatus, e.nombre, e.color_badge
 ORDER BY e.orden ASC
 ";

 $stmtEstatus = $this->db->prepare($sqlEstatus);
 if (!empty($filtros['desde'])) {
 $stmtEstatus->bindValue(':d_desde', $filtros['desde']);
 }
 if (!empty($filtros['hasta'])) {
 $stmtEstatus->bindValue(':d_hasta', $filtros['hasta']);
 }
 $stmtEstatus->execute();
 $porEstatus = $stmtEstatus->fetchAll();

 $totalDocumentos = 0;
 $totalRecibidos = 0;
 $totalEnProceso = 0;
 $totalAtendidos = 0;

 foreach ($porEstatus as $item) {
 $totalDocumentos += (int)$item['total'];
 if ($item['id_estatus'] == 1) $totalRecibidos = (int)$item['total'];
 if ($item['id_estatus'] == 2) $totalEnProceso = (int)$item['total'];
 if ($item['id_estatus'] == 3) $totalAtendidos = (int)$item['total'];
 }

 $tasaResolucion = $totalDocumentos > 0 ? round(($totalAtendidos / $totalDocumentos) * 100, 1) : 0;

 $sqlTipos = "
 SELECT
 COALESCE(t.nombre, 'Sin Clasificar') AS tipo,
 COUNT(d.id_documento) AS total
 FROM tipos_documento t
 LEFT JOIN documentos d ON t.id_tipo = d.id_tipo
 " . (!empty($filtros['desde']) ? "AND d.fecha_recepcion >= :t_desde " : "") . "
 " . (!empty($filtros['hasta']) ? "AND d.fecha_recepcion <= :t_hasta " : "") . "
 GROUP BY t.id_tipo, t.nombre
 HAVING total > 0
 ORDER BY total DESC
 ";
 $stmtTipos = $this->db->prepare($sqlTipos);
 if (!empty($filtros['desde'])) {
 $stmtTipos->bindValue(':t_desde', $filtros['desde']);
 }
 if (!empty($filtros['hasta'])) {
 $stmtTipos->bindValue(':t_hasta', $filtros['hasta']);
 }
 $stmtTipos->execute();
 $porTipo = $stmtTipos->fetchAll();

 $sqlAreas = "
 SELECT
 des.area_departamento AS area,
 COUNT(d.id_documento) AS total
 FROM documentos d
 INNER JOIN destinatarios des ON d.id_destinatario = des.id_destinatario
 {$sqlWhere}
 GROUP BY des.area_departamento
 ORDER BY total DESC
 LIMIT 6
 ";
 $stmtAreas = $this->db->prepare($sqlAreas);
 $stmtAreas->execute($params);
 $porArea = $stmtAreas->fetchAll();

 $sqlUltimos = "
 SELECT * FROM " . SQL_FROM_DOCUMENTOS_COMPLETOS . "
 {$sqlWhere}
 ORDER BY fecha_recepcion DESC, id_documento DESC
 LIMIT 20
 ";
 $stmtUltimos = $this->db->prepare($sqlUltimos);
 $stmtUltimos->execute($params);
 $ultimosDocs = $stmtUltimos->fetchAll();

 return [
 'kpis' => [
 'total' => $totalDocumentos,
 'recibidos' => $totalRecibidos,
 'en_proceso' => $totalEnProceso,
 'atendidos' => $totalAtendidos,
 'tasa_resolucion' => $tasaResolucion
 ],
 'por_estatus' => $porEstatus,
 'por_tipo' => $porTipo,
 'por_area' => $porArea,
 'documentos' => $ultimosDocs
 ];
 }
}
