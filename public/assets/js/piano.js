console.log("📦 piano.js CARGADO");

/* =========================
   VARIABLES GLOBALES
   ========================= */
let grabando = false;
let contadorNotas = 0;
let startTime = 0;
let idSecuencia = null;

let notasPendientes = [];
let keyDownTimes = {};
let activeNotes = {};
let playbackActiveNotes = {};

const DURACION_MIN_MS = 40;
const DURACION_MAX_MS = 3000;
const limiteTiempo = 30000;

/* =========================
   MAPA DE TECLAS
   ========================= */
const keyMap = {
  'z': 'c3',   's': 'c-3',
  'x': 'd3',   'd': 'd-3',
  'c': 'e3',
  'v': 'f3',   'g': 'f-3',
  'b': 'g3',   'h': 'g-3',
  'n': 'a3',   'j': 'a-3',
  'm': 'b3',

  'q': 'c4',   '2': 'c-4',
  'w': 'd4',   '3': 'd-4',
  'e': 'e4',
  'r': 'f4',   '5': 'f-4',
  't': 'g4',   '6': 'g-4',
  'y': 'a4',   '7': 'a-4',
  'u': 'b4',

  'i': 'c5',
};

const noteIdToName = {
  1:"c3",2:"c-3",3:"d3",4:"d-3",5:"e3",
  6:"f3",7:"f-3",8:"g3",9:"g-3",
  10:"a3",11:"a-3",12:"b3",
  13:"c4",14:"c-4",15:"d4",16:"d-4",17:"e4",
  18:"f4",19:"f-4",20:"g4",21:"g-4",
  22:"a4",23:"a-4",24:"b4"
};

/* =========================
   WEB AUDIO
   ========================= */
const SAMPLE_PATH = "/public/assets/sounds/";
let audioCtx = null;
let sampleCache = {};

function ensureAudioContext() {
  if (!audioCtx) {
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
  }
  return audioCtx;
}

async function loadSample(note) {
  if (sampleCache[note]) return sampleCache[note];

  const resp = await fetch(`${SAMPLE_PATH}${note}.mp3`);
  const buf = await resp.arrayBuffer();
  const audioBuf = await ensureAudioContext().decodeAudioData(buf);

  sampleCache[note] = audioBuf;
  return audioBuf;
}

/* =========================
   TECLAS
   ========================= */
function activarTecla(note) {
  document.querySelectorAll(`[data-note="${note}"]`)
    .forEach(t => t.classList.add("active"));
}

function desactivarTecla(note) {
  document.querySelectorAll(`[data-note="${note}"]`)
    .forEach(t => t.classList.remove("active"));
}

/* =========================
   AUDIO EN VIVO
   ========================= */
function playLiveNote(note) {
  if (activeNotes[note]) return;

  const buffer = sampleCache[note];
  if (!buffer) return;

  const ctx = ensureAudioContext();
  const source = ctx.createBufferSource();
  const gain = ctx.createGain();

  source.buffer = buffer;
  source.connect(gain);
  gain.connect(ctx.destination);

  const now = ctx.currentTime;
  gain.gain.setValueAtTime(0, now);
  gain.gain.linearRampToValueAtTime(1, now + 0.01);

  source.start(now);
  activeNotes[note] = { source, gain };
}

function stopLiveNote(note) {
    const active = activeNotes[note];
    if (!active) return;

    const ctx = ensureAudioContext();
    const now = ctx.currentTime;

    // 🔊 cola más musical
    active.gain.gain.cancelScheduledValues(now);
    active.gain.gain.setValueAtTime(active.gain.gain.value, now);
    active.gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);

    active.source.stop(now + 0.65);

    delete activeNotes[note];
}


/* =========================
   NOTAS
   ========================= */
function noteOn(nota) {
  activarTecla(nota);
  playLiveNote(nota);

  if (grabando) {
    keyDownTimes[nota] = performance.now();
  }
}

function noteOff(nota) {
    if (!keyDownTimes[nota]) {
        desactivarTecla(nota);
        stopLiveNote(nota);
        return;
    }


  let duracion = performance.now() - keyDownTimes[nota];
  duracion = Math.max(DURACION_MIN_MS, Math.min(DURACION_MAX_MS, duracion));

  if (grabando) {
    registrarNota(nota, duracion);
  }

  delete keyDownTimes[nota];
  desactivarTecla(nota);
  stopLiveNote(nota);
}

/* =========================
   EVENTOS TECLADO
   ========================= */
document.addEventListener("keydown", e => {
    if (e.repeat) return; // 🔒 CLAVE PARA TRINOS
    const nota = keyMap[e.key];
    if (nota) noteOn(nota);
});

document.addEventListener("keyup", e => {
  const nota = keyMap[e.key];
  if (nota) noteOff(nota);
});

/* =========================
   GRABACIÓN
   ========================= */
function iniciarGrabacion() {
  if (!usuarioLogueado) {
    alert("Debes iniciar sesión para grabar.");
    return;
  }

  grabando = true;
  contadorNotas = 0;
  notasPendientes = [];
  idSecuencia = null;
  startTime = performance.now();
  
  const btnGrabar = document.getElementById("btn-grabar");
  const btnParar  = document.getElementById("btn-parar");

  if (btnGrabar) btnGrabar.disabled = true;
  if (btnParar)  btnParar.disabled  = false;


  fetch("/api/crear_secuencia.php", {
    method: "POST",
    credentials: "same-origin"
  })
  .then(r => r.json())
  .then(data => {
    idSecuencia = data.idSecuencia;
    setTimeout(() => grabando && pararGrabacion(), limiteTiempo);
  })
  .catch(() => {
    grabando = false;
    alert("No se pudo iniciar la grabación");
  });
}

async function pararGrabacion() {
  const btnGrabar = document.getElementById("btn-grabar");
  const btnParar  = document.getElementById("btn-parar");

  if (btnParar)  btnParar.disabled  = true;
  if (btnGrabar) btnGrabar.disabled = false;

  if (!grabando) return;
  grabando = false;

  const notasAbiertas = { ...keyDownTimes };
  keyDownTimes = {};

  for (const nota in notasAbiertas) {
    let duracion = performance.now() - notasAbiertas[nota];
    duracion = Math.max(DURACION_MIN_MS, Math.min(DURACION_MAX_MS, duracion));
    registrarNota(nota, duracion);
}

  keyDownTimes = {};

  const titulo = prompt("Ponle un nombre a tu canción:");
  if (!titulo) return;

  await fetch("/api/crear_cancion.php", {
    method: "POST",
    credentials: "same-origin",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ titulo, idSecuencia })
  });

  idSecuencia = null;
}

/* =========================
   REGISTRAR NOTA
   ========================= */
function registrarNota(nota, duracionMs) {
  contadorNotas++;
  const timestamp = performance.now() - startTime;

  fetch("/api/guardar_nota.php", {
    method: "POST",
    credentials: "same-origin",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      idSecuencia,
      nota,
      posicion: contadorNotas,
      timestamp,
      duracionMs
    })
  });
}

/* =========================
   REPRODUCCIÓN
   ========================= */
function playBufferNote(note, dur, when) {
  const ctx = ensureAudioContext();
  const source = ctx.createBufferSource();
  const gain = ctx.createGain();

  source.buffer = sampleCache[note];
  source.connect(gain);
  gain.connect(ctx.destination);

  gain.gain.setValueAtTime(1, when);
  gain.gain.linearRampToValueAtTime(0, when + dur / 1000 + 1.5);

  source.start(when);
  source.stop(when + dur / 1000 + 1.6);
}

async function reproducirSecuencia(id) {
  const res = await fetch(`/api/reproducir_secuencia.php?id=${id}`, {
    credentials: "same-origin"
  });

  const notas = await res.json();
  const names = [...new Set(notas.map(n => noteIdToName[n.nota]))];
  await Promise.all(names.map(loadSample));

  const start = ensureAudioContext().currentTime + 0.05;
  notas.forEach(n =>
    playBufferNote(noteIdToName[n.nota], n.duracionMs, start + n.timestamp / 1000)
  );
}

/* =========================
   BORRAR CANCIÓN
   ========================= */
function borrarCancion(idCancion, boton) {
  if (!confirm("¿Seguro que quieres borrar esta canción?")) return;

  fetch("/api/borrar_cancion.php", {
    method: "POST",
    credentials: "same-origin",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ idCancion })
  })
  .then(res => res.json())
  .then(data => {
    if (data.ok) {
      boton.closest("li")?.remove();
    } else {
      alert(data.error || "Error al borrar");
    }
  })
  .catch(() => alert("Error de comunicación con el servidor"));
}

/* =========================
   DOM
   ========================= */
document.addEventListener("DOMContentLoaded", async () => {
  sampleCache = {};
  await Promise.all(Object.values(keyMap).map(loadSample));

  document.querySelectorAll(".white, .black").forEach(tecla => {
    tecla.addEventListener("mousedown", () => noteOn(tecla.dataset.note));
    tecla.addEventListener("mouseup", () => noteOff(tecla.dataset.note));
    tecla.addEventListener("mouseleave", () => noteOff(tecla.dataset.note));
  });

  document.getElementById("btn-grabar")?.addEventListener("click", iniciarGrabacion);
  document.getElementById("btn-parar")?.addEventListener("click", pararGrabacion);
});

window.addEventListener("blur", () => {
    for (const nota in keyDownTimes) {
        noteOff(nota);
    }
});
