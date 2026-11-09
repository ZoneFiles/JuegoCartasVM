<?php /** Vista principal del juego. Datos: ninguno. */ ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <base href="<?= e(ASSETS_URL) ?>/">
  <script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Batalla de Cartas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Barlow:wght@400;500;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="css/estilo.css" />
  <link rel="icon" type="image/png" href="favicon.png">
  <!-- Librería de Confeti -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>

<body>
  <main id="pantalla-auth" class="pantalla-auth">
    <section class="auth-panel">
      <h1><img src="img/logo.png" alt="" /> Batalla de Cartas</h1>
      <h2 id="auth-titulo">Iniciar sesión</h2>
      <p id="auth-descripcion">Ingresá con tu cuenta para jugar.</p>
      <form id="form-auth">
        <label for="auth-identificador" id="auth-label-identificador">Usuario o correo</label>
        <input id="auth-identificador" name="identificador" type="text" autocomplete="username" minlength="3"
          maxlength="254" required>
        <div id="auth-campo-usuario" class="auth-campo" hidden>
          <label for="auth-usuario">Nombre de usuario</label>
          <input id="auth-usuario" name="usuario" type="text" autocomplete="username" minlength="3" maxlength="30">
        </div>
        <div id="auth-campo-email" class="auth-campo" hidden>
          <label for="auth-email">Correo electrónico</label>
          <input id="auth-email" name="email" type="email" autocomplete="email" maxlength="254">
        </div>
        <label for="auth-password">Contraseña</label>
        <input id="auth-password" name="password" type="password" autocomplete="current-password" minlength="8"
          maxlength="72" required>
        <button id="auth-submit" type="submit">Ingresar</button>
      </form>
      <p id="auth-mensaje" class="auth-mensaje" role="status" aria-live="polite"></p>
      <button id="auth-cambiar" class="auth-cambiar" type="button">¿No tenés cuenta? Registrate</button>
    </section>
  </main>

  <div id="app-contenido" style="display:none;">
    <!-- Controles de Audio y Ambiente -->
    <div class="control-volumen">
      <button id="btn-ambiente" title="Activar/Desactivar Sonido Tribuna">🏟️</button>
      <button id="btn-mute" title="Silenciar/Activar Música">🔊</button>
      <input type="range" min="0" max="100" value="30" class="slider-volumen" id="volumen-slider">
    </div>

    <audio id="musica-fondo">
      <source src="" type="audio/mpeg">
    </audio>
    <audio id="audio-ambiente" loop>
      <source src="audio/tribuna.mp3" type="audio/mpeg">
    </audio>
    <audio id="sfx-clash">
      <source src="audio/clash.mp3" type="audio/mpeg">
    </audio>
    <audio id="sfx-gol">
      <source src="audio/silbato.mp3" type="audio/mpeg">
    </audio>

    <!-- INICIO -->
    <div id="pantalla-inicial" class="pantalla">
      <div class="hero-cartas" aria-hidden="true">
        <img src="img/cartas/borja.png" alt="">
        <img src="img/cartas/paredes.png" alt="">
        <img src="img/cartas/maravilla.png" alt="">
      </div>
      <h1>Batalla de Cartas</h1>
      <p class="inicio-etiqueta">Primera y Segunda División</p>
      <p class="inicio-bajada">Elegí un equipo, arrastrá tus cartas a la cancha y ganá cada ronda antes de quedarte sin energía.</p>
      <div class="acciones-inicio">
        <button onclick="mostrarModoJuego()">Jugar ahora</button>
        <button onclick="mostrarRanking()">Historial</button>
        <button onclick="mostrarReglas()">Reglas</button>
      </div>
      <footer>Derechos reservados · Grupo iOS</footer>
    </div>
    <div id="barra-usuario" class="barra-usuario">
      <span>Sesión: <strong id="usuario-actual"></strong> <small id="rol-actual"></small></span>
      <button id="btn-admin" type="button" style="display:none;" onclick="location.href='<?= e(url('admin')) ?>'">Panel de cartas</button>
      <button id="auth-cerrar" type="button">Cerrar sesión</button>
    </div>

    <!-- SELECCIÓN DE MODO DE JUEGO -->
    <div id="pantalla-modo" class="pantalla" style="display:none;">
      <h2>Elegí el modo de juego</h2>
      <div class="botones-division">
        <button onclick="setModoJuego('pvp')">2 jugadores (local)</button>
        <button onclick="setModoJuego('pve')">Contra la computadora</button>
      </div>
      <div style="margin-top: 15px;">
        <button onclick="volverAInicio()">Volver</button>
      </div>
    </div>

    <!-- RANKING / HISTORIAL -->
    <div id="pantalla-ranking" class="pantalla" style="display:none;">
      <h2>Historial de partidas</h2>
      <div id="ranking-container" class="card-panel"
        style="max-width: 600px; margin: 0 auto; max-height: 350px; overflow-y: auto;">
        <p>Cargando historial...</p>
      </div>
      <div style="margin-top: 15px;">
        <button onclick="volverAInicio()">Volver</button>
      </div>
    </div>

    <!-- DIVISIONES -->
    <div id="pantalla-divisiones" class="pantalla" style="display:none;">
      <h2>Elegí una división</h2>
      <div class="botones-division">
        <button onclick="mostrarEquipos('primera')">Primera División</button>
        <button onclick="mostrarEquipos('segunda')">Segunda División</button>
      </div>
      <div style="margin-top: 15px;">
        <button onclick="mostrarModoJuego()">Volver</button>
      </div>
    </div>

    <!-- SELECCIÓN DE EQUIPOS -->
    <div id="pantalla-equipos" class="pantalla" style="display:none;">
      <h2 id="seleccion-titulo">Jugador 1, elegí tu equipo</h2>
      <div id="equipos-container" class="grid-equipos"></div>
      <div style="margin-top: 15px;">
        <button onclick="volverADivisiones()">Volver</button>
      </div>
    </div>

    <!-- JUEGO -->
    <div id="pantalla-juego" class="pantalla" style="display:none;">
      <h2 id="titulo-juego"></h2>

      <div class="barras-resistencia-container">
        <div class="barra-wrapper">
          <span id="label-res-j1">Energía J1</span>
          <div class="barra-bg">
            <div id="energia-j1" class="barra-fill" style="width: 100%;"></div>
          </div>
        </div>
        <div class="barra-wrapper">
          <span id="label-res-j2">Energía J2 (IA)</span>
          <div class="barra-bg">
            <div id="energia-j2" class="barra-fill" style="width: 100%;"></div>
          </div>
        </div>
      </div>

      <div class="juego-wrapper">
        <div id="jugador1" class="zona-jugador">
          <h3 id="label-j1">Jugador 1</h3>
          <div class="cartas"></div>
        </div>

        <div id="zona-batalla" class="zona-batalla">
          <div class="cancha" aria-hidden="true">
            <span class="borde"></span>
            <span class="linea-media"></span>
            <span class="circulo"></span>
            <span class="punto"></span>
            <span class="area izq"></span>
            <span class="area der"></span>
            <span class="area-chica izq"></span>
            <span class="area-chica der"></span>
          </div>
          <div id="battlefield">
            <div id="battleJ1" class="battle-card"></div>
            <span class="vs-text">VS</span>
            <div id="battleJ2" class="battle-card"></div>
          </div>
          <div id="mensaje-ronda" class="mensaje-ronda"></div>
        </div>

        <div id="jugador2" class="zona-jugador">
          <h3 id="label-j2">Jugador 2</h3>
          <div class="cartas"></div>
        </div>
      </div>

      <div id="puntajes" class="marcador">Puntaje J1: 0 | Puntaje J2: 0</div>

      <div id="resultado-final"></div>
      <div style="margin-top: 15px;">
        <button id="btn-reiniciar" style="display:none;" onclick="reiniciarJuego()">Volver a jugar</button>
      </div>
    </div>

    <!-- Modal Reglas -->
    <div id="modal-reglas" class="modal">
      <div class="modal-content">
        <span class="close" onclick="cerrarReglas()">&times;</span>
        <h2>Reglas del juego</h2>
        <ol class="reglas-lista">
          <li>Cada equipo juega con sus <strong>3 cartas</strong>. Los dos jugadores empiezan con <strong>100 de energía</strong>.</li>
          <li>En cada ronda, arrastrá una de tus cartas hasta la cancha. Después juega el rival.</li>
          <li>Se toma la <strong>mejor estadística</strong> de la carta del Jugador 1 y se enfrenta a la estadística opuesta del rival: el ataque contra la defensa, la defensa contra el ataque y el control contra el control.</li>
          <li>Gana la ronda quien tenga el número más alto: suma <strong>1 punto</strong> y el perdedor pierde <strong>33 de energía</strong>. Si empatan, nadie suma.</li>
          <li>La partida termina cuando se acaban las cartas o cuando alguien se queda sin energía. Gana quien tenga más puntos.</li>
          <li>Las cartas con borde dorado son las más fuertes: sus tres estadísticas suman más de 250.</li>
          <li>Podés jugar de a dos en la misma pantalla o contra la computadora. Para guardar el resultado en el historial tenés que haber iniciado sesión.</li>
        </ol>
      </div>
    </div>

    <script src="js/script.js"></script>
  </div>
</body>

</html>