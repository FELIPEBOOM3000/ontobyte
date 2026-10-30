<?php
/**
 * ============================================================================
 * ARCHIVO: app/controllers/AuthController.php
 * ============================================================================
 * Propósito: Controlador de autenticación. Gestiona el inicio de sesión,
 *            registro de nuevos usuarios, cierre de sesión, el dashboard
 *            de bienvenida (protegido por sesión) y el flujo completo de
 *            Google OAuth 2.0 (verificación criptográfica del token ID).
 *            Implementa todas las medidas de seguridad requeridas:
 *            validación de entradas, tokens CSRF, encriptación de
 *            contraseñas con bcrypt y control de intentos fallidos.
 * ============================================================================
 */

require_once CONTROLLERS_PATH . '/Controller.php';

class AuthController extends Controller
{
    /** @var User Instancia del modelo de usuario */
    private $userModel;

    /**
     * Constructor del controlador de autenticación.
     * Inicializa el modelo de usuario para las operaciones de datos.
     */
    public function __construct()
    {
        require_once MODELS_PATH . '/User.php';
        $this->userModel = new User();
    }

    /**
     * Muestra el formulario de inicio de sesión.
     * Si el usuario ya está autenticado, se redirige al inicio.
     *
     * @return void
     */
    public function login(): void
    {
        /* Si ya está autenticado, no mostrar el formulario de nuevo */
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        /* Generar token CSRF para el formulario */
        $csrfToken = $this->generateCsrfToken();

        /* Renderizar la vista de login */
        $this->render('auth/login', [
            'csrfToken' => $csrfToken,
            'activePage' => 'login'
        ]);
    }

    /**
     * Procesa los datos del formulario de inicio de sesión.
     * Valida el token CSRF, verifica las credenciales y establece
     * la sesión segura del usuario.
     *
     * @return void
     */
    public function authenticate(): void
    {
        /* Tomar los datos: primero verificar token CSRF (POST oculto) */
        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            $this->redirect('/login');
        }

        /* Sanitizar y validar las entradas del formulario */
        $email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        /* Validar que los campos no estén vacíos */
        if (!$email || empty($password)) {
            $this->setFlash('error', 'Por favor ingresa un email válido y tu contraseña.');
            $this->redirect('/login');
        }

        /* Intentar autenticar al usuario con las credenciales */
        $user = $this->userModel->authenticate($email, $password);

        /* Si la autenticación fue exitosa, iniciar sesión */
        if ($user) {
            /* Establecer la sesión segura del usuario */
            $this->userModel->startUserSession($user);

            $this->setFlash('success', 'Bienvenido de nuevo, ' . $_SESSION['user_name'] . '!');

            /* Redirigir al panel de administración si es admin, si no al panel de bienvenida */
            if ($user['role'] === 'admin') {
                $this->redirect('/admin');
            }
            $this->redirect('/dashboard');
        }

        /* Autenticación fallida: el modelo ya incrementó los intentos fallidos */
        $this->setFlash('error', 'Credenciales incorrectas. Por favor verifica tus datos.');
        $this->redirect('/login');
    }

    /**
     * Muestra el formulario de registro de nuevos usuarios.
     *
     * @return void
     */
    public function register(): void
    {
        /* Si ya está autenticado, no mostrar registro */
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        /* Generar token CSRF para el formulario */
        $csrfToken = $this->generateCsrfToken();

        /* Renderizar la vista de registro */
        $this->render('auth/register', [
            'csrfToken' => $csrfToken,
            'activePage' => 'register'
        ]);
    }

    /**
     * Procesa el registro de un nuevo usuario en el sistema.
     * Implementa validación estricta de entrada y encriptación
     * de contraseña con PASSWORD_BCRYPT.
     *
     * @return void
     */
    public function store(): void
    {
        /* Validar el token CSRF para prevenir ataques CSRF */
        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            $this->redirect('/register');
        }

        /* Sanitizar y validar cada campo del formulario */
        $firstName = $this->sanitize($_POST['first_name'] ?? '');
        $lastName  = $this->sanitize($_POST['last_name'] ?? '');
        $email     = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone     = isset($_POST['phone']) ? $this->sanitize($_POST['phone']) : '';
        $password  = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        /* --- Validaciones estrictas de cada campo --- */

        /* Validar nombres (letras, espacios, mínimo 2 caracteres) */
        if (strlen($firstName) < 2 || !preg_match('/^[a-zA-ZáéíóúñÑüÜ\s]+$/', $firstName)) {
            $this->setFlash('error', 'El nombre debe contener solo letras y tener al menos 2 caracteres.');
            $this->redirect('/register');
        }

        if (strlen($lastName) < 2 || !preg_match('/^[a-zA-ZáéíóúñÑüÜ\s]+$/', $lastName)) {
            $this->setFlash('error', 'El apellido debe contener solo letras y tener al menos 2 caracteres.');
            $this->redirect('/register');
        }

        /* Validar email */
        if (!$email) {
            $this->setFlash('error', 'Por favor ingresa un email válido.');
            $this->redirect('/register');
        }

        /* Validar longitud mínima de contraseña */
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $this->setFlash('error', 'La contraseña debe tener al menos ' . PASSWORD_MIN_LENGTH . ' caracteres.');
            $this->redirect('/register');
        }

        /* Validar que la contraseña tenga al menos una letra mayúscula, una minúscula y un número */
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $this->setFlash('error', 'La contraseña debe incluir mayúsculas, minúsculas y números.');
            $this->redirect('/register');
        }

        /* Validar que las contraseñas coincidan */
        if ($password !== $passwordConfirm) {
            $this->setFlash('error', 'Las contraseñas no coinciden.');
            $this->redirect('/register');
        }

        /* Verificar que el email no esté registrado previamente */
        if ($this->userModel->findByEmail($email)) {
            $this->setFlash('error', 'Este email ya está registrado. Intenta iniciar sesión.');
            $this->redirect('/register');
        }

        /* Crear usuario con contraseña encriptada mediante bcrypt */
        $userId = $this->userModel->create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'phone'      => $phone,
            'password'   => $password
        ]);

        /* Registrar la sesión de forma automática tras el registro exitoso */
        if ($userId) {
            $user = $this->userModel->find($userId);
            $this->userModel->startUserSession($user);

            $this->setFlash('success', 'Cuenta creada exitosamente. Bienvenido a OntoByte!');
            $this->redirect('/dashboard');
        }

        $this->setFlash('error', 'No fue posible crear tu cuenta. Intenta de nuevo más tarde.');
        $this->redirect('/register');
    }

    /**
     * Cierra la sesión del usuario autenticado.
     * Destruye la sesión y redirige al inicio con un mensaje de despedida.
     * Se inicia una sesión limpia después de destruir la anterior para
     * poder almacenar el mensaje flash sin reintroducir datos del usuario.
     *
     * @return void
     */
    public function logout(): void
    {
        /* Destruir la sesión del usuario (libera datos, cookies y archivos) */
        $this->userModel->logout();

        /* Iniciar una sesión totalmente limpia para el mensaje de despedida */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            session_regenerate_id(true);
        }

        /* Redirigir al inicio con mensaje informativo */
        $this->setFlash('info', 'Has cerrado sesión correctamente.');
        $this->redirect('/');
    }

    /**
     * Panel/Dashboard de bienvenida del usuario autenticado.
     * Ruta protegida por sesión: solo usuarios con sesión iniciada pueden
     * acceder. Muestra el nombre, correo y rol del usuario, así como las
     * opciones de cerrar sesión y (si aplica) acceder al panel admin.
     *
     * @return void
     */
    public function dashboard(): void
    {
        /* Proteger la ruta: redirige al login si no hay sesión activa */
        $this->requireAuth();

        /* Generar token CSRF (disponible si se añaden formularios al panel) */
        $csrfToken = $this->generateCsrfToken();

        /* Consultar los datos ampliados del miembro en la base de datos
           (fecha de registro, último acceso y estado de la cuenta) */
        require_once MODELS_PATH . '/User.php';
        $member = (new User())->find((int) ($_SESSION['user_id'] ?? 0));

        /* Nombre a mostrar: el de la base de datos o, en su defecto, el de sesión */
        $displayName = 'Usuario';
        if ($member) {
            $displayName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
        }
        if ($displayName === '') {
            $displayName = $_SESSION['user_name'] ?? 'Usuario';
        }

        /* Renderizar la zona de miembros con los datos completos */
        $this->render('auth/dashboard', [
            'user'       => [
                'name'       => $displayName,
                'email'      => $member['email']      ?? ($_SESSION['user_email'] ?? ''),
                'role'       => $member['role']       ?? ($_SESSION['user_role'] ?? 'user'),
                'created_at' => $member['created_at'] ?? null,
                'last_login' => $member['last_login'] ?? null,
                'is_active'  => $member['is_active']  ?? 1
            ],
            'csrfToken'  => $csrfToken,
            'activePage' => 'dashboard'
        ]);
    }

    /**
     * Callback del flujo de Google OAuth 2.0 (Google Identity Services).
     * Recibe el token ID (JWT) enviado por el frontend tras el inicio de
     * sesión con Google, verifica criptográficamente la identidad de la
     * cuenta y, según el resultado:
     *   1. Si el google_id ya existe  -> inicia sesión directamente.
     *   2. Si el email ya está registrado con contraseña -> vincula el
     *      google_id y inicia sesión.
     *   3. Si es una cuenta nueva -> la crea y inicia sesión.
     *
     * @return void
     */
    public function googleCallback(): void
    {
        /* PASO 1: Validar token CSRF (protección contra peticiones falsificadas) */
        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            $this->redirect('/login');
        }

        /* PASO 2: Verificar que se recibió la credencial y hay Client ID configurado */
        $credential = $_POST['credential'] ?? '';
        if (empty($credential) || empty(GOOGLE_CLIENT_ID)) {
            $this->setFlash('error', 'Credencial de Google no válida. Configura GOOGLE_CLIENT_ID en config/config.php.');
            $this->redirect('/login');
        }

        /* PASO 3: Verificar criptográficamente el token ID de Google (JWT RS256) */
        $claims = $this->verifyGoogleIdToken($credential, GOOGLE_CLIENT_ID);

        if ($claims === null) {
            $this->setFlash('error', 'No fue posible verificar tu identidad con Google. Intenta de nuevo.');
            $this->redirect('/login');
        }

        /* PASO 4: Extraer los datos verificados del token */
        $googleId = (string) $claims['sub'];
        $email    = strtolower(trim($claims['email'] ?? ''));
        $fullName = trim($claims['name'] ?? ($claims['given_name'] ?? '') . ' ' . ($claims['family_name'] ?? ''));

        /* Separar el nombre completo en nombre y apellido (con respaldo) */
        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0] ?? 'Usuario';
        $lastName  = $nameParts[1] ?? 'Google';

        if ($email === '') {
            $this->setFlash('error', 'Tu cuenta de Google no proporciona un correo válido.');
            $this->redirect('/login');
        }

        /* PASO 5: Buscar al usuario en la base de datos (flujo de 3 vías) */
        $user = $this->userModel->findByGoogleId($googleId);

        if (!$user) {
            /* Si el email ya existe en el sistema, vincular el google_id */
            $existing = $this->userModel->findByEmail($email);
            if ($existing) {
                $this->userModel->linkGoogleId((int) $existing['id'], $googleId);
                $user = $existing;
            } else {
                /* Usuario completamente nuevo: crearlo con sus datos de Google */
                $userId = $this->userModel->createWithGoogle([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'google_id'  => $googleId
                ]);

                if (!$userId) {
                    $this->setFlash('error', 'No fue posible crear tu cuenta con Google.');
                    $this->redirect('/login');
                }
                $user = $this->userModel->find($userId);
            }
        }

        /* PASO 6: Verificar que la cuenta esté activa antes de iniciar sesión */
        if (!$user || (int) $user['is_active'] !== 1) {
            $this->setFlash('error', 'Tu cuenta está desactivada. Contacta con el administrador.');
            $this->redirect('/login');
        }

        /* PASO 7: Iniciar sesión segura y redirigir según el rol */
        $this->userModel->startUserSession($user);
        $this->userModel->updateLastLogin((int) $user['id']);

        $this->setFlash('success', 'Bienvenido a OntoByte, ' . $_SESSION['user_name'] . '!');

        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        }
        $this->redirect('/dashboard');
    }

    /**
     * Verifica un token ID (JWT) emitido por Google.
     * El proceso es estrictamente criptográfico:
     *   1. Divide el JWT en sus tres partes (header, payload, firma).
     *   2. Valida las reclamaciones (claims): emisor, audiencia y expiración.
     *   3. Descarga los certificados públicos de Google y localiza el
     *      que coincide con el 'kid' del header del token.
     *   4. Reconstruye la clave pública RSA en formato PEM a partir de
     *      los componentes del certificado (modulus y exponent).
     *   5. Verifica la firma RS256 con openssl_verify().
     *
     * @param string $jwt      Token ID proporcionado por Google
     * @param string $clientId Client ID de la aplicación (audiencia esperada)
     * @return array|null Claims verificados del token o null si no es válido
     */
    private function verifyGoogleIdToken(string $jwt, string $clientId): ?array
    {
        /* Descomponer el JWT en header.payload.firma */
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        [$headerB64, $payloadB64, $signatureB64] = $parts;

        /* Decodificar cada componente desde Base64url */
        $header    = json_decode(self::base64UrlDecode($headerB64), true);
        $claims    = json_decode(self::base64UrlDecode($payloadB64), true);
        $signature = self::base64UrlDecode($signatureB64);

        if (!is_array($header) || !is_array($claims)) {
            return null;
        }

        /* El algoritmo de firma de Google siempre es RS256 */
        if (($header['alg'] ?? '') !== 'RS256') {
            return null;
        }

        /* --- Validación de reclamaciones (claims) --- */

        /* Emisor: solo se aceptan tokens emitidos por Google */
        $validIssuers = ['accounts.google.com', 'https://accounts.google.com'];
        if (!in_array($claims['iss'] ?? '', $validIssuers, true)) {
            return null;
        }

        /* Audiencia: debe ser el Client ID registrado de la aplicación */
        if (($claims['aud'] ?? '') !== $clientId) {
            return null;
        }

        /* Expiración: el token no debe haber expirado */
        if (($claims['exp'] ?? 0) < time()) {
            return null;
        }

        /* Debe contener el identificador del usuario y su email */
        if (empty($claims['sub']) || empty($claims['email'])) {
            return null;
        }

        /* --- Verificación criptográfica de la firma --- */

        /* Obtener los certificados públicos vigentes de Google */
        $certs = $this->fetchGoogleCerts();
        if (!$certs || empty($certs['keys'])) {
            return null;
        }

        /* Localizar el certificado cuyo 'kid' coincide con el del token */
        $kid    = $header['kid'] ?? null;
        $jwkKey = null;
        foreach ($certs['keys'] as $candidate) {
            if (($candidate['kid'] ?? '') === $kid) {
                $jwkKey = $candidate;
                break;
            }
        }
        if ($jwkKey === null || empty($jwkKey['n']) || empty($jwkKey['e'])) {
            return null;
        }

        /* Construir la clave pública RSA en formato PEM desde el JWK */
        $publicKeyPem = self::buildPublicKeyPem(
            self::base64UrlDecode($jwkKey['n']),
            self::base64UrlDecode($jwkKey['e'])
        );

        /* Verificar la firma del token con la clave pública (SHA-256) */
        $data = $headerB64 . '.' . $payloadB64;
        $ok = openssl_verify($data, $signature, $publicKeyPem, OPENSSL_ALGO_SHA256);

        if ($ok !== 1) {
            return null;
        }

        /* Token auténtico: devolver los claims verificados */
        return $claims;
    }

    /**
     * Descarga el conjunto de certificados públicos de firma de Google.
     * Google publica sus claves en https://www.googleapis.com/oauth2/v3/certs.
     * El endpoint devuelve JSON con un listado de claves JWK (kid, n, e).
     * Se prefiere cURL cuando está disponible; si no, se usa file_get_contents.
     *
     * @return array|null Array con las claves ('keys') o null si falla
     */
    private function fetchGoogleCerts(): ?array
    {
        $url = GOOGLE_CERTS_URL;
        $response = null;

        if (function_exists('curl_init')) {
            /* Método preferido: cURL con verificación de TLS */
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,   /* Devolver la respuesta como string */
                CURLOPT_SSL_VERIFYPEER => true,   /* Verificar el certificado SSL */
                CURLOPT_TIMEOUT        => 15,     /* Tiempo máximo de espera (segundos) */
                CURLOPT_CONNECTTIMEOUT => 10
            ]);
            $raw = curl_exec($ch);
            if (curl_errno($ch) === 0) {
                $response = $raw;
            }
            curl_close($ch);
        } else {
            /* Fallback: file_get_contents con contexto HTTP y timeout */
            $context = stream_context_create(['http' => ['timeout' => 15, 'method' => 'GET']]);
            $raw = @file_get_contents($url, false, $context);
            if ($raw !== false) {
                $response = $raw;
            }
        }

        if ($response === null) {
            return null;
        }

        $certs = json_decode($response, true);
        return is_array($certs) ? $certs : null;
    }

    /**
     * Decodifica una cadena Base64url (RFC 4648) a datos binarios.
     * Variante de Base64 que usa '-' y '_' en lugar de '+' y '/'.
     *
     * @param string $data Cadena codificada en Base64url
     * @return string Datos binarios decodificados
     */
    private static function base64UrlDecode(string $data): string
    {
        /* Reemplazar caracteres de la variante URL y rellenar con '=' */
        $b64 = strtr($data, '-_', '+/');
        $b64 .= str_repeat('=', (4 - strlen($b64) % 4) % 4);
        return base64_decode($b64, true) ?: '';
    }

    /**
     * Construye una clave pública RSA en formato PEM (SubjectPublicKeyInfo)
     * a partir de los componentes del JWK de Google: modulus (n) y exponent
     * (e). Implementa la codificación DER/SER según la especificación ASN.1
     * para la estructura SubjectPublicKeyInfo definida en RFC 5280.
     *
     * @param string $modulus  Bytes del módulo RSA (n)
     * @param string $exponent Bytes del exponente público RSA (e)
     * @return string Clave pública PEM lista para openssl_verify()
     */
    private static function buildPublicKeyPem(string $modulus, string $exponent): string
    {
        /* RSAPublicKey ::= SEQUENCE { modulus INTEGER, publicExponent INTEGER } */
        $rsaPublicKey = self::derEncodeSequence(
            self::derEncodeInteger($modulus),
            self::derEncodeInteger($exponent)
        );

        /* AlgorithmIdentifier ::= SEQUENCE { algorithm OID, parameters NULL } */
        /* OID rsaEncryption: 1.2.840.113549.1.1.1 */
        $algorithmIdentifier = self::derEncodeSequence(
            "\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01",
            "\x05\x00"
        );

        /* SubjectPublicKeyInfo ::= SEQUENCE { algorithm, subjectPublicKey BIT STRING } */
        $spki = self::derEncodeSequence(
            $algorithmIdentifier,
            self::derEncodeBitString($rsaPublicKey)
        );

        /* Envolver en el formato PEM estándar con saltos de 64 caracteres */
        return "-----BEGIN PUBLIC KEY-----\n"
             . chunk_split(base64_encode($spki), 64, "\n")
             . "-----END PUBLIC KEY-----\n";
    }

    /**
     * Codifica un valor como INTEGER DER (etiqueta 0x02).
     * Añade un byte 0x00 al inicio si el bit más significativo está
     * activo para garantizar que el entero se interprete como positivo.
     *
     * @param string $bytes Bytes del entero
     * @return string TLV DER del INTEGER
     */
    private static function derEncodeInteger(string $bytes): string
    {
        if ($bytes === '' || (ord($bytes[0]) & 0x80) !== 0) {
            $bytes = "\x00" . $bytes;
        }
        return "\x02" . self::derEncodeLength(strlen($bytes)) . $bytes;
    }

    /**
     * Codifica un valor como BIT STRING DER (etiqueta 0x03) con el
     * byte de "bits no usados" (0x00) antes del contenido.
     *
     * @param string $bytes Bytes del bit string
     * @return string TLV DER del BIT STRING
     */
    private static function derEncodeBitString(string $bytes): string
    {
        return "\x03" . self::derEncodeLength(strlen($bytes) + 1) . "\x00" . $bytes;
    }

    /**
     * Codifica una SEQUENCE DER (etiqueta 0x30), es decir, la concatena-
     * ción ordenada de una serie de elementos TLV con su longitud total.
     *
     * @param string ...$parts Elementos TLV a encapsular
     * @return string TLV DER de la SEQUENCE
     */
    private static function derEncodeSequence(string ...$parts): string
    {
        $body = implode('', $parts);
        return "\x30" . self::derEncodeLength(strlen($body)) . $body;
    }

    /**
     * Codifica una longitud en formato DER (definitive short/long form).
     * Para longitudes menores a 128 usa la forma corta; para mayores,
     * usa la forma larga con el byte de control 0x80 | númeroDeBytes.
     *
     * @param int $length Longitud del contenido TLV
     * @return string Longitud codificada en DER
     */
    private static function derEncodeLength(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        $bytes = '';
        while ($length > 0) {
            $bytes = chr($length & 0xFF) . $bytes;
            $length >>= 8;
        }

        return chr(0x80 | strlen($bytes)) . $bytes;
    }
}