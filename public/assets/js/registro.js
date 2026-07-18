document.addEventListener('DOMContentLoaded', function () {
    var botones = document.querySelectorAll('.tabs__boton');
    var paneles = document.querySelectorAll('.tabs__panel');

    botones.forEach(function (boton) {
        boton.addEventListener('click', function () {
            var destino = boton.dataset.tab;
            botones.forEach(function (b) { b.classList.toggle('tabs__boton--activo', b === boton); });
            paneles.forEach(function (p) { p.classList.toggle('tabs__panel--activo', p.dataset.panel === destino); });
        });
    });

    var camposRol = {
        voluntario: document.querySelector('[data-campo-rol="voluntario"]'),
        organizacion: document.querySelector('[data-campo-rol="organizacion"]'),
    };
    var camposInput = {
        voluntario: document.getElementById('registro-nombre-completo'),
        organizacion: document.getElementById('registro-nombre-organizacion'),
    };
    var radiosRol = document.querySelectorAll('input[name="rol"]');

    function actualizarCamposRol() {
        var seleccionado = document.querySelector('input[name="rol"]:checked');
        var rol = seleccionado ? seleccionado.value : 'voluntario';

        Object.keys(camposRol).forEach(function (clave) {
            var visible = clave === rol;
            camposRol[clave].classList.toggle('oculto', !visible);
            camposInput[clave].required = visible;
        });
    }

    radiosRol.forEach(function (radio) {
        radio.addEventListener('change', actualizarCamposRol);
    });

    actualizarCamposRol();
});
