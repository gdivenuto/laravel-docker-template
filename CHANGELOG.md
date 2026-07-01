# Changelog

Todos los cambios importantes de este proyecto se documentarán en este archivo.

El formato está basado en **Keep a Changelog** y el proyecto sigue el versionado **Semantic Versioning (SemVer)**.

---

## [1.0.0] - 2026-07-01

### Added

* Plantilla base para proyectos Laravel con Docker.
* PHP 8.4 + Apache.
* MySQL 8.4.
* phpMyAdmin.
* Node.js 22.
* Mailpit para pruebas de correo electrónico.
* Configuración de Apache apuntando a `public/`.
* `Dockerfile` optimizado para Laravel.
* `docker-compose.yml` parametrizado mediante `.env`.
* `entrypoint.sh` para inicialización automática del entorno.
* `php.ini` personalizado.
* Sincronización automática entre `.env` (Docker) y `src/.env` (Laravel).
* Scripts organizados por categorías:

  * Docker
  * Laravel
  * Frontend
  * Utilidades
* Comando unificado `./dev`.
* Script `init` para inicialización del proyecto.
* Script `sync-env`.
* Scripts para Composer, Artisan, NPM, Vite y pruebas.
* Scripts para administración de Docker (up, down, rebuild, logs, ps, fresh).
* README completo con instrucciones de instalación y uso.

### Planned

Las siguientes funcionalidades están previstas para versiones futuras:

#### v1.1.0

* Redis.
* Queue Worker.
* Scheduler.

#### v1.2.0

* Xdebug.
* Docker Compose Profiles.
* Configuración para desarrollo y producción.

#### v1.3.0

* Scripts para backup y restauración de bases de datos.
* Health checks adicionales.
* Optimización de imágenes Docker.

#### v2.0.0

* Herramienta `gdocker`.
* Generación automática de nuevos proyectos.
* Soporte para múltiples plantillas (Laravel, PHP, API, Microservicios).
* Integración con Redis, Horizon y Reverb.
* Automatización completa de la creación del entorno de desarrollo.
