<?php
/**
 * ============================================================================
 * ARCHIVO: app/models/TeamMember.php
 * ============================================================================
 * Propósito: Modelo de la entidad "Miembro del Equipo". Gestiona la
 *            información de las personas que forman parte de OntoByte
 *            y la expone para la sección del equipo en el sitio web.
 * ============================================================================
 */

require_once MODELS_PATH . '/Model.php';

class TeamMember extends Model
{
    /** @var string Nombre de la tabla en la base de datos */
    protected $table = 'team_members';

    /**
     * Obtiene los miembros activos del equipo ordenados por prioridad.
     *
     * @return array Lista de miembros activos
     */
    public function getActiveMembers(): array
    {
        $sql = "SELECT id, full_name, role, bio, photo, linkedin_url, github_url
                FROM {$this->table}
                WHERE is_active = 1
                ORDER BY sort_order ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}