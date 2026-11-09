# Batalla de Cartas ⚽ — Versión MVC (PHP)

Juego de cartas de fútbol reorganizado con el patrón **Modelo – Vista – Controlador**.

## Instalación (WAMP)
1. Copiá la carpeta `JuegoFutbol/` dentro de `C:\wamp64\www\`.
2. Abrí phpMyAdmin e importá `database/JuegoFutbol.sql` (crea la base `juego_cartas`, con roles incluidos).
   Si ya tenías la base creada y no querés perder datos, ejecutá en cambio `database/roles_migracion.sql` (una sola vez).
3. Revisá los datos de conexión en el archivo `.env` (por defecto: root, sin contraseña).
4. Abrí **http://localhost/JuegoFutbol/**

## Roles y permisos
| Rol | Permisos |
|---|---|
| `admin` | Jugar + ver el botón **Panel Admin** y crear/editar/borrar cartas |
| `jugador` | Solo jugar. Todo usuario que se registra queda como `jugador` |

- Tablas: `roles` (id, nombre, descripcion) y `usuarios.rol_id` (clave foránea a `roles.id`).
- Usuario administrador inicial: **hola** (cambialo en `database/roles_migracion.sql`, paso 3).
- El rol se consulta en la base de datos en cada petición al panel (`Controller::esAdmin()`), no solo al iniciar sesión.
- Un jugador que entre a `/admin` por URL recibe un error 403 (Acceso denegado).
- Para promover a alguien: `UPDATE usuarios SET rol_id = 1 WHERE usuario = 'nombre';`

## Estructura de carpetas

```
JuegoFutbol/
├── config/        Conexión a la base de datos (db.php) y configuración global (config.php)
├── controllers/   Lógica de negocio y respuestas (JugadorController.php, AuthController.php, ...)
├── models/        Consultas SQL (Jugador.php, Partida.php, Usuario.php)
├── routes/        Endpoints y verbos HTTP (jugadorRoutes.php, authRoutes.php, ...)
├── views/         Vistas HTML/PHP (juego/index.php, admin/index.php, partials/)
├── core/          Núcleo del mini-framework (Router.php, Controller.php, Env.php)
├── public/        Archivos estáticos: css/, js/, img/, audio/
├── database/      Script SQL de la base de datos
├── docs/          Diagrama de la estructura MVC
├── .env           Variables de entorno (datos de la BD)
└── index.php      Archivo principal (Front Controller, equivale a server.js)
```

## Consigna 3 — Ubicación de cada componente MVC

| Componente MVC | Ubicación en el proyecto |
|---|---|
| Rutas (Routes) | `routes/` → `web.php`, `jugadorRoutes.php`, `authRoutes.php`, `partidaRoutes.php`, `adminRoutes.php` |
| Controladores (Controllers) | `controllers/` → `JugadorController.php`, `AuthController.php`, `PartidaController.php`, `AdminController.php`, `JuegoController.php` |
| Modelos (Models) | `models/` → `Jugador.php`, `Partida.php`, `Usuario.php` |

## Flujo de una petición
`index.php` → `routes/*.php` → `controllers/*Controller.php` → `models/*.php` (SQL) → `views/*` o JSON

## Endpoints

| Verbo | Ruta | Controlador@método |
|---|---|---|
| GET | `/` | JuegoController@index |
| GET | `/api/cartas` | JugadorController@cartas |
| GET | `/api/auth/status` | AuthController@status |
| POST | `/api/auth/register` | AuthController@register |
| POST | `/api/auth/login` | AuthController@login |
| POST | `/api/auth/logout` | AuthController@logout |
| POST | `/api/partidas` | PartidaController@guardar |
| GET | `/api/ranking` | PartidaController@ranking |
| GET | `/admin` | AdminController@index |
| POST | `/admin/guardar` | AdminController@guardar |
| POST | `/admin/eliminar` | AdminController@eliminar |

Las URLs se usan como `http://localhost/JuegoFutbol/index.php/api/cartas`.

## Correspondencia con el proyecto original

| Antes | Ahora |
|---|---|
| `conexion.php` | `config/db.php` + `.env` |
| `obtener_cartas.php` | `JugadorController::cartas` + `models/Jugador.php` |
| `obtener_ranking.php` | `PartidaController::ranking` + `views/partials/ranking_tabla.php` |
| `guardar_partida.php` | `PartidaController::guardar` + `models/Partida.php` |
| `auth.php` | `AuthController` + `models/Usuario.php` |
| `admin.php` | `AdminController` + `views/admin/index.php` + `public/css/admin.css` + `public/js/admin.js` |
| `index.html` | `views/juego/index.php` |
| `css/ js/ img/ audio/` | `public/css/ js/ img/ audio/` |

## Mejoras incluidas
- Todas las consultas del panel admin usan sentencias preparadas (antes había riesgo de inyección SQL).
- El panel admin exige sesión iniciada **y rol admin**, y usa token CSRF; el borrado se hace por POST.
- El panel usa las columnas reales de la tabla (`division`, `escudo_equipo`); antes usaba `habilidad`, que no existe en la base.
- Se bloquea el acceso web directo a `.env`, `config/`, `models/`, `controllers/`, etc.

## Diseño
- Tipografías: **Barlow Condensed** (títulos y botones) y **Barlow** (texto), cargadas desde Google Fonts.
- Paleta: verde cancha de noche, blanco tiza y ámbar. Los colores están como variables al inicio de `public/css/estilo.css` y `public/css/admin.css`, así que se cambian en un solo lugar.
- La zona de batalla es una cancha dibujada con CSS; el marcador tiene formato de tablero.
- Se rediseñó solo la parte visual: los ids, funciones y la lógica del juego no cambiaron.
