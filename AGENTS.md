# AGENTS.md

# Instrucciones

No analices todo el proyecto salvo que se solicite explícitamente.

Trabajá siempre sobre archivos o carpetas indicadas por el usuario.

Antes de modificar código, explicar brevemente qué cambio vas a realizar.

Ignorá estas carpetas:
- vendor/
- node_modules/
- storage/
- bootstrap/cache/
- public/build/

## Contexto general

Todo el desarrollo se realiza bajo la estructura de Laravel 13, PHP 8.3 y una base de datos MySQL nueva, diseñada de cero con el esquema estándar que el nuevo framework requiere.

## Stack principal

- PHP
- Laravel
- MySQL
- JavaScript
- Bootstrap / Tailwind según el proyecto
- APIs REST
- Git
- Linux

## Forma de trabajo esperada

Antes de modificar código, analizar:

1. Qué hace el archivo.
2. Qué dependencias tiene.
3. Qué impacto puede tener el cambio.
4. Si el cambio afecta base de datos, rutas, vistas, controladores o modelos.
5. Si existe riesgo de romper compatibilidad.

## Reglas importantes

- No modificar arquitectura sin explicar la razón.
- No eliminar código sin justificarlo.
- No cambiar nombres de tablas, columnas, rutas o métodos sin advertirlo.
- No asumir que se puede instalar cualquier paquete externo.
- Priorizar soluciones simples y mantenibles.
- Evitar sobreingeniería.
- Respetar el estilo existente del proyecto.
- Explicar cada cambio relevante.
- Si hay varias alternativas, indicar la más segura.

## Seguridad

Nunca exponer ni modificar sin advertencia:

- Credenciales
- Tokens
- API keys
- Archivos `.env`
- Datos de usuarios del sistema
- Datos personales
- Datos de documentos

Si se detectan credenciales en el código, advertirlo.

## Base de datos

Cuando se trabaje con MySQL:

- Revisar claves primarias y foráneas.
- Sugerir índices cuando haya búsquedas frecuentes.
- Evitar consultas N+1.
- No proponer cambios destructivos sin advertirlo.
- Si se propone una migración, explicar el impacto.

## Laravel

Cuando el proyecto use Laravel:

- Utilizar tipado estricto (`declare(strict_types=1);`), declaraciones de tipos nativas de PHP 8.3, FormRequests y controladores modernos en la raíz.
- Usar controladores, modelos, requests, services y policies según las convenciones actuales.
- No asumir que existe `RouteServiceProvider` (las rutas se configuran en `bootstrap/app.php` de forma moderna).
- Revisar middlewares, guards y rutas antes de proponer cambios.

## JavaScript

- Priorizar JavaScript claro y compatible.
- Evitar dependencias innecesarias.
- Si se usa fetch, manejar errores.
- Si se manipula DOM, verificar que los elementos existan.
- Evitar duplicación de listeners.

## Herramientas y Scripts del Proyecto

Al proponer comandos o realizar tareas de ejecución:
- Utilizar los scripts de Composer configurados en el proyecto en lugar de comandos sueltos siempre que sea posible:
  - `composer run setup` para inicialización.
  - `composer run dev` para iniciar el entorno de desarrollo (servidor, colas, Vite).
  - `composer run test` para ejecutar la suite de pruebas.

## Compilación de Assets

- Tener en cuenta que el proyecto raíz utiliza **Vite** para compilar JavaScript y CSS.
- Si se migran scripts o estilos desde los subsistemas anteriores, adaptarlos a la estructura de Vite en `resources/js` y `resources/css`.

## Pruebas (Testing)

- Al migrar o escribir nuevas funcionalidades en el proyecto raíz, proponer o crear pruebas unitarias y de integración utilizando **Pest** (framework de pruebas por defecto) o PHPUnit.
- Asegurar que la funcionalidad migrada no rompe los flujos de negocio existentes escribiendo pruebas de regresión.

## Respuestas esperadas

Responder de forma práctica, con:

1. Diagnóstico.
2. Solución recomendada.
3. Código sugerido.
4. Riesgos o puntos a revisar.
5. Pasos para probar.

## Estilo de respuesta

- Responder en español.
- Ser claro y directo.
- No dar respuestas excesivamente teóricas.
- Explicar como asistente técnico senior.
