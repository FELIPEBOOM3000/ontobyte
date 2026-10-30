-- ============================================================================
-- ARCHIVO: database/schema.sql
-- ============================================================================
-- Propósito: Script DDL (Data Definition Language) para la creación de la
--            base de datos relacional MySQL del sistema OntoByte.
--            Define todas las tablas, relaciones, índices y restricciones
--            necesarias para el funcionamiento completo del sistema.
-- ============================================================================

-- --------------------------------------------------------------------------
-- CREACIÓN DE LA BASE DE DATOS
-- Se utiliza IF NOT EXISTS para evitar errores si ya existe.
-- --------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS ontobyte_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos para operaciones posteriores
USE ontobyte_db;

-- ============================================================================
-- TABLA: users
-- Almacena los usuarios registrados del sistema con sus credenciales
-- y datos personales. Implementa autenticación por email/contraseña
-- y por Google OAuth 2.0 (campo google_id con índice único).
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,  /* Identificador único autoincremental */
    first_name      VARCHAR(100)        NOT NULL,                 /* Nombre del usuario */
    last_name       VARCHAR(100)        NOT NULL,                 /* Apellido del usuario */
    email           VARCHAR(255)        NOT NULL,                 /* Email único (usado para login) */
    password        VARCHAR(255)        NOT NULL,                 /* Contraseña hasheada con PASSWORD_BCRYPT */
    google_id       VARCHAR(255)        DEFAULT NULL,             /* Identificador único 'sub' del usuario en Google OAuth 2.0 */
    role            ENUM('admin', 'user') NOT NULL DEFAULT 'user',/* Rol del usuario para control de acceso */
    phone           VARCHAR(20)         DEFAULT NULL,             /* Número telefónico (opcional) */
    avatar          VARCHAR(500)        DEFAULT NULL,             /* Ruta al archivo de foto de perfil */
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,       /* Estado de la cuenta (1=activo, 0=inactivo) */
    login_attempts  TINYINT UNSIGNED    NOT NULL DEFAULT 0,       /* Contador de intentos fallidos de login */
    email_verified  TINYINT(1)          NOT NULL DEFAULT 0,       /* Verificación de email (Google lo confirma) */
    last_login      DATETIME            DEFAULT NULL,             /* Fecha y hora del último login exitoso */
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Fecha de creación del registro */
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, /* Última actualización */

    /* Restricciones de la tabla */
    PRIMARY KEY (id),                                             /* Clave primaria */
    UNIQUE KEY uk_users_email (email),                            /* Email único para login */
    UNIQUE KEY uk_users_google (google_id),                       /* google_id único (evita duplicados de OAuth) */
    INDEX idx_users_role (role),                                  /* Índice para búsquedas por rol */
    INDEX idx_users_active (is_active)                            /* Índice para filtrar usuarios activos */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabla de usuarios registrados del sistema';

-- ============================================================================
-- TABLA: mensajes_contacto
-- Almacena los mensajes enviados desde el formulario de contacto del sitio
-- web corporativo. Permite a los usuarios potenciales comunicarse con la
-- empresa OntoByte.
-- ============================================================================
CREATE TABLE IF NOT EXISTS mensajes_contacto (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,  /* Identificador único del mensaje */
    full_name       VARCHAR(200)        NOT NULL,                 /* Nombre completo del remitente */
    email           VARCHAR(255)        NOT NULL,                 /* Email del remitente para respuesta */
    phone           VARCHAR(20)         DEFAULT NULL,             /* Teléfono del remitente (opcional) */
    subject         VARCHAR(255)        NOT NULL,                 /* Asunto del mensaje */
    message         TEXT                NOT NULL,                 /* Cuerpo del mensaje (texto largo) */
    service_interest VARCHAR(100)        DEFAULT NULL,             /* Servicio de interés (select del formulario) */
    is_read         TINYINT(1)          NOT NULL DEFAULT 0,       /* Estado de lectura (0=no leído, 1=leído) */
    is_replied      TINYINT(1)          NOT NULL DEFAULT 0,       /* Estado de respuesta (0=pendiente, 1=respondido) */
    ip_address      VARCHAR(45)         DEFAULT NULL,             /* Dirección IP del remitente (para auditoría) */
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Fecha de recepción del mensaje */

    /* Restricciones de la tabla */
    PRIMARY KEY (id),                                             /* Clave primaria */
    INDEX idx_mensajes_read (is_read),                            /* Índice para filtrar mensajes no leídos */
    INDEX idx_mensajes_email (email),                             /* Índice para buscar por remitente */
    INDEX idx_mensajes_created (created_at)                       /* Índice para ordenar por fecha */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Mensajes del formulario de contacto corporativo';

-- ============================================================================
-- TABLA: services
-- Catálogo de servicios que ofrece la empresa OntoByte.
-- Cada servicio tiene una descripción, ícono y orden de visualización.
-- ============================================================================
CREATE TABLE IF NOT EXISTS services (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,  /* Identificador único del servicio */
    title           VARCHAR(200)        NOT NULL,                 /* Título del servicio */
    slug            VARCHAR(200)        NOT NULL,                 /* Slug URL-friendly del servicio */
    description     TEXT                NOT NULL,                 /* Descripción detallada del servicio */
    short_desc      VARCHAR(500)        DEFAULT NULL,             /* Descripción corta para tarjetas */
    icon_class      VARCHAR(100)        DEFAULT NULL,             /* Clase CSS del ícono (ej: fas fa-code) */
    image           VARCHAR(500)        DEFAULT NULL,             /* Ruta de imagen representativa */
    sort_order      INT UNSIGNED        NOT NULL DEFAULT 0,       /* Orden de visualización */
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,       /* Estado (1=activo, 0=inactivo) */
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Fecha de creación */
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, /* Última actualización */

    /* Restricciones de la tabla */
    PRIMARY KEY (id),                                             /* Clave primaria */
    UNIQUE KEY uk_services_slug (slug),                           /* Slug único para URLs amigables */
    INDEX idx_services_active (is_active),                        /* Índice para filtrar activos */
    INDEX idx_services_sort (sort_order)                          /* Índice para ordenamiento */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de servicios ofrecidos por OntoByte';

-- ============================================================================
-- TABLA: team_members
-- Información de los miembros del equipo de OntoByte.
-- Se muestra en la sección "Nuestro Equipo" de la página principal.
-- ============================================================================
CREATE TABLE IF NOT EXISTS team_members (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,  /* Identificador único del miembro */
    full_name       VARCHAR(200)        NOT NULL,                 /* Nombre completo */
    role            VARCHAR(150)        NOT NULL,                 /* Cargo o rol dentro de la empresa */
    bio             TEXT                DEFAULT NULL,              /* Biografía breve del miembro */
    photo           VARCHAR(500)        DEFAULT NULL,             /* Ruta de la foto del miembro */
    email           VARCHAR(255)        DEFAULT NULL,             /* Email de contacto del miembro */
    linkedin_url    VARCHAR(500)        DEFAULT NULL,             /* URL del perfil de LinkedIn */
    github_url      VARCHAR(500)        DEFAULT NULL,             /* URL del perfil de GitHub */
    sort_order      INT UNSIGNED        NOT NULL DEFAULT 0,       /* Orden de visualización */
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,       /* Estado (1=activo, 0=inactivo) */
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Fecha de creación */
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, /* Última actualización */

    /* Restricciones de la tabla */
    PRIMARY KEY (id),                                             /* Clave primaria */
    INDEX idx_team_active (is_active),                            /* Índice para filtrar activos */
    INDEX idx_team_sort (sort_order)                              /* Índice para ordenamiento */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Miembros del equipo de OntoByte';

-- ============================================================================
-- TABLA: testimonials
-- Testimonios de clientes satisfechos con los servicios de OntoByte.
-- Se muestra en la sección de testimonios de la página principal.
-- ============================================================================
CREATE TABLE IF NOT EXISTS testimonials (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,  /* Identificador único del testimonio */
    client_name     VARCHAR(200)        NOT NULL,                 /* Nombre del cliente */
    client_role     VARCHAR(150)        DEFAULT NULL,             /* Cargo o empresa del cliente */
    content         TEXT                NOT NULL,                 /* Texto del testimonio */
    rating          TINYINT UNSIGNED    DEFAULT NULL,             /* Calificación del 1 al 5 (estrellas) */
    photo           VARCHAR(500)        DEFAULT NULL,             /* Foto del cliente (opcional) */
    sort_order      INT UNSIGNED        NOT NULL DEFAULT 0,       /* Orden de visualización */
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,       /* Estado (1=activo, 0=inactivo) */
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Fecha de creación */

    /* Restricciones de la tabla */
    PRIMARY KEY (id),                                             /* Clave primaria */
    INDEX idx_testimonials_active (is_active),                    /* Índice para filtrar activos */
    INDEX idx_testimonials_sort (sort_order)                      /* Índice para ordenamiento */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Testimonios de clientes de OntoByte';

-- ============================================================================
-- DATOS INICIALES (SEED DATA)
-- Inserta datos predeterminados para el funcionamiento inicial del sistema.
-- ============================================================================

-- Usuario administrador por defecto
-- Contraseña: Admin123* (hasheada con PASSWORD_BCRYPT / cost 10)
-- IMPORTANTE: Cambiar esta contraseña en el primer acceso.
-- Sobre $2b$: PHP password_verify() acepta hashes con prefijo $2y$, $2a$ y $2b$.
INSERT INTO users (first_name, last_name, email, password, role, is_active) VALUES
('Administrador', 'General', 'admin@ontobyte.com', '$2y$10$1RVV.DSoTvJk/iN7T1KyNubhp4gxcabwqKCR6EG5r5TtD7DlVGE82', 'admin', 1);

-- Servicios predeterminados de OntoByte
-- Los tres primeros son los "Servicios con propósito" destacados en el sitio.
INSERT INTO services (title, slug, description, short_desc, icon_class, sort_order) VALUES
('Diseño Web Creativo', 'diseno-web-creativo', 'Diseñamos interfaces modernas, elegantes y funcionales que reflejan la esencia de cada marca. Cada pixel está pensado para cautivar y comunicar con propósito.', 'Interfaces modernas, elegantes y funcionales que reflejan la esencia de cada marca.', 'fas fa-palette', 1),
('Desarrollo Web', 'desarrollo-web', 'Creamos soluciones tecnológicas concretas, eficientes y escalables construidas byte a byte. Arquitectura limpia, código seguro y rendimiento excepcional.', 'Soluciones tecnológicas concretas, eficientes y escalables construidas byte a byte.', 'fas fa-code', 2),
('Acompañamiento Total', 'acompanamiento-total', 'Desde la primera idea hasta la entrega final, acompañamos tu proyecto de principio a fin. Nos dedicamos al bienestar y la educación de cada iniciativa digital.', 'Desde la primera idea hasta la entrega final, dedicados al bienestar y educación de tu proyecto.', 'fas fa-handshake', 3),
('Desarrollo Móvil', 'desarrollo-movil', 'Diseñamos y desarrollamos aplicaciones móviles nativas e híbridas para iOS y Android, ofreciendo experiencias de usuario excepcionales y alto rendimiento.', 'Apps nativas e híbridas para iOS y Android.', 'fas fa-mobile-alt', 4),
('Inteligencia Artificial', 'inteligencia-artificial', 'Implementamos soluciones de IA y machine learning que transforman datos en insights accionables, automatizando procesos y optimizando la toma de decisiones.', 'Soluciones de IA y machine learning empresariales.', 'fas fa-brain', 5),
('Ciberseguridad', 'ciberseguridad', 'Protegemos tus activos digitales con estrategias avanzadas de seguridad, auditorías de vulnerabilidad y cumplimiento de normativas internacionales.', 'Protección avanzada de activos digitales.', 'fas fa-shield-alt', 6);

-- Miembros del equipo: dos años de formación SENA, futuros líderes visionarios
INSERT INTO team_members (full_name, role, bio, sort_order) VALUES
('Andres Diaz', 'Fundador & Desarrollador', 'Aprendiz SENA con dos años de formación en desarrollo de software. Futuro líder visionario, apasionado por crear soluciones web con propósito.', 1),
('Samuel Sanchez', 'Co-fundador & Desarrollador', 'Dos años de formación SENA marcando el camino hacia ser un líder visionario, enfocado en la innovación y en construir tecnología con impacto.', 2);

-- Testimonios de ejemplo
INSERT INTO testimonials (client_name, client_role, content, rating, sort_order) VALUES
('María Fernández', 'CEO, TechStart', 'OntoByte transformó nuestra visión en una plataforma digital increíble. Su equipo es profesional, innovador y siempre entrega antes del plazo.', 5, 1),
('Roberto Sánchez', 'CTO, DataFlow', 'La implementación de IA que realizaron para nuestro proceso de producción aumentó nuestra eficiencia en un 40%. Resultados extraordinarios.', 5, 2),
('Diana Torres', 'Directora de Marketing, BrandUp', 'El nuevo sitio web que diseñaron superó todas nuestras expectativas. Nuestras conversiones aumentaron un 60% en el primer trimestre.', 5, 3);
