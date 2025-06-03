document.addEventListener("DOMContentLoaded", function () {
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");

    provinciaSelect.addEventListener("change", function () {
        const provinciaId = this.value;
        distritoSelect.innerHTML = provinciaId
            ? '<option value="">Cargando...</option>'
            : '<option value="">Seleccionar Distrito</option>';

        if (!provinciaId) return;

        fetch(`/provincias/${provinciaId}/distritos`)
            .then((res) => res.json())
            .then((data) => {
                distritoSelect.innerHTML =
                    '<option value="">Seleccionar Distrito</option>';
                data.forEach((d) => {
                    distritoSelect.innerHTML += `<option value="${d.distrito_id}">${d.nombre}</option>`;
                });
            })
            .catch(() => {
                distritoSelect.innerHTML =
                    '<option value="">Error al cargar</option>';
            });
    });

    // Validación Bootstrap
    (() => {
        "use strict";
        const forms = document.querySelectorAll(".needs-validation");
        forms.forEach((form) => {
            form.addEventListener("submit", (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add("was-validated");
            });
        });
    })();
});
