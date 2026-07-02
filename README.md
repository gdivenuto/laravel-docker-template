# Sistema de Gestión (SGL) - Proyecto de Migración

Este repositorio contiene la estructura base para el proceso de migración progresiva del **Sistema de Gestión (SGL)**. El objetivo principal es consolidar, modernizar y unificar múltiples subsistemas antiguos en una arquitectura robusta basada en **Laravel 13** y **PHP 8.3**.

---

## 🏗️ Estructura del Repositorio

El repositorio se divide en dos secciones principales:

1. **Aplicación Destino (Laravel 13 - Raíz del proyecto):** Contiene el código modernizado y unificado bajo los estándares actuales de Laravel, usando PHP 8.3, Vite y PHPUnit/Pest.
2. **Subsistemas Anteriores (`sgl_anterior/`):** Almacena el código original de los sistemas que se están migrando progresivamente:
   - `sgl_anterior/biblioteca_anterior/`: Desarrollado con versiones antiguas de Laravel (5.8 o 7).
   - `sgl_anterior/inventario_anterior/`: Desarrollado con versiones antiguas de Laravel (5.8 o 7).
   - `sgl_anterior/con_mvc_nativo/`: Sistema desarrollado en PHP nativo, arquitectura MVC propia y jQuery.

---

## 🛠️ Requisitos Previos

Asegúrate de contar con el siguiente entorno configurado localmente:
- **PHP** >= 8.3
- **Composer** (para dependencias de PHP)
- **Node.js** & **npm** (para la compilación de assets con Vite)
- **MySQL** (motor de base de datos del sistema)

---

## 🚀 Inicialización y Configuración

El proyecto cuenta con scripts de Composer para automatizar y agilizar el setup de desarrollo:

### 1. Configuración de Variables de Entorno
Copia el archivo de ejemplo de variables de entorno y ajusta las credenciales de tu base de datos y otras configuraciones locales:
```bash
cp .env.example .env
```

### 2. Inicialización Automática
Ejecuta el comando de instalación automatizada. Este comando instalará las dependencias de Composer y NPM, generará la clave de la aplicación, ejecutará las migraciones pendientes y compilará los assets iniciales:
```bash
composer run setup
```

### 3. Servidor de Desarrollo
Levanta todos los servicios concurrentes de desarrollo (servidor de Laravel, procesador de colas, logger de Laravel Pail y Vite) en una sola consola con el siguiente comando:
```bash
composer run dev
```

### 4. Ejecución de Pruebas
Para limpiar configuraciones previas y ejecutar la suite de pruebas unitarias y de integración:
```bash
composer run test
```

---

## 📋 Estrategia de Migración Progresiva

Para mantener el sistema seguro, escalable y evitar riesgos de regresión, se debe seguir la siguiente estrategia al migrar funcionalidades desde `sgl_anterior/`:

1. **Análisis de Impacto:** Antes de migrar código, analiza su funcionamiento en el subsistema original, sus dependencias y las bases de datos implicadas.
2. **Migración de Base de Datos:**
   - Escribe migraciones de Laravel para recrear o adaptar tablas antiguas.
   - Declara correctamente claves primarias, foráneas e índices para búsquedas frecuentes.
   - Evita cambios destructivos sin previa planificación y aviso.
3. **Migración de Lógica (PHP Nativo / Laravel Antiguo a Laravel 13):**
   - Transforma controladores nativos o antiguos en controladores de Laravel modernos y estructurados.
   - Extrae la lógica de negocio a servicios (`Services`) o capas de persistencia adecuadas (modelos Eloquent).
   - Valida datos de entrada mediante `FormRequests` de Laravel.
4. **Migración de Vistas y Assets:**
   - Migra las plantillas a Blade usando componentes reutilizables.
   - Si usas jQuery o JavaScript personalizado de los subsistemas antiguos, limpia dependencias redundantes y asegúrate de que sean compatibles con PHP 8.x/Vite.
5. **Pruebas y Verificación:** Escribe tests con Pest/PHPUnit para las rutas y funcionalidades nuevas para asegurar que el comportamiento original se preserva.

---

## 🔒 Reglas Importantes de Desarrollo y Seguridad

* **Seguridad de Datos:** Nunca subas credenciales, tokens, API keys ni información sensible en los archivos del repositorio. Mantén las configuraciones en el archivo `.env`.
* **Compatibilidad de PHP:** Todo código migrado debe ser compatible con PHP 8.3+. Evita usar funciones deprecadas de versiones antiguas de PHP.
* **Consultas Eficientes:** Optimiza las queries para evitar el problema de consultas N+1. Utiliza `eager loading` (`with()`) en Eloquent donde sea necesario.
* **Dependencias:** No instales paquetes de terceros (`composer require` o `npm install`) a menos que sea estrictamente necesario y se haya validado su mantenimiento y compatibilidad. Prioriza soluciones nativas y mantenibles.
* **Mantenimiento de Arquitectura:** Respeta los patrones de diseño de Laravel y la estructura establecida. Si necesitas modificar nombres de tablas, columnas o rutas existentes, adviértelo al equipo de desarrollo previamente.
