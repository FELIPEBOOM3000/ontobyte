<?php
/**
 * ============================================================================
 * ARCHIVO: app/models/Service.php
 * ============================================================================
 * Propósito: Modelo de la entidad "Servicio". Gestiona el catálogo de
 *            servicios que ofrece OntoByte y los expone para mostrarse
 *            en la página principal y de servicios. Solo devuelve
 *            registros activos y ordenados según configuración.
 * ============================================================================
 */

require_once MODELS_PATH . '/Model.php';

class Service extends Model
{
    /** @var string Nombre de la tabla en la base de datos */
    protected $table = 'services';

    /**
     * Obtiene todos los servicios activos ordenados por prioridad.
     * Método personalizado para la visualización pública.
     *
     * @return array Lista de servicios activos
     */
    public function getActiveServices(): array
    {
        $sql = "SELECT id, title, slug, description, short_desc, icon_class
                FROM {$this->table}
                WHERE is_active = 1
                ORDER BY sort_order ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}