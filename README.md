# Laravel Docker Template — Entorno de desarrollo con ./dev

Plantilla para desarrollar aplicaciones Laravel con Docker, administrada mediante el comando ./dev. Permite inicializar el proyecto, gestionar los servicios y ejecutar Artisan, Composer y npm desde una única interfaz.

## Requisitos

- Git.
- Docker con Docker Compose v2 (`docker compose`).
- Una terminal compatible con Bash y permisos para ejecutar Docker.

Esta configuración está orientada al desarrollo local. Un despliegue en producción requiere su propia configuración.

## Instalación y configuración

### 1. Crear un repositorio desde la plantilla y clonarlo

En [laravel-docker-template](https://github.com/gdivenuto/laravel-docker-template), seleccionar **Use this template → Create a new repository**, completar el nombre y la visibilidad y crear el repositorio.

Clonar **el nuevo repositorio**, reemplazando los valores del ejemplo:

```bash
git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git
cd TU_REPOSITORIO
```

Todos los comandos siguientes se ejecutan desde la raíz de ese repositorio.

### 2. Crear la configuración del entorno

```bash
cp .env.example .env
```

Editar el `.env` de la raíz antes de inicializar:

```dotenv
PROJECT_NAME=mi_proyecto
APP_PORT=8080
MYSQL_PORT=3307
MYSQL_ROOT_PASSWORD=cambiar_root
MYSQL_DATABASE=mi_proyecto_db
MYSQL_USER=mi_proyecto_user
MYSQL_PASSWORD=cambiar_password
PMA_PORT=8081
NODE_PORT=5173
MAILPIT_PORT=8025
```

Reemplazar las contraseñas por valores propios. Para varios proyectos, utilizar distintos directorios, puertos disponibles y un `PROJECT_NAME` diferente: esta variable determina el nombre del volumen MySQL (`${PROJECT_NAME}_mysql_data`), no el nombre del proyecto de Docker Compose.

| Archivo | Uso |
|---|---|
| `.env` | Puertos, credenciales y nombre del volumen utilizados por Docker Compose y los scripts. |
| `src/.env` | Configuración de Laravel. Se crea si no existe y recibe los valores administrados por `sync-env`. |

Configurar la base de datos y los puertos en el `.env` de la raíz. `sync-env` sobrescribe en `src/.env` las variables `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `MAIL_MAILER`, `MAIL_SCHEME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` y `MAIL_FROM_ADDRESS`.

Otras variables propias de Laravel, como `APP_NAME`, `APP_URL` o las de integraciones externas, pueden editarse en `src/.env`: no se sincroniza todo el archivo de la raíz. El remitente de correo se fija actualmente en `test@example.com` mediante el script.

### 3. Inicializar el proyecto

```bash
./dev init
```

El comando realiza lo siguiente, en este orden:

1. Crea `.env` desde el ejemplo si falta y crea el directorio `src/` si falta.
2. Inicia los servicios en segundo plano con `docker compose up -d --build`.
3. Si no existe `src/artisan`, intenta crear Laravel mediante Composer en `src/` (el directorio debe estar vacío para crear un proyecto nuevo).
4. Crea `src/.env` si falta, sincroniza base de datos y correo y reinicia el servicio `app`.
5. Genera una nueva `APP_KEY`, crea el enlace `public/storage` y ejecuta las migraciones pendientes, sin seeders.

El repositorio ya incluye Laravel en `src/`. Al arrancar `app`, su entrypoint instala las dependencias Composer **solo si no existe `vendor/`**; también prepara el archivo de entorno, la clave si falta, el enlace de almacenamiento y los permisos de `storage/` y `bootstrap/cache/`.

**Ejecutar `init` para la primera preparación.** Cada ejecución regenera `APP_KEY`, incluso si ya existe, lo que puede invalidar sesiones y datos cifrados con la clave anterior. Para el uso diario, utilizar `./dev up`.

### 4. Preparar el frontend

```bash
./dev npm install
./dev vite
```

Mantener la terminal de Vite abierta durante el desarrollo. Para trabajar sin el servidor Vite, generar los recursos con `./dev build`.

### 5. Acceder a los servicios

Con los puertos del ejemplo:

| Servicio | Dirección o conexión |
|---|---|
| Laravel | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| Mailpit (interfaz de correos) | http://localhost:8025 |
| MySQL desde el equipo anfitrión | `127.0.0.1:3307` |
| Vite (servidor de recursos) | http://localhost:5173 |

Abrir Laravel en el puerto de la aplicación; Vite proporciona sus recursos frontend. Si se cambia `NODE_PORT`, revisar también la configuración de Vite y su conexión HMR: el script utiliza el puerto interno 5173 y no adapta automáticamente la configuración al puerto externo.

### 6. Adaptar el README al nuevo proyecto

Una vez creada la aplicación, **personalizar este README para documentar el proyecto que utiliza la plantilla**:

- Reemplazar el título, la descripción y los enlaces por los de la aplicación.
- Describir su finalidad, funcionalidades y requisitos específicos.
- Ajustar los ejemplos de configuración, puertos e instalación a su uso real, sin publicar secretos.
- Agregar los pasos necesarios de migraciones, seeders, pruebas e integraciones.
- Actualizar autoría y licencia según corresponda.

Conservar las instrucciones del entorno Docker que sigan siendo válidas y actualizar las tablas de comandos cuando cambien los scripts.

## Uso diario

Iniciar los servicios:

```bash
./dev up
```

Iniciar Vite en una terminal que permanecerá abierta:

```bash
./dev vite
```

Ejecutar otros comandos desde otra terminal. Al terminar, detener Vite con `Ctrl+C` y bajar el entorno:

```bash
./dev down
```

`down` detiene y elimina los contenedores y las redes del proyecto, conservando el volumen MySQL.

## Referencia de comandos

Consultar la ayuda con `./dev help`, `./dev --help`, `./dev -h` o simplemente `./dev`.

### Docker

| Comando | Comportamiento definido en el script |
|---|---|
| `./dev up` | Si existe `src/.env`, sincroniza base de datos y correo; luego ejecuta `docker compose up -d --build`. |
| `./dev down` | Ejecuta `docker compose down`: elimina contenedores y redes, sin borrar los volúmenes. |
| `./dev rebuild` | Ejecuta `docker compose down` y después `docker compose up -d --build`. Conserva los volúmenes; no ejecuta `sync-env` ni fuerza una construcción sin caché. |
| `./dev fresh` | Solicita confirmación (`s` o `S`); ejecuta `docker compose down -v` y después `docker compose up -d --build`. No ejecuta migraciones ni `sync-env`. |
| `./dev ps` | Ejecuta `docker compose ps` para mostrar el estado de los contenedores. |
| `./dev logs [servicio] [opciones]` | Ejecuta `docker compose logs -f` con los argumentos recibidos: sigue los registros hasta pulsar `Ctrl+C`. |
| `./dev bash` | Abre Bash dentro del servicio `app`, que debe estar en ejecución. |

**`./dev fresh` elimina los volúmenes del proyecto, incluidos los datos MySQL.** Hacer una copia de seguridad si deben conservarse. No equivale a `migrate:fresh`: elimina el volumen completo y no reconstruye por sí mismo el esquema Laravel.

Ejemplos de registros:

```bash
./dev logs app
./dev logs mysql --tail=100
```

### Laravel y Composer

El servicio `app` debe estar en ejecución. Los comandos se ejecutan dentro de `src/`, montado como `/var/www/html`.

| Comando | Comportamiento definido en el script |
|---|---|
| `./dev artisan [argumentos]` | Ejecuta `php artisan` y transmite todos los argumentos. |
| `./dev migrate [argumentos]` | Ejecuta `php artisan migrate` y transmite todos los argumentos. No ejecuta seeders por defecto. |
| `./dev composer [argumentos]` | Ejecuta Composer y transmite todos los argumentos. |
| `./dev test [argumentos]` | Ejecuta `php artisan test` y transmite todos los argumentos; no ejecuta el script `test` de Composer. |
| `./dev tinker` | Ejecuta `php artisan tinker`; el script no transmite argumentos adicionales. |

```bash
./dev artisan make:model Producto -mcr
./dev artisan route:list
./dev migrate --seed
./dev composer install
./dev composer require livewire/livewire
./dev test --filter=ExampleTest
```

`make:model Producto -mcr` crea el modelo, una migración y un controlador de recursos. Usar `--seed` solo cuando los seeders del proyecto estén preparados para los datos existentes.

### Frontend

El servicio `node` debe estar en ejecución; su directorio de trabajo también corresponde a `src/`.

| Comando | Comportamiento definido en el script |
|---|---|
| `./dev npm [argumentos]` | Ejecuta npm y transmite todos los argumentos, por ejemplo `./dev npm install`. |
| `./dev vite` | Ejecuta `npm run dev -- --host 0.0.0.0`. Permanece en primer plano y no transmite argumentos adicionales. |
| `./dev build` | Ejecuta `npm run build`. No transmite argumentos adicionales. |

Para pasar opciones a los scripts npm, utilizar `./dev npm run ... -- ...`.

### Inicialización y configuración

| Comando | Comportamiento definido en el script |
|---|---|
| `./dev init` | Prepara el entorno, sincroniza variables, reinicia `app`, regenera `APP_KEY`, crea el enlace de almacenamiento y ejecuta migraciones. Ver la instalación inicial. |
| `./dev sync-env` | Actualiza únicamente las variables de base de datos y correo enumeradas arriba en `src/.env`. Requiere ambos archivos `.env`; no inicia ni reinicia servicios ni limpia la caché de Laravel. |

Solo `logs`, `artisan`, `migrate`, `composer`, `test` y `npm` transmiten los argumentos adicionales a las herramientas subyacentes. Los demás scripts no los utilizan.

Existen también `scripts/frontend/install` y `scripts/laravel/fresh`, pero no están expuestos como comandos de `./dev`. En particular, `./dev fresh` corresponde al borrado de volúmenes Docker.

## Servicios y configuración

### MySQL y phpMyAdmin

Laravel utiliza `mysql:3306` dentro de la red Docker. Las herramientas del equipo anfitrión utilizan `127.0.0.1` y `MYSQL_PORT`. En phpMyAdmin, ingresar con los valores de `MYSQL_USER` y `MYSQL_PASSWORD`.

MySQL persiste sus datos en el volumen `${PROJECT_NAME}_mysql_data`. Cambiar las credenciales en `.env` no modifica automáticamente los usuarios de una base ya inicializada; las variables de inicialización de la imagen MySQL se aplican cuando el volumen está vacío.

### Mailpit

`sync-env` configura Laravel para enviar por SMTP a `mailpit:1025`, sin autenticación. Los mensajes de prueba se consultan en la interfaz web del puerto `MAILPIT_PORT`; ese puerto no es el de SMTP.

### Cambios de configuración

Después de modificar la configuración administrada desde el `.env` de la raíz:

```bash
./dev up
```

Este comando vuelve a sincronizar `src/.env` y aplica la configuración de Compose. Si Laravel tiene la configuración en caché, limpiarla:

```bash
./dev artisan config:clear
```

Para actualizar dependencias PHP de un proyecto existente, ejecutar `./dev composer install`: el entrypoint no vuelve a instalarlas si ya existe `vendor/`.

## Estructura

```text
mi-proyecto/
├── .env.example
├── Dockerfile
├── docker-compose.yml
├── README.md
├── dev
├── apache/
│   └── 000-default.conf
├── docker/
│   ├── entrypoint.sh
│   └── php.ini
├── scripts/
│   ├── docker/
│   ├── frontend/
│   ├── laravel/
│   └── utils/
└── src/                 # Aplicación Laravel
```

## Buenas prácticas

- Versionar el código de la aplicación y la configuración del entorno Docker.
- No versionar `.env`, `src/.env`, credenciales, `vendor/` ni `node_modules/`.
- Ejecutar las herramientas mediante los contenedores.
- Respaldar los datos antes de borrar volúmenes.
- Mantener esta documentación alineada con los scripts del proyecto.

## Licencia

MIT License.

## Autor

**Gabriel Eduardo Divenuto**

Lic. en Informática · Desarrollador Full Stack PHP / Laravel

[GitHub](https://github.com/gdivenuto)
