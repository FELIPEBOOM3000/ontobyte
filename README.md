# OntoByte

Sitio web corporativo de **OntoByte**, empresa de desarrollo de software y gestión de información. Proyecto formativo desarrollado por aprendices SENA.

**Sitio publicado:** https://ontobyte.site.je/

---

## Descripción

OntoByte ofrece soluciones de software a la medida: páginas web, bases de datos, aplicaciones móviles, inteligencia artificial y ciberseguridad. Este repositorio contiene el sitio corporativo y su panel administrativo.

## Funcionalidades

- Página de inicio, nosotros, servicios, precios, equipo y contacto.
- Registro e inicio de sesión de usuarios.
- Zona de miembros con descargas y datos de la cuenta.
- Panel administrativo con gestión de usuarios, mensajes de contacto y estadísticas.
- Formulario de contacto con validación y almacenamiento en base de datos.
- Protección CSRF, contraseñas con bcrypt y sesiones seguras.
- Diseño responsive y modo oscuro.

## Tecnologías

- PHP 8 (arquitectura MVC propia)
- MySQL / MariaDB con PDO y sentencias preparadas
- HTML5, CSS3 y JavaScript
- Font Awesome 6
- Apache con mod_rewrite

## Estructura del proyecto

```
app/
  controllers/    Controladores de la aplicación
  models/         Modelos y acceso a datos
  views/          Vistas y layouts
config/
  config.php      Constantes y configuración general
  database.php    Conexión PDO (no se versiona)
database/
  schema.sql      Estructura y datos iniciales
public/
  css/ js/ img/   Recursos públicos
  downloads/      Documentos descargables
  index.php       Front controller
```

## Instalación local (XAMPP)

1. Copia el proyecto en `C:\xampp\htdocs\ontobyte`.
2. Copia `config/database.example.php` como `config/database.php` y ajusta las credenciales.
3. Crea la base de datos `ontobyte_db` e importa `database/schema.sql`.
4. Inicia Apache y MySQL desde XAMPP.
5. Abre `http://localhost/ontobyte`.

## Despliegue

El sitio está publicado en InfinityFree con PHP 8 y MySQL:

- URL: https://ontobyte.site.je/
- Las credenciales de administración se entregan al docente por separado.

## Estado del proyecto

- Versión 1.0 publicada y funcionando.
- Repositorio inicializado con Git y listo para sincronizar con GitHub.
- Flujo de trabajo con ramas para nuevas funcionalidades.

## Autores

- Andrés Díaz
- Samuel Sánchez

Aprendices SENA — 2026.
