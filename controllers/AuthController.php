<?php
/**
 * Controlador de autenticación: registro, login, logout y estado de sesión.
 */
class AuthController extends Controller
{
    // GET /api/auth/status
    public function status(): void
    {
        $rol = $this->autenticado()
            ? (new Usuario())->rolDe((int)$_SESSION['usuario_id'])
            : null;

        $this->json([
            'autenticado' => $this->autenticado(),
            'usuario' => $_SESSION['usuario'] ?? null,
            'rol' => $rol,
        ]);
    }

    // POST /api/auth/register
    public function register(): void
    {
        $usuario = $this->post('usuario');
        $email = $this->post('email');
        $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

        if (!preg_match('/^[\p{L}\p{N}_-]{3,30}$/u', $usuario)) {
            $this->json(['error' => 'El usuario debe tener entre 3 y 30 caracteres (letras, números, guion o guion bajo).'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            $this->json(['error' => 'Ingresá un correo electrónico válido.'], 400);
        }
        if (strlen($password) < 8 || strlen($password) > 72) {
            $this->json(['error' => 'La contraseña debe tener entre 8 y 72 bytes.'], 400);
        }

        try {
            $id = (new Usuario())->crear($usuario, $email, password_hash($password, PASSWORD_DEFAULT));
        } catch (mysqli_sql_exception $error) {
            if ($error->getCode() === 1062) {
                $this->json(['error' => 'Ese usuario o correo ya está registrado.'], 409);
            }
            throw $error;
        }

        $this->iniciarSesion($id, $usuario);
        $this->json(['ok' => true, 'usuario' => $usuario, 'rol' => Usuario::ROL_JUGADOR], 201);
    }

    // POST /api/auth/login
    public function login(): void
    {
        $identificador = $this->post('identificador');
        $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

        $fila = (new Usuario())->buscarPorIdentificador($identificador);

        if (!$fila || !password_verify($password, $fila['password_hash'])) {
            $this->json(['error' => 'Usuario/correo o contraseña incorrectos.'], 401);
        }

        $this->iniciarSesion((int)$fila['id'], $fila['usuario']);
        $this->json(['ok' => true, 'usuario' => $fila['usuario'], 'rol' => $fila['rol']]);
    }

    // POST /api/auth/logout
    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        $this->json(['ok' => true]);
    }

    private function iniciarSesion(int $id, string $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario'] = $usuario;
    }
}
