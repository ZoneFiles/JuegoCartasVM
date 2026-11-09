<?php
/**
 * Router mínimo: asocia (verbo HTTP + ruta) con [Controlador, método].
 */
class Router
{
    private array $rutas = [];

    public function get(string $ruta, array $accion): void
    {
        $this->agregar('GET', $ruta, $accion);
    }

    public function post(string $ruta, array $accion): void
    {
        $this->agregar('POST', $ruta, $accion);
    }

    private function agregar(string $metodo, string $ruta, array $accion): void
    {
        $this->rutas[$metodo]['/' . trim($ruta, '/')] = $accion;
    }

    public function despachar(string $metodo, string $uri): void
    {
        $ruta = $this->normalizar($uri);

        if (isset($this->rutas[$metodo][$ruta])) {
            [$clase, $accion] = $this->rutas[$metodo][$ruta];
            (new $clase())->$accion();
            return;
        }

        foreach ($this->rutas as $otroMetodo => $rutas) {
            if (isset($rutas[$ruta])) {
                $this->error(405, 'Método no permitido.');
            }
        }
        $this->error(404, 'Ruta no encontrada.');
    }

    /** Quita la carpeta base y "index.php" de la URL solicitada. */
    private function normalizar(string $uri): string
    {
        $ruta = rawurldecode((string)parse_url($uri, PHP_URL_PATH));
        if (BASE_PATH !== '' && str_starts_with($ruta, BASE_PATH)) {
            $ruta = substr($ruta, strlen(BASE_PATH));
        }
        if (str_starts_with($ruta, '/index.php')) {
            $ruta = substr($ruta, strlen('/index.php'));
        }
        return '/' . trim($ruta, '/');
    }

    private function error(int $codigo, string $mensaje): void
    {
        http_response_code($codigo);
        if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', BASE_PATH . '/index.php/api')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => $mensaje], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo $codigo . ' - ' . $mensaje;
        }
        exit;
    }
}
