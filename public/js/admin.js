// Script para actualizar la vista previa en tiempo real
const inNombre = document.getElementById('input-nombre');
const inAtaque = document.getElementById('input-ataque');
const inDefensa = document.getElementById('input-defensa');
const inControl = document.getElementById('input-control');
const inImg = document.getElementById('input-img');

function actualizarPreview() {
    document.getElementById('prev-nombre').innerText = inNombre.value || 'Nombre';
    document.getElementById('prev-stats').innerText = `ATQ ${inAtaque.value || 0} · DEF ${inDefensa.value || 0} · CTR ${inControl.value || 0}`;
    if(inImg.value) document.getElementById('prev-img').src = inImg.value;
}

[inNombre, inAtaque, inDefensa, inControl, inImg].forEach(el => {
    el.addEventListener('input', actualizarPreview);
});
actualizarPreview();
