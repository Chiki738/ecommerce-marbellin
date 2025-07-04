document.addEventListener("DOMContentLoaded", () => {
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");
    const emailInput = document.getElementById("email");
    const form = document.getElementById("formSign");

    let correoValido = false;

    // Mostrar ayuda visual del correo
    const emailHelp = Object.assign(document.createElement("small"), {
        className: "text-danger d-none",
    });
    emailInput.parentNode.appendChild(emailHelp);

    // Validar correo con API externa
    emailInput.addEventListener("blur", async () => {
        const email = emailInput.value.trim();
        if (!email) return;

        try {
            const res = await fetch(
                `http://apilayer.net/api/check?access_key=6d9af1afa51ac8b6c08c0bb56c79f40f&email=${encodeURIComponent(
                    email
                )}&smtp=1&format=1`
            );
            const data = await res.json();

            correoValido =
                data.format_valid &&
                data.smtp_check &&
                email.endsWith("@gmail.com");

            emailHelp.textContent = correoValido
                ? ""
                : "Correo inválido o no existe";
            emailHelp.classList.toggle("d-none", correoValido);
        } catch {
            correoValido = false;
            emailHelp.textContent = "Error al validar el correo";
            emailHelp.classList.remove("d-none");
        }
    });

    // Cargar distritos al seleccionar provincia
    provinciaSelect.addEventListener("change", async () => {
        const provinciaId = provinciaSelect.value;
        distritoSelect.innerHTML = `<option value="">${
            provinciaId ? "Cargando..." : "Seleccionar Distrito"
        }</option>`;
        if (!provinciaId) return;

        try {
            const res = await fetch(`/provincias/${provinciaId}/distritos`);
            const data = await res.json();

            distritoSelect.innerHTML =
                '<option value="">Seleccionar Distrito</option>';
            data.forEach((d) => {
                distritoSelect.insertAdjacentHTML(
                    "beforeend",
                    `<option value="${d.distrito_id}">${d.nombre}</option>`
                );
            });
        } catch {
            distritoSelect.innerHTML =
                '<option value="">Error al cargar</option>';
        }
    });

    // Validación completa del formulario y envío por AJAX
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        e.stopPropagation();
        form.classList.add("was-validated");

        if (!form.checkValidity() || !correoValido) {
            let mensaje = "";
            if (!form.checkValidity())
                mensaje += "Completa todos los campos correctamente.<br>";
            if (!correoValido) {
                mensaje += "Debes ingresar un correo válido de Gmail.";
                emailHelp.classList.remove("d-none");
            }

            Swal.fire({
                icon: "error",
                title: "Error al registrar",
                html: mensaje,
                confirmButtonText: "Entendido",
            });
            return;
        }

        const formData = new FormData(form);

        try {
            const res = await fetch("/acceso/signup", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: formData,
            });

            const data = await res.json();

            if (res.ok) {
                Swal.fire({
                    icon: "success",
                    title: "Registro exitoso",
                    text: "Verifica tu correo antes de continuar.",
                    confirmButtonText: "Aceptar",
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Errores en el formulario",
                    html: Object.values(data.errors).flat().join("<br>"),
                    confirmButtonText: "Entendido",
                });
            }
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "No se pudo completar el registro.",
            });
        }
    });
});
