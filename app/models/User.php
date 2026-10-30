<?php
/**
 * ============================================================================
 * ARCHIVO: app/models/User.php
 * ============================================================================
 * Propósito: Modelo de la entidad "Usuario". Gestiona todas las operaciones
 *            relacionadas con la tabla `users` de la base de datos.
 *            Utiliza sentencias preparadas (PDO) para operar de forma segura
 *            contra inyección SQL. Implementa lógica de autenticación,
 *            registro, encriptación de contraseñas y control de intentos.
 * ============================================================================
 */

require_once MODELS_PATH . '/Model.php';

class User extends Model
{
    /** @var string Nombre de la tabla en la base de datos */
    protected $table = 'users';

    /** @var array Campos editables del modelo (protegen contra mass assignment) */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'is_active'
    ];

    /**
     * Busca un usuario en la base de datos por su dirección de email.
     * Método seguro que utiliza sentencias preparadas.
     *
     * @param string $email Email a buscar
     * @return array|null Registro del usuario o null si no existe
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Busca un usuario por su identificador único de Google OAuth 2.0.
     * El claim 'sub' del token ID de Google es el identificador inmutable
     * de la cuenta. Permite reconocer a usuarios que ya iniciaron sesión
     * previamente con su cuenta de Google.
     *
     * @param string $googleId Identificador 'sub' proporcionado por Google
     * @return array|null Registro del usuario o null si no existe
     */
    public function findByGoogleId(string $googleId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE google_id = :google_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':google_id', $googleId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Crea un nuevo usuario procedente del flujo de Google OAuth 2.0.
     * Ya que la identidad fue verificada por Google, se genera una
     * contraseña aleatoria fuerte (irrelevante para el usuario, ya que
     * este iniciará sesión siempre con su cuenta de Google) que se
     * almacena hasheada con PASSWORD_BCRYPT.
     *
     * @param array $data Datos del usuario (nombres, email, google_id, etc.)
     * @return int ID del nuevo usuario insertado
     */
    public function createWithGoogle(array $data): int
    {
        /* Normalizar entradas */
        $first_name = trim($data['first_name']);
        $last_name  = trim($data['last_name']);
        $email      = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $googleId   = trim($data['google_id']);

        /* Generar y hashear una contraseña aleatoria de alta entropía */
        $hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);

        /* Consulta preparada para inserción segura */
        $sql = "INSERT INTO {$this->table}
                    (first_name, last_name, email, password, google_id, role, is_active)
                VALUES (:first_name, :last_name, :email, :password, :google_id, :role, 1)";

        $stmt = $this->db->prepare($sql);

        /* Vincular parámetros con tipos de datos específicos */
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
        $stmt->bindParam(':google_id', $googleId, PDO::PARAM_STR);

        /* El rol de los usuarios de OAuth siempre es 'user' */
        $role = 'user';
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);

        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Vincula el identificador de Google OAuth a una cuenta existente.
     * Se utiliza cuando un correo ya está registrado con contraseña pero
     * el usuario decide iniciar sesión con Google por primera vez: se le
     * asocia su google_id para reconocerlo en futuros accesos.
     *
     * @param int    $userId   ID del usuario en la base de datos
     * @param string $googleId Identificador 'sub' de Google
     * @return bool True si la operación fue exitosa
     */
    public function linkGoogleId(int $userId, string $googleId): bool
    {
        $sql = "UPDATE {$this->table} SET google_id = :google_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':google_id', $googleId, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Crea un nuevo usuario en la base de datos con su contraseña encriptada.
     *
     * Encriptación de contraseñas: Se utiliza password_hash() con el algoritmo
     * PASSWORD_BCRYPT, que genera un hash seguro de 60 caracteres con salt
     * aleatorio automático. Nunca se almacena la contraseña en texto plano.
     * El email se normaliza con FILTER_SANITIZE_EMAIL y la protección XSS
     * se aplica en la salida (escapes en las vistas).
     *
     * @param array $data Datos del usuario (nombres, email, contraseña, etc.)
     * @return int ID del nuevo usuario insertado
     */
    public function create(array $data): int
    {
        /* Limpieza básica y normalización de entradas */
        $first_name = trim($data['first_name']);
        $last_name  = trim($data['last_name']);
        $email      = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $phone      = isset($data['phone']) && trim($data['phone']) !== ''
                        ? trim($data['phone'])
                        : null;

        /* Encriptar la contraseña con el algoritmo bcrypt */
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        /* Consulta preparada para inserción segura */
        $sql = "INSERT INTO {$this->table}
                    (first_name, last_name, email, password, phone, role, is_active)
                VALUES (:first_name, :last_name, :email, :password, :phone, :role, 1)";

        $stmt = $this->db->prepare($sql);

        /* Vincular parámetros con tipos de datos específicos */
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);

        /* Asignar rol por defecto siempre 'user' en el registro público */
        $role = 'user';
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);

        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualiza los datos de un usuario (gestión desde el panel admin).
     * Permite editar nombres, teléfono, rol y estado (activo/inactivo).
     * Si se proporciona una nueva contraseña (no vacía), esta se hashea
     * con bcrypt y se reemplaza la anterior. Usa sentencia preparada.
     *
     * @param int   $id   ID del usuario a actualizar
     * @param array $data Datos a actualizar (nombres, rol, etc.)
     * @return bool True si la operación fue exitosa
     */
    public function update(int $id, array $data): bool
    {
        $first_name = trim($data['first_name']);
        $last_name  = trim($data['last_name']);
        $phone      = isset($data['phone']) && trim($data['phone']) !== ''
                        ? trim($data['phone'])
                        : null;
        $role       = ($data['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
        $isActive   = !empty($data['is_active']) ? 1 : 0;

        /* Construir la consulta base de actualización */
        $sql = "UPDATE {$this->table}
                SET first_name = :first_name,
                    last_name  = :last_name,
                    phone      = :phone,
                    role       = :role,
                    is_active  = :is_active";

        $params = [
            ':first_name' => $first_name,
            ':last_name'  => $last_name,
            ':phone'      => $phone,
            ':role'       => $role,
            ':is_active'  => $isActive
        ];

        /* Si el admin proporciona una contraseña nueva, actualizarla hasheada */
        if (isset($data['password']) && trim($data['password']) !== '') {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $params[':id'] = $id;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        return $stmt->execute();
    }

    /**
     * Verifica las credenciales de un usuario para el inicio de sesión.
     * Implementa validación de contraseña con password_verify() y control
     * de intentos fallidos contra fuerza bruta.
     *
     * @param string $email    Email ingresado
     * @param string $password Contraseña ingresada
     * @return array|false Registro del usuario autenticado o false si falla
     */
    public function authenticate(string $email, string $password)
    {
        /* Buscar el usuario por email */
        $user = $this->findByEmail($email);

        /* Si el usuario no existe, retornar false */
        if (!$user) {
            return false;
        }

        /* Verificar que la cuenta está activa */
        if ((int) $user['is_active'] !== 1) {
            return false;
        }

        /* Control anti fuerza bruta: verificar intentos fallidos */
        if ((int) $user['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            return false;
        }

        /* Verificar la contraseña con password_verify() */
        if (password_verify($password, $user['password'])) {
            /* Credenciales correctas: resetear intentos y actualizar último login */
            $this->resetLoginAttempts($user['id']);
            $this->updateLastLogin($user['id']);
            return $user;
        }

        /* Credenciales incorrectas: incrementar intentos fallidos */
        $this->incrementLoginAttempts($user['id']);
        return false;
    }

    /**
     * Incrementa el contador de intentos fallidos de login.
     * Medida de seguridad contra ataques de fuerza bruta.
     *
     * @param int $userId ID del usuario
     * @return bool True si la operación fue exitosa
     */
    public function incrementLoginAttempts(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET login_attempts = login_attempts + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Restablece el contador de intentos fallidos a cero tras login exitoso.
     *
     * @param int $userId ID del usuario
     * @return bool True si la operación fue exitosa
     */
    public function resetLoginAttempts(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET login_attempts = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Actualiza la fecha y hora del último inicio de sesión exitoso.
     *
     * @param int $userId ID del usuario
     * @return bool True si la operación fue exitosa
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Establece las variables de sesión del usuario autenticado.
     * Crea una sesión segura con regeneración del ID de sesión.
     *
     * @param array $user Registro completo del usuario
     * @return void
     */
    public function startUserSession(array $user): void
    {
        /* Regenerar el ID de sesión para prevenir session fixation */
        session_regenerate_id(true);

        /* Almacenar datos del usuario en la sesión */
        $_SESSION['user_id']       = (int) $user['id'];
        $_SESSION['user_name']     = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['user_role']     = $user['role'];
        $_SESSION['user_logged_in']= true;
        $_SESSION['last_activity'] = time();
    }

    /**
     * Cierra la sesión del usuario actual de forma segura.
     * Elimina todas las variables de sesión y destruye la cookie.
     *
     * @return void
     */
    public function logout(): void
    {
        /* Eliminar todas las variables de sesión */
        $_SESSION = [];

        /* Destruir la cookie de sesión del navegador */
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        /* Destruir la sesión del servidor */
        session_destroy();
    }
}