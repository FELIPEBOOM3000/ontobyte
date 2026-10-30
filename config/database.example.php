<?php
/**
 * ============================================================================
 * ARCHIVO: config/database.php
 * ============================================================================
 * Propósito: Establecer la conexión a la base de datos MySQL utilizando
 *            PHP Data Objects (PDO) con manejo robusto de excepciones.
 *            Implementa el patrón Singleton para una única instancia de
 *            conexión a lo largo de toda la aplicación.
 *            Utiliza sentencias preparadas para prevenir inyección SQL.
 * ============================================================================
 */

/* --------------------------------------------------------------------------
 * CONSTANTES DE CONEXIÓN A BASE DE DATOS
 * Credenciales y parámetros para la conexión PDO.
 * En producción, estos valores deben provenir de variables de entorno.
 * -------------------------------------------------------------------------- */
define('DB_HOST', '127.0.0.1');                               /* Dirección del servidor MySQL */
define('DB_PORT', '3306');                                     /* Puerto del servidor MySQL */
define('DB_NAME', 'ontobyte_db');                              /* Nombre de la base de datos */
define('DB_CHARSET', 'utf8mb4');                               /* Codificación de caracteres (soporte Unicode completo) */
define('DB_USER', 'root');                                     /* Usuario de la base de datos */
define('DB_PASS', '');                                         /* Contraseña de la base de datos */

/* --------------------------------------------------------------------------
 * CLASE Database
 * Implementa el patrón Singleton para gestionar la conexión PDO.
 * Solo se permite una única instancia de conexión a la base de datos.
 * -------------------------------------------------------------------------- */
class Database
{
    /** @var Database|null Instancia única de la clase (Singleton) */
    private static ?Database $instance = null;

    /** @var PDO|null Objeto de conexión PDO a la base de datos */
    private ?PDO $connection = null;

    /**
     * Constructor privado.
     * Evita la instanciación externa directa (patrón Singleton).
     * Establece la conexión PDO con manejo de errores en modo excepción.
     *
     * @throws Exception Si falla la conexión a la base de datos
     */
    private function __construct()
    {
        /* Construir el DSN (Data Source Name) para la conexión PDO */
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        /* Opciones de configuración PDO para seguridad y rendimiento */
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    /* Lanzar excepciones en errores */
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          /* Fetch asociativo por defecto */
            PDO::ATTR_EMULATE_PREPARES   => false,                     /* Usar prepared statements nativos */
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET  /* Forzar codificación de caracteres */
        ];

        try {
            /* Crear la conexión PDO con las credenciales y opciones */
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            /* En desarrollo: mostrar error detallado */
            if (APP_DEBUG) {
                throw new Exception(
                    'Error de conexión a la base de datos: ' . $e->getMessage()
                );
            }
            /* En producción: mostrar mensaje genérico por seguridad */
            throw new Exception('Error de conexión a la base de datos.');
        }
    }

    /**
     * Método estático para obtener la instancia Singleton de la conexión.
     * Si no existe una instancia, la crea; si ya existe, la reutiliza.
     *
     * @return Database Instancia única de la clase Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene el objeto PDO de conexión a la base de datos.
     * Se utiliza para ejecutar consultas preparadas en los modelos.
     *
     * @return PDO Objeto de conexión PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /* Evitar la clonación y deserialización de la instancia Singleton */
    private function __clone() {}
    public function __wakeup()
    {
        throw new Exception('No se permite la deserialización de singleton.');
    }
}
