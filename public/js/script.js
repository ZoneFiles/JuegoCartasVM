// =======================
// script.js - Versión Definitiva Corregida
// =======================

let divisiones = {};
let juegoGlobal = null;
let musicaGlobal = null;
let modoJuego = 'pvp'; // 'pvp' o 'pve'
let modoRegistro = false;
let aplicacionIniciada = false;

// Las rutas de la API las define el servidor (routes/*.php). APP_URL se inyecta desde la vista.
const api = (ruta) => `${window.APP_URL}/api/${ruta}`;

async function leerRespuestaAuth(respuesta) {
  const contenido = await respuesta.text();

  try {
    return JSON.parse(contenido);
  } catch {
    const mensaje = location.protocol === 'file:'
      ? 'Para crear una cuenta, iniciá Apache y MySQL y abrí el juego desde http://localhost/JuegoFutbol/. No abras index.html directamente.'
      : 'El servidor no devolvió una respuesta válida. Verificá que PHP esté habilitado y que Apache y MySQL estén funcionando.';
    throw new Error(mensaje);
  }
}

function mostrarFormularioRegistro(registro) {
  modoRegistro = registro;
  document.getElementById('auth-titulo').textContent = registro ? 'Crear cuenta' : 'Iniciar sesión';
  document.getElementById('auth-descripcion').textContent = registro
    ? 'Registrate para guardar tu cuenta y jugar.'
    : 'Ingresá con tu cuenta para jugar.';
  document.getElementById('auth-label-identificador').hidden = registro;
  document.getElementById('auth-identificador').hidden = registro;
  document.getElementById('auth-identificador').required = !registro;
  document.getElementById('auth-campo-usuario').hidden = !registro;
  document.getElementById('auth-campo-email').hidden = !registro;
  document.getElementById('auth-usuario').required = registro;
  document.getElementById('auth-email').required = registro;
  document.getElementById('auth-password').autocomplete = registro ? 'new-password' : 'current-password';
  document.getElementById('auth-submit').textContent = registro ? 'Registrarme' : 'Ingresar';
  document.getElementById('auth-cambiar').textContent = registro
    ? '¿Ya tenés cuenta? Iniciá sesión'
    : '¿No tenés cuenta? Registrate';
  document.getElementById('auth-mensaje').textContent = '';
}

function mostrarAplicacion(usuario, rol) {
  document.getElementById('pantalla-auth').style.display = 'none';
  document.getElementById('app-contenido').style.display = '';
  document.getElementById('usuario-actual').textContent = usuario;

  // Solo los administradores ven el botón del panel de cartas
  const esAdmin = rol === 'admin';
  document.getElementById('btn-admin').style.display = esAdmin ? '' : 'none';
  document.getElementById('rol-actual').textContent = esAdmin ? '(administrador)' : '';

  if (aplicacionIniciada) return;
  aplicacionIniciada = true;
  iniciarAplicacion();
}

async function enviarFormularioAuth(event) {
  event.preventDefault();
  const mensaje = document.getElementById('auth-mensaje');
  const boton = document.getElementById('auth-submit');
  const datos = new URLSearchParams(new FormData(event.currentTarget));
  boton.disabled = true;
  mensaje.textContent = '';

  try {
    const respuesta = await fetch(api(modoRegistro ? 'auth/register' : 'auth/login'), { method: 'POST', body: datos });
    const resultado = await leerRespuestaAuth(respuesta);
    if (!respuesta.ok) throw new Error(resultado.error || 'No se pudo completar la operación.');
    mostrarAplicacion(resultado.usuario, resultado.rol);
  } catch (error) {
    mensaje.textContent = error.message || 'No se pudo conectar con el servidor.';
  } finally {
    boton.disabled = false;
  }
}

async function cerrarSesion() {
  const respuesta = await fetch(api('auth/logout'), { method: 'POST' });
  if (!respuesta.ok) throw new Error('No se pudo cerrar la sesión.');
  location.reload();
}

class Carta {
  constructor({ nombre, ataque, defensa, control, cartaImg, equipo, habilidad }) {
    this.nombre = nombre;
    this.ataque = Number(ataque);
    this.defensa = Number(defensa);
    this.control = Number(control);
    this.cartaImg = cartaImg;
    this.equipo = equipo;
    this.habilidad = habilidad || "";
    this.poderTotal = this.ataque + this.defensa + this.control;
  }
}

class Musica {
  constructor() {
    this.playlist = ['audio/musica1.mp3', 'audio/musica2.mp3', 'audio/musica3.mp3'];
    this.cancionActual = 0;
    this.audioElem = document.getElementById("musica-fondo");
    this.audioAmbiente = document.getElementById("audio-ambiente");
    this.sfxClash = document.getElementById("sfx-clash");
    this.sfxGol = document.getElementById("sfx-gol");
    this.volumenSlider = document.getElementById("volumen-slider");
    this.btnMute = document.getElementById("btn-mute");
    this.btnAmbiente = document.getElementById("btn-ambiente");
    this.estaMuteado = false;
    this.ambienteActivo = false;

    if (this.volumenSlider) this.volumenSlider.value = 30;
    if (this.audioElem) this.audioElem.volume = 0.3;
    if (this.audioAmbiente) this.audioAmbiente.volume = 0.2;
    if (this.audioElem && this.playlist.length) this.audioElem.src = this.playlist[this.cancionActual];

    this._configurarEventos();
    document.addEventListener('click', () => this.reproducirMusica(), { once: true });
  }

  _configurarEventos() {
    if (this.audioElem) {
      this.audioElem.addEventListener('ended', () => {
        this.cancionActual = (this.cancionActual + 1) % this.playlist.length;
        this.audioElem.src = this.playlist[this.cancionActual];
        this.audioElem.play().catch(()=>{});
      });
    }

    if (this.volumenSlider) {
      this.volumenSlider.addEventListener('input', (e) => {
        const vol = e.target.value / 100;
        if (this.audioElem) this.audioElem.volume = vol;
        if (this.audioAmbiente) this.audioAmbiente.volume = vol * 0.7;
      });
    }

    if (this.btnMute) {
      this.btnMute.addEventListener('click', () => {
        if (this.estaMuteado) {
          if (this.audioElem) this.audioElem.volume = 0.3;
          this.btnMute.textContent = '🔊';
          this.estaMuteado = false;
        } else {
          if (this.audioElem) this.audioElem.volume = 0;
          this.btnMute.textContent = '🔇';
          this.estaMuteado = true;
        }
      });
    }

    if (this.btnAmbiente) {
      this.btnAmbiente.addEventListener('click', () => {
        if (this.ambienteActivo) {
          this.audioAmbiente.pause();
          this.btnAmbiente.style.opacity = '0.5';
          this.ambienteActivo = false;
        } else {
          this.audioAmbiente.play().catch(()=>{});
          this.btnAmbiente.style.opacity = '1';
          this.ambienteActivo = true;
        }
      });
    }
  }

  reproducirMusica() { if (this.audioElem && this.audioElem.paused) this.audioElem.play().catch(()=>{}); }
  reproducirClash() { if (this.sfxClash) { this.sfxClash.currentTime = 0; this.sfxClash.play().catch(()=>{}); } }
  reproducirGol() { if (this.sfxGol) { this.sfxGol.currentTime = 0; this.sfxGol.play().catch(()=>{}); } }
}

class Jugador {
  constructor(label, equipoNombre, cartas = []) {
    this.label = label;
    this.equipo = equipoNombre;
    this.cartas = cartas.map(c => new Carta(c));
    this.puntaje = 0;
    this.energia = 100;
  }
  sumarPunto() { this.puntaje++; }
  reducirEnergia(cantidad) { this.energia = Math.max(0, this.energia - cantidad); }
}

class Juego {
  constructor(divisionesObj) {
    this.divisiones = divisionesObj;
    this.equipoJ1 = null;
    this.equipoJ2 = null;
    this.jugador1 = null;
    this.jugador2 = null;
    this.turno = 1;
    this.cartaJ1 = null;
    this.cartaJ2 = null;
    this.rondaActiva = false;
  }

  iniciar(division, equipo1, equipo2) {
    this.equipoJ1 = equipo1;
    this.equipoJ2 = equipo2;
    const jugadores1 = this.divisiones[division][equipo1].jugadores;
    const jugadores2 = this.divisiones[division][equipo2].jugadores;
    this.jugador1 = new Jugador("jugador1", equipo1, jugadores1);
    this.jugador2 = new Jugador("jugador2", equipo2, jugadores2);
    this.turno = 1;
    this.cartaJ1 = null;
    this.cartaJ2 = null;
    this.rondaActiva = false;
    this._poblarUIConCartas();
    this._actualizarUIInicio();
  }

  _poblarUIConCartas() {
    const cont1 = document.querySelector("#jugador1 .cartas");
    const cont2 = document.querySelector("#jugador2 .cartas");
    if (!cont1 || !cont2) return;
    cont1.innerHTML = ""; cont2.innerHTML = "";

    let id = 0;
    this.jugador1.cartas.forEach(c => cont1.appendChild(crearCartaDOM(c, "jugador1", `j1-${id++}`)));
    id = 0;
    this.jugador2.cartas.forEach(c => {
      const cardDOM = crearCartaDOM(c, "jugador2", `j2-${id++}`);
      if (modoJuego === 'pve') cardDOM.style.opacity = '0.7';
      cont2.appendChild(cardDOM);
    });
  }

  _actualizarUIInicio() {
    document.getElementById("titulo-juego").innerText = `${this.equipoJ1} vs ${this.equipoJ2}`;
    document.getElementById("label-j2").innerText = modoJuego === 'pve' ? "Computadora (IA)" : "Jugador 2";
    document.getElementById("label-res-j2").innerText = modoJuego === 'pve' ? "Energía IA" : "Energía J2";
    document.getElementById("puntajes").innerText = `Puntaje J1: 0 | Puntaje J2: 0`;
    document.getElementById("mensaje-ronda").innerText = `Turno del Jugador 1...`;
    document.getElementById("resultado-final").innerText = "";
    document.getElementById("btn-reiniciar").style.display = "none";
    this._actualizarEnergiaUI();
  }

  _actualizarEnergiaUI() {
    document.getElementById("energia-j1").style.width = `${this.jugador1.energia}%`;
    document.getElementById("energia-j2").style.width = `${this.jugador2.energia}%`;
  }

  procesarDrop(data) {
    if (this.rondaActiva) return;
    const esTurnoValido = (this.turno === 1 && data.jugador === "jugador1") || (this.turno === 2 && data.jugador === "jugador2" && modoJuego === 'pvp');
    if (!esTurnoValido) { alert("No es tu turno o acción no permitida!"); return; }

    const elem = document.querySelector(`.carta[data-id="${data.id}"][data-jugador="${data.jugador}"]`);
    if (elem) elem.remove();

    if (this.turno === 1) {
      this.cartaJ1 = data;
      mostrarMiniCarta(data, document.getElementById("battleJ1"));
      musicaGlobal.reproducirClash();
      
      if (modoJuego === 'pve') {
        document.getElementById("mensaje-ronda").innerText = "La Computadora está pensando...";
        this.turno = 2;
        this.rondaActiva = true;
        setTimeout(() => this.ejecutarTurnoIA(), 1200);
      } else {
        document.getElementById("mensaje-ronda").innerText = "Esperando Jugador 2...";
        this.turno = 2;
      }
      return;
    }

    if (this.turno === 2 && modoJuego === 'pvp') {
      this.cartaJ2 = data;
      mostrarMiniCarta(data, document.getElementById("battleJ2"));
      musicaGlobal.reproducirClash();
      this.rondaActiva = true;
      this._compararYMostrar();
      this.turno = 1;
    }
  }

  ejecutarTurnoIA() {
    const cartasIA = document.querySelectorAll("#jugador2 .carta");
    if (cartasIA.length === 0) return;

    const cartaAleatoriaDOM = cartasIA[Math.floor(Math.random() * cartasIA.length)];
    const dataIA = { ...cartaAleatoriaDOM.dataset };
    cartaAleatoriaDOM.remove();

    this.cartaJ2 = dataIA;
    mostrarMiniCarta(dataIA, document.getElementById("battleJ2"));
    musicaGlobal.reproducirClash();
    this._compararYMostrar();
    this.turno = 1;
  }

  _compararYMostrar() {
    const zonaBatalla = document.getElementById("zona-batalla");
    zonaBatalla.classList.add("shake");
    setTimeout(() => zonaBatalla.classList.remove("shake"), 400);

    const j1V = Math.max(Number(this.cartaJ1.ataque), Number(this.cartaJ1.defensa), Number(this.cartaJ1.control));
    let statJ1 = j1V === Number(this.cartaJ1.ataque) ? "Ataque" : (j1V === Number(this.cartaJ1.defensa) ? "Defensa" : "Control");

    let j2V = 0, statJ2 = "";
    if (statJ1 === "Ataque") { j2V = Number(this.cartaJ2.defensa); statJ2 = "Defensa"; }
    else if (statJ1 === "Defensa") { j2V = Number(this.cartaJ2.ataque); statJ2 = "Ataque"; }
    else { j2V = Number(this.cartaJ2.control); statJ2 = "Control"; }

    let txt = "";
    let ganadorRonda = null;

    if (j1V > j2V) {
      this.jugador1.sumarPunto();
      this.jugador2.reducirEnergia(33);
      ganadorRonda = this.equipoJ1;
      txt = `${this.cartaJ1.nombre} (${statJ1}: ${j1V}) gana a ${this.cartaJ2.nombre} (${statJ2}: ${j2V})`;
      musicaGlobal.reproducirGol();
    } else if (j2V > j1V) {
      this.jugador2.sumarPunto();
      this.jugador1.reducirEnergia(33);
      ganadorRonda = modoJuego === 'pve' ? "Computadora" : this.equipoJ2;
      txt = `${this.cartaJ2.nombre} (${statJ2}: ${j2V}) gana a ${this.cartaJ1.nombre} (${statJ1}: ${j1V})`;
      musicaGlobal.reproducirGol();
    } else {
      txt = `¡Empate en la ronda!`;
    }

    document.getElementById("puntajes").innerText = `Puntaje J1: ${this.jugador1.puntaje} | Puntaje J2: ${this.jugador2.puntaje}`;
    document.getElementById("mensaje-ronda").innerText = txt;
    this._actualizarEnergiaUI();

    setTimeout(() => {
      this.cartaJ1 = null; this.cartaJ2 = null;
      document.getElementById("battleJ1").innerHTML = "";
      document.getElementById("battleJ2").innerHTML = "";
      document.getElementById("mensaje-ronda").innerText = "Turno del Jugador 1...";
      this.rondaActiva = false;
      this._chequearFin();
    }, 2800);
  }

  _chequearFin() {
    const sinCartas = document.querySelectorAll("#jugador1 .carta").length === 0 && document.querySelectorAll("#jugador2 .carta").length === 0;
    const sinEnergia = this.jugador1.energia <= 0 || this.jugador2.energia <= 0;

    if (sinCartas || sinEnergia) {
      let msg = "¡Empate!";
      let ganadorFinal = "Empate";
      let perdedorFinal = "Empate";

      if (this.jugador1.puntaje > this.jugador2.puntaje || this.jugador2.energia <= 0) {
        msg = `¡Gana ${this.equipoJ1} (Jugador 1)!`;
        ganadorFinal = this.equipoJ1;
        perdedorFinal = modoJuego === 'pve' ? "Computadora" : this.equipoJ2;
      } else if (this.jugador2.puntaje > this.jugador1.puntaje || this.jugador1.energia <= 0) {
        const nombreGanadorJ2 = modoJuego === 'pve' ? "Computadora" : this.equipoJ2;
        msg = `¡Gana ${nombreGanadorJ2}!`;
        ganadorFinal = nombreGanadorJ2;
        perdedorFinal = this.equipoJ1;
      }

      document.getElementById("resultado-final").innerText = msg;
      document.getElementById("btn-reiniciar").style.display = "inline-block";

      if (typeof confetti === 'function') {
        confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 } });
      }

      guardarPartidaEnRanking(ganadorFinal, perdedorFinal, this.jugador1.puntaje, this.jugador2.puntaje);
    }
  }
}

// Funciones de Navegación UI
function mostrarModoJuego() {
  document.getElementById("pantalla-inicial").style.display = "none";
  document.getElementById("pantalla-modo").style.display = "block";
  document.getElementById("pantalla-divisiones").style.display = "none";
  document.getElementById("pantalla-ranking").style.display = "none";
  document.getElementById("pantalla-equipos").style.display = "none";
  document.getElementById("pantalla-juego").style.display = "none";
}

function setModoJuego(modo) {
  modoJuego = modo;
  document.getElementById("pantalla-modo").style.display = "none";
  document.getElementById("pantalla-divisiones").style.display = "block";
}

function volverAInicio() {
  document.getElementById("pantalla-inicial").style.display = "block";
  document.getElementById("pantalla-modo").style.display = "none";
  document.getElementById("pantalla-ranking").style.display = "none";
  document.getElementById("pantalla-divisiones").style.display = "none";
}

function volverADivisiones() {
  document.getElementById("pantalla-equipos").style.display = "none";
  document.getElementById("pantalla-divisiones").style.display = "block";
}

let equipoSeleccionadoJ1 = null;
let equipoSeleccionadoJ2 = null;
let divisionSeleccionada = null;

function mostrarEquipos(division) {
  divisionSeleccionada = division;
  document.getElementById("pantalla-divisiones").style.display = "none";
  document.getElementById("pantalla-equipos").style.display = "block";
  document.getElementById("seleccion-titulo").innerText = modoJuego === 'pve' ? "Elegí tu equipo" : "Jugador 1, elegí tu equipo";
  
  equipoSeleccionadoJ1 = null;
  equipoSeleccionadoJ2 = null;
  
  const container = document.getElementById("equipos-container");
  container.innerHTML = "";

  const equipos = divisiones[division] ? Object.keys(divisiones[division]) : [];
  if (equipos.length < 2) {
    container.innerHTML = "<p style='grid-column: 1/-1; color: #f59e0b;'>Se necesitan al menos dos equipos cargados en esta división para jugar.</p>";
    return;
  }

  equipos.forEach(equipo => {
    const card = document.createElement("div");
    card.className = "equipo-card";
    card.innerHTML = `<img src="${divisiones[division][equipo].escudo}" alt="${equipo}"><p>${equipo}</p>`;
    card.addEventListener('click', () => seleccionarEquipoUI(equipo, card));
    container.appendChild(card);
  });
}

function seleccionarEquipoUI(equipo, card) {
  if (card.classList.contains("disabled")) return;

  if (!equipoSeleccionadoJ1) {
    equipoSeleccionadoJ1 = equipo;
    card.classList.add("disabled");
    document.getElementById("seleccion-titulo").innerText = modoJuego === 'pve'
      ? "Elegí el equipo rival para la IA"
      : "Jugador 2, elegí tu equipo";
    return;
  }

  if (!equipoSeleccionadoJ2) {
    if (equipo === equipoSeleccionadoJ1) {
      alert("Elegí un equipo diferente para el rival.");
      return; 
    }
    equipoSeleccionadoJ2 = equipo;
    card.classList.add("disabled");
    document.querySelectorAll('.equipo-card').forEach(c => c.classList.add('disabled'));

    setTimeout(() => {
      juegoGlobal = new Juego(divisiones);
      juegoGlobal.iniciar(divisionSeleccionada, equipoSeleccionadoJ1, equipoSeleccionadoJ2);
      document.getElementById("pantalla-equipos").style.display = "none";
      document.getElementById("pantalla-juego").style.display = "block";
    }, 300);
  }
}

function mostrarReglas() { document.getElementById('modal-reglas').style.display = 'block'; }
function cerrarReglas() { document.getElementById('modal-reglas').style.display = 'none'; }
function reiniciarJuego() { location.reload(); }

function crearCartaDOM(carta, owner, id) {
  const div = document.createElement("div");
  div.className = "carta";
  if (carta.ataque + carta.defensa + carta.control > 250) {
    div.classList.add("holografica");
  }

  div.draggable = true;
  div.dataset.id = id;
  div.dataset.jugador = owner;
  div.dataset.nombre = carta.nombre;
  div.dataset.ataque = carta.ataque;
  div.dataset.defensa = carta.defensa;
  div.dataset.control = carta.control;
  div.dataset.cartaImg = carta.cartaImg;
  div.dataset.equipo = carta.equipo;
  div.innerHTML = `<img src="${carta.cartaImg}" alt="${carta.nombre}">`;
  return div;
}

function mostrarMiniCarta(carta, cont) {
  if (!cont) return;
  cont.innerHTML = `
    <img src="${carta.cartaImg}" style="width:100%; border-radius:6px; height: 90px; object-fit: cover;">
    <div style="font-weight:700; font-size:11px; margin-top:2px;">${carta.nombre}</div>
    <div style="font-size:9px; color:#38bdf8;">ATQ ${carta.ataque} · DEF ${carta.defensa} · CTR ${carta.control}</div>
  `;
}

function guardarPartidaEnRanking(ganador, perdedor, p1, p2) {
  fetch(api('partidas'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `ganador=${encodeURIComponent(ganador)}&perdedor=${encodeURIComponent(perdedor)}&p1=${p1}&p2=${p2}`
  })
    .then(respuesta => {
      if (!respuesta.ok) throw new Error(`No se pudo guardar la partida (HTTP ${respuesta.status}).`);
    })
    .catch(error => console.error(error.message));
}

function mostrarRanking() {
  document.getElementById("pantalla-inicial").style.display = "none";
  document.getElementById("pantalla-ranking").style.display = "block";
  
  fetch(api('ranking'))
    .then(res => res.text())
    .then(html => {
      document.getElementById("ranking-container").innerHTML = html;
    })
    .catch(() => {
      document.getElementById("ranking-container").innerHTML = "<p>No se pudo cargar el historial.</p>";
    });
}

function iniciarAplicacion() {
  musicaGlobal = new Musica();

  const zona = document.getElementById("zona-batalla");
  if (zona) {
    document.addEventListener("dragstart", (e) => {
      const carta = e.target.closest(".carta");
      if (!carta) return;
      e.dataTransfer.setData("application/json", JSON.stringify(carta.dataset));
    });
    zona.addEventListener("dragover", (e) => { e.preventDefault(); zona.classList.add("dragover"); });
    zona.addEventListener("dragleave", () => zona.classList.remove("dragover"));
    zona.addEventListener("drop", (e) => {
      e.preventDefault();
      zona.classList.remove("dragover");
      if (!juegoGlobal) return;
      try { juegoGlobal.procesarDrop(JSON.parse(e.dataTransfer.getData("application/json"))); } catch(err){}
    });
  }

  fetch(api('cartas'))
    .then(res => res.json())
    .then(data => { divisiones = data; })
    .catch(err => console.log("Error al conectar con la BD."));
}

window.addEventListener('DOMContentLoaded', async () => {
  const formAuth = document.getElementById('form-auth');
  formAuth.addEventListener('submit', enviarFormularioAuth);
  document.getElementById('auth-cambiar').addEventListener('click', () => mostrarFormularioRegistro(!modoRegistro));
  document.getElementById('auth-cerrar').addEventListener('click', () => {
    cerrarSesion().catch(error => alert(error.message));
  });

  try {
    const respuesta = await fetch(api('auth/status'));
    const resultado = await leerRespuestaAuth(respuesta);
    if (!respuesta.ok) throw new Error(resultado.error || 'No se pudo verificar la sesión.');
    if (resultado.autenticado) mostrarAplicacion(resultado.usuario, resultado.rol);
  } catch (error) {
    document.getElementById('auth-mensaje').textContent = error.message || 'No se pudo conectar con el servidor.';
  }
});