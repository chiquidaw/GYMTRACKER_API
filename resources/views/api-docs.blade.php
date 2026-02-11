<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentación API - Rutas</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;margin:24px;color:#111}
        .route{border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin:12px 0}
        .method{font-weight:700;padding:4px 8px;border-radius:4px;background:#eef2ff;color:#3730a3;margin-right:8px}
        .path{font-family:monospace;color:#065f46}
        .example{background:#f8fafc;border-left:4px solid #e2e8f0;padding:8px;margin-top:8px}
        h1,h2{margin:8px 0}
    </style>
</head>
<body>
    <h1>Documentación rápida de la API</h1>
    <p>Listado de rutas públicas y protegidas por token (Sanctum). Página informativa — no ejecuta llamadas.</p>

    <h2>Autenticación</h2>
    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/login</span>
        <div>Inicia sesión con credenciales y devuelve un token de acceso (Sanctum) y datos del usuario.</div>
        <div class="example">Ejemplo de petición: { "email": "usuario@ejemplo.com", "password": "secret" } • Ejemplo de respuesta: { "success": true, "token": "...", "user": {...} }</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/register</span>
        <div>Registra un nuevo usuario; devuelve token y datos del usuario recién creado.</div>
        <div class="example">Ejemplo de petición: { "name": "Nombre", "email": "usuario@ejemplo.com", "password": "secret" } • Ejemplo de respuesta: { "success": true, "token": "...", "user": {...} }</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/logout</span>
        <div>Protegida: invalida el token del usuario actual. Devuelve confirmación de cierre de sesión.</div>
        <div class="example">Encabezado: <strong>Authorization: Bearer &lt;token&gt;</strong> • Ejemplo de respuesta: { "success": true }</div>
    </div>

    <h2>Categorías</h2>
    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/categories</span>
        <div>Devuelve todas las categorías disponibles con conteo de ejercicios por categoría.</div>
        <div class="example">Ejemplo de respuesta: { "success": true, "data": [ { "id":1, "name":"Pecho", "icon_path":"/icons/chest.svg", "exercises_count":5 }, ... ] }</div>
    </div>

    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/categories/{id}</span>
        <div>Devuelve los detalles de una categoría por su id.</div>
    </div>

    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/categories/{id}/exercises</span>
        <div>Lista los ejercicios que pertenecen a la categoría especificada.</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/categories</span>
        <div>Protegida: crea una nueva categoría. Envía datos como `name` y `icon_path`.</div>
    </div>

    <div class="route">
        <span class="method">PUT</span>
        <span class="path">/api/categories/{id}</span>
        <div>Protegida: actualiza una categoría existente por id.</div>
    </div>

    <div class="route">
        <span class="method">DELETE</span>
        <span class="path">/api/categories/{id}</span>
        <div>Protegida: elimina la categoría indicada.</div>
    </div>

    <h2>Ejercicios</h2>
    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/exercises</span>
        <div>Lista todos los ejercicios. Cada ejercicio incluye su categoría y metadatos.</div>
    </div>

    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/exercises/{id}</span>
        <div>Devuelve los detalles de un ejercicio por id.</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/exercises</span>
        <div>Protegida: crea un nuevo ejercicio. Enviar nombre, descripción, categoría, etc.</div>
    </div>

    <div class="route">
        <span class="method">PUT</span>
        <span class="path">/api/exercises/{id}</span>
        <div>Protegida: actualiza un ejercicio existente.</div>
    </div>

    <div class="route">
        <span class="method">DELETE</span>
        <span class="path">/api/exercises/{id}</span>
        <div>Protegida: elimina el ejercicio indicado.</div>
    </div>

    <h2>Rutinas</h2>
    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/routines</span>
        <div>Lista rutinas públicas disponibles. Incluye resumen y autor.</div>
    </div>

    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/routines/{id}</span>
        <div>Devuelve los detalles de una rutina específica, incluyendo ejercicios.</div>
    </div>

    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/routines/{id}/exercises</span>
        <div>Lista los ejercicios que componen la rutina indicada.</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/routines</span>
        <div>Protegida: crea una nueva rutina.</div>
    </div>

    <div class="route">
        <span class="method">PUT</span>
        <span class="path">/api/routines/{id}</span>
        <div>Protegida: actualiza datos de una rutina.</div>
    </div>

    <div class="route">
        <span class="method">DELETE</span>
        <span class="path">/api/routines/{id}</span>
        <div>Protegida: elimina una rutina.</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/routines/{id}/exercises</span>
        <div>Protegida: añade un ejercicio a la rutina indicada.</div>
    </div>

    <div class="route">
        <span class="method">DELETE</span>
        <span class="path">/api/routines/{id}/exercises/{exercise_id}</span>
        <div>Protegida: remueve un ejercicio de la rutina.</div>
    </div>

    <h2>Mis rutinas (suscripciones)</h2>
    <div class="route">
        <span class="method">GET</span>
        <span class="path">/api/my-routines</span>
        <div>Protegida: obtiene las rutinas a las que el usuario está suscrito.</div>
    </div>

    <div class="route">
        <span class="method">POST</span>
        <span class="path">/api/my-routines</span>
        <div>Protegida: suscribe al usuario a una rutina (envía id de rutina).</div>
    </div>

    <div class="route">
        <span class="method">DELETE</span>
        <span class="path">/api/my-routines/{id}</span>
        <div>Protegida: desuscribe al usuario de la rutina indicada.</div>
    </div>

    <footer style="margin-top:24px;color:#6b7280">Nota: Las rutas etiquetadas como "Protegida" requieren header <strong>Authorization: Bearer &lt;token&gt;</strong>.</footer>
</body>
</html>
