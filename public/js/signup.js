document.addEventListener("DOMContentLoaded", function () {
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");
    const emailInput = document.getElementById("email");

    // Validación de correo con API externa
    const emailHelp = document.createElement("small");
    emailHelp.className = "text-danger d-none";
    emailInput.parentNode.appendChild(emailHelp);
    let correoValido = false;

    emailInput.addEventListener("blur", () => {
        const email = emailInput.value.trim();
        if (!email) return;

        fetch(
            `http://apilayer.net/api/check?access_key=6d9af1afa51ac8b6c08c0bb56c79f40f&email=${encodeURIComponent(
                email
            )}&smtp=1&format=1`
        )
            .then((res) => res.json())
            .then((data) => {
                if (
                    data.format_valid &&
                    data.smtp_check &&
                    email.endsWith("@gmail.com")
                ) {
                    correoValido = true;
                    emailHelp.classList.add("d-none");
                } else {
                    correoValido = false;
                    emailHelp.textContent = "Correo inválido o no existe";
                    emailHelp.classList.remove("d-none");
                }
            })
            .catch(() => {
                correoValido = false;
                emailHelp.textContent = "Error al validar el correo";
                emailHelp.classList.remove("d-none");
            });
    });

    // Cargar distritos según provincia
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

    // Validación completa incluyendo correo
    (() => {
        "use strict";
        const forms = document.querySelectorAll(".needs-validation");
        forms.forEach((form) => {
            form.addEventListener("submit", (e) => {
                if (!form.checkValidity() || !correoValido) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!correoValido) {
                        emailHelp.textContent =
                            "Debe ingresar un correo válido de Gmail";
                        emailHelp.classList.remove("d-none");
                    }
                } else {
                    // Ocultar mensaje si todo está bien
                    emailHelp.classList.add("d-none");
                }
                form.classList.add("was-validated");
            });
        });
    })();
});
