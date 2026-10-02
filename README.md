# Laravel Docker Template

Plantilla profesional para desarrollo de aplicaciones Laravel utilizando Docker.

El objetivo de este proyecto es disponer de un entorno de desarrollo moderno, reproducible y fácil de utilizar, eliminando la necesidad de instalar Apache, PHP, MySQL, Node.js y demás herramientas directamente en el sistema operativo.

---

# Características

* PHP 8.4
* Apache
* MySQL 8.4
* phpMyAdmin
* Node.js 22
* Mailpit
* Docker Compose
* Composer
* Vite
* Configuración optimizada para Laravel
* Scripts de automatización
* Inicialización automática del proyecto
* Sincronización automática de variables de entorno

---

# Requisitos

* Docker
* Docker Compose

No es necesario instalar:

* Apache
* PHP
* Composer
* MySQL
* Node.js

Todo se ejecuta dentro de los contenedores Docker.

---

# Estructura del proyecto

```
laravel-docker-template/
│
├── .env.example
├── .gitignore
├── README.md
├── Dockerfile
├── docker-compose.yml
│
├── apache/
│   └── 000-default.conf
│
├── docker/
│   ├── entrypoint.sh
│   └── php.ini
│
├── scripts/
│   ├── docker/
│   ├── frontend/
│   ├── laravel/
│   └── utils/
│
├── src/
│
└── dev
```

---

# Uso

## 1.a. Clonar el repositorio

```bash
git clone https://github.com/gdivenuto/laravel-docker-template.git
cd laravel-docker-template
```
## 1.b. Usarlo como Template (recomendado)
Utilizar la opción **Use this template** (arriba a la derecha, botón verde)

Elegir **Create a new repository**

Completar los datos en base al proyecto que se desea crear con dicho template.

## 2. Crear el archivo de configuración

```bash
cp .env.example .env
```

## 3. Configurar variables de entorno

La plantilla utiliza dos archivos `.env`.

Editar **únicamente** el del **raíz del proyecto**:

```
.env
```

Es el único archivo que debe modificarse.

Utilizado para la configuración de Docker Compose.

Ejemplo:

```env
PROJECT_NAME=nombre_proyecto

APP_PORT=8080

MYSQL_PORT=3307
MYSQL_DATABASE=nombre_proyecto_db
MYSQL_USER=nombre_proyecto_user
MYSQL_PASSWORD=secret

PMA_PORT=8081

MAILPIT_PORT=8025

NODE_PORT=5173
```

**No modificar** `src/.env` manualmente.

---

## 4. Inicializar el proyecto

```bash
./dev init
```

Este comando:

* Levanta Docker
* Crea Laravel (si no existe)
* Sincroniza variables
* Instala dependencias
* Genera APP_KEY
* Ejecuta migraciones
* Crea el enlace de storage

```bash
./dev npm install
```

---

# Uso diario

## Iniciar el entorno

```bash
./dev up
```

## Ejecutar Vite

```bash
./dev vite
```

## Finalizar la jornada

```bash
./dev down
```

---

# Comandos disponibles

## Docker

Subir el entorno

```bash
./dev up
```

Bajar el entorno

```bash
./dev down
```

Reconstruir imágenes

```bash
./dev rebuild
```

Eliminar contenedores y volúmenes

```bash
./dev fresh
```

Ver contenedores

```bash
./dev ps
```

Ver logs

```bash
./dev logs
```

Logs de un servicio

```bash
./dev logs app

./dev logs mysql
```

Ingresar al contenedor

```bash
./dev bash
```

---

## Laravel

Ejecutar Artisan

```bash
./dev artisan
```

Ejemplos

```bash
./dev artisan make:model Producto -mcr

./dev artisan optimize

./dev artisan route:list
```

Migraciones

```bash
./dev migrate
```

Composer

```bash
./dev composer install

./dev composer update

./dev composer require livewire/livewire
```

Tinker

```bash
./dev tinker
```

Tests

```bash
./dev test
```

---

## Frontend

Instalar dependencias

```bash
./dev npm install
```

Servidor Vite

```bash
./dev vite
```

Build de producción

```bash
./dev build
```

---

# Servicios

## Aplicación Laravel

```
http://localhost:8080
```

## phpMyAdmin

```
http://localhost:8081
```

## Mailpit

```
http://localhost:8025
```

## Laravel

```
src/.env
```

Se genera automáticamente.

No debería modificarse manualmente para la configuración de la base de datos.

---

# Base de datos

Host

```
mysql
```

Puerto interno

```
3306
```

Puerto externo

Definido por:

```
MYSQL_PORT
```

---

# Mailpit

Laravel queda configurado automáticamente para enviar correos a Mailpit.

No es necesario utilizar Gmail durante el desarrollo.

Interfaz web:

```
http://localhost:8025
```

---

# phpMyAdmin

Permite administrar la base de datos desde el navegador.

URL:

```
http://localhost:8081
```

Usuario:

```
MYSQL_USER
```

Contraseña:

```
MYSQL_PASSWORD
```

---

# Flujo de trabajo recomendado

Al comenzar el día

```bash
./dev up

./dev vite
```

Durante el desarrollo

```bash
./dev artisan make:controller ProductoController

./dev composer require paquete

./dev migrate
```

Al finalizar

```bash
./dev down
```

---

# Buenas prácticas

* Versionar todo el proyecto, incluyendo Docker.
* No versionar `.env`.
* No versionar `src/.env`.
* No versionar `vendor`.
* No versionar `node_modules`.
* Mantener Docker como única fuente del entorno de desarrollo.

---

# Licencia

MIT License

---

# Autor

Gabriel Eduardo Divenuto

Licenciado en Informática

Desarrollador Full Stack PHP / Laravel
