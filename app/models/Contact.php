<?php
/**
 * ============================================================================
 * ARCHIVO: app/models/Contact.php
 * ============================================================================
 * Propósito: Modelo de la entidad "Contacto". Gestiona los mensajes enviados
 *            a través del formulario de contacto del sitio web corporativo.
 *            Almacena los mensajes con sentencias preparadas y proporciona
 *            métodos para consultar, marcar como leído/respondido y contar
 *            mensajes pendientes.
 * ============================================================================
 */

require_once MODELS_PATH . '/Model.php';

class Contact extends Model
{
    /** @var string Nombre de la tabla en la base de datos */
    protected $table = 'mensajes_contacto';

    /** @var array Campos editables del modelo */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'subject',
        'message',
        'service_interest',
        'ip_address'
    ];

    /**
     * Almacena un nuevo mensaje de contacto en la base de datos.
     * Los valores se guardan en formato bruto (ya validados en el
     * controlador) y se utilizan sentencias preparadas para prevenir
     * inyección SQL. La protección contra XSS se aplica en la SALIDA
     * (escapes en las vistas con htmlspecialchars), práctica correcta
     * que evita la doble codificación.
     *
     * @param array $data Datos del formulario (nombre, email, asunto, mensaje, etc.)
     * @return int ID del mensaje insertado
     */
    public function create(array $data): int
    {
        /* Limpieza básica de entradas: recorte de espacios en blanco */
        $full_name   = trim($data['full_name']);
        $email       = trim($data['email']);
        $phone       = isset($data['phone']) && trim($data['phone']) !== ''
                        ? trim($data['phone'])
                        : null;
        $subject     = trim($data['subject']);
        $message     = trim($data['message']);
        $service_int = isset($data['service_interest']) && trim($data['service_interest']) !== ''
                        ? trim($data['service_interest'])
                        : null;

        /* Capturar la IP del remitente para auditoría de seguridad */
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        /* Consulta preparada para inserción segura */
        $sql = "INSERT INTO {$this->table}
                    (full_name, email, phone, subject, message, service_interest, ip_address)
                VALUES
                    (:full_name, :email, :phone, :subject, :message, :service_interest, :ip_address)";

        $stmt = $this->db->prepare($sql);

        /* Vincular parámetros con tipos de datos específicos */
        $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':service_interest', $service_int, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ip, PDO::PARAM_STR);

        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Marca un mensaje de contacto como leído.
     *
     * @param int $id ID del mensaje
     * @return bool True si la operación fue exitosa
     */
    public function markAsRead(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Marca un mensaje de contacto como respondido.
     *
     * @param int $id ID del mensaje
     * @return bool True si la operación fue exitosa
     */
    public function markAsReplied(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_replied = 1, is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cuenta la cantidad de mensajes de contacto no leídos.
     * Se implementa en la tabla para el panel de administración.
     *
     * @return int Cantidad de mensajes sin leer
     */
    public function countUnread(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE is_read = 0";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetch()['total'];
    }
}