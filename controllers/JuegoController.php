<?php
/**
 * Controlador de la pantalla principal del juego.
 */
class JuegoController extends Controller
{
    public function index(): void
    {
        $this->vista('juego/index');
    }
}
