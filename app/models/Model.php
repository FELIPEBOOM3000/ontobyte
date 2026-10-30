<?php
/**
 * ============================================================================
 * ARCHIVO: app/models/Model.php
 * ============================================================================
 * Propósito: Clase base abstracta para todos los modelos de la aplicación.
 *            Proporciona funcionalidad común como acceso a la base de datos
 *            e implementa métodos genéricos (CRUD básico) que heredan
 *            todos los modelos específicos del sistema.
 * ============================================================================
 */

abstract class Model
{
    /** @var PDO Instancia de conexión a la base de datos */
    protected $db;

    /** @var string Nombre de la tabla que maneja el modelo hijo */
    protected $table;

    /** @var array Campos que pueden ser asignados masivamente */
    protected $fillable = [];

    /**
     * Constructor del modelo base.
     * Obtiene la instancia Singleton de la conexión a la base de datos.
     * Solo la clase base conoce la configuración del conector (bajo
     * acoplamiento) y lo heredan todos los modelos hijos.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los registros de la tabla con paginación opcional.
     *
     * @param int|null $limit  Cantidad máxima de registros a obtener
     * @param int      $offset Desplazamiento para paginación
     * @return array Lista de registros
     */
    public function all(?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";

        /* Aplicar límites si fueron proporcionados */
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Busca un registro por su clave primaria (ID).
     *
     * @param int $id Identificador del registro
     * @return array|null Registro encontrado o null si no existe
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Elimina un registro de la tabla por su ID.
     *
     * @param int $id ID del registro a eliminar
     * @return bool True si la eliminación fue exitosa
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cuenta el número total de registros de la tabla.
     *
     * @return int Cantidad total de registros
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Ejecuta una consulta personalizada con parámetros preparados.
     * Método generalizado que permite consultas complejas específicas
     * dentro de cada modelo hijo.
     *
     * @param string $sql    Consulta SQL con parámetros nombrados (:param)
     * @param array  $params Array asociativo con los parámetros
     * @return array Resultado de la consulta en formato array
     */
    protected function executeQuery(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}