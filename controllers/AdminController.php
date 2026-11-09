<?php
/**
 * Controlador del panel CRUD de cartas (requiere sesión iniciada y rol admin).
 */
class AdminController extends Controller
{
    private Jugador $jugadores;

    public function __construct()
    {
        if (!$this->autenticado()) {
            $this->redirigir('');
        }
        // Solo el rol 'admin' puede editar las cartas
        if (!$this->esAdmin()) {
            http_response_code(403);
            $this->vista('partials/acceso_denegado');
            exit;
        }
        $this->jugadores = new Jugador();
    }

    // GET /admin   (opcional: ?editar=ID)
    public function index(): void
    {
        $editando = null;
        if (isset($_GET['editar'])) {
            $editando = $this->jugadores->buscar((int)$_GET['editar']);
        }

        $mensaje = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->vista('admin/index', [
            'jugadores' => $this->jugadores->todos(),
            'editando'  => $editando,
            'mensaje'   => $mensaje,
        ]);
    }

    // POST /admin/guardar   (crea o actualiza)
    public function guardar(): void
    {
        $this->verificarCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $datos = [
            'nombre'        => $this->post('nombre'),
            'equipo'        => $this->post('equipo'),
            'division'      => $this->post('division'),
            'carta_img'     => $this->post('carta_img'),
            'escudo_equipo' => $this->post('escudo_equipo') ?: 'img/logo.png',
            'ataque'        => filter_var($_POST['ataque'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]),
            'defensa'       => filter_var($_POST['defensa'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]),
            'control'       => filter_var($_POST['control'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]),
        ];

        $invalido = $datos['nombre'] === '' || $datos['equipo'] === '' || $datos['carta_img'] === ''
            || !in_array($datos['division'], ['primera', 'segunda'], true)
            || $datos['ataque'] === false || $datos['defensa'] === false || $datos['control'] === false;

        if ($invalido) {
            $_SESSION['flash'] = 'Datos inválidos: completá todos los campos (estadísticas entre 0 y 100).';
            $this->redirigir($id ? 'admin?editar=' . $id : 'admin');
        }

        if ($id > 0) {
            $this->jugadores->actualizar($id, $datos);
            $_SESSION['flash'] = 'Jugador actualizado.';
        } else {
            $this->jugadores->crear($datos);
            $_SESSION['flash'] = 'Jugador creado.';
        }
        $this->redirigir('admin');
    }

    // POST /admin/eliminar
    public function eliminar(): void
    {
        $this->verificarCsrf();
        $this->jugadores->eliminar((int)($_POST['id'] ?? 0));
        $_SESSION['flash'] = 'Jugador eliminado.';
        $this->redirigir('admin');
    }

    private function verificarCsrf(): void
    {
        if (!csrf_valido($_POST['csrf'] ?? null)) {
            http_response_code(419);
            exit('Sesión expirada o token inválido. Volvé al panel e intentá de nuevo.');
        }
    }
}
