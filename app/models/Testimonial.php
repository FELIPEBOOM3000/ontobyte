<?php
/**
 * ============================================================================
 * ARCHIVO: app/models/Testimonial.php
 * ============================================================================
 * Propósito: Modelo de la entidad "Testimonio". Gestiona las opiniones de
 *            los clientes de OntoByte para mostrarse como prueba social
 *            en la página principal.
 * ============================================================================
 */

require_once MODELS_PATH . '/Model.php';

class Testimonial extends Model
{
    /** @var string Nombre de la tabla en la base de datos */
    protected $table = 'testimonials';

    /**
     * Obtiene los testimonios activos ordenados por prioridad.
     * Solo se muestran testimonios activos y con calificación.
     *
     * @return array Lista de testimonios activos
     */
    public function getActiveTestimonials(): array
    {
        $sql = "SELECT id, client_name, client_role, content, rating, photo
                FROM {$this->table}
                WHERE is_active = 1
                ORDER BY sort_order ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}