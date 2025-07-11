document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formSign");
    const emailInput = document.getElementById("email");
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");

    let correoValido = false;

    // Crear mensaje de ayuda para el correo
    const emailHelp = document.createElement("small");
    emailHelp.className = "text-danger d-none";
    emailInput.parentNode.appendChild(emailHelp);

    // Validación de correo con API externa
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

    // Cargar distritos al cambiar provincia
    provinciaSelect.addEventListener("change", async () => {
        const id = provinciaSelect.value;
        distritoSelect.innerHTML = `<option value="">${
            id ? "Cargando..." : "Seleccionar Distrito"
        }</option>`;
        if (!id) return;

        try {
            const res = await fetch(`/provincias/${id}/distritos`);
            const distritos = await res.json();

            distritoSelect.innerHTML =
                '<option value="">Seleccionar Distrito</option>';
            distritos.forEach(({ distrito_id, nombre }) => {
                distritoSelect.insertAdjacentHTML(
                    "beforeend",
                    `<option value="${distrito_id}">${nombre}</option>`
                );
            });
        } catch {
            distritoSelect.innerHTML =
                '<option value="">Error al cargar</option>';
        }
    });

    // Validación y envío del formulario
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        form.classList.add("was-validated");

        if (!form.checkValidity() || !correoValido) {
            let errores = [];

            if (!form.checkValidity())
                errores.push("Completa todos los campos correctamente.");
            if (!correoValido) {
                errores.push("Debes ingresar un correo válido de Gmail.");
                emailHelp.classList.remove("d-none");
            }

            return Swal.fire({
                icon: "error",
                title: "Error al registrar",
                html: errores.join("<br>"),
                confirmButtonText: "Entendido",
            });
        }

        try {
            const res = await fetch("/acceso/signup", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: new FormData(form),
            });

            const data = await res.json();

            if (res.ok) {
                Swal.fire({
                    icon: "success",
                    title: "Registro exitoso",
                    text: "Verifica tu correo antes de continuar.",
                    confirmButtonText: "Aceptar",
                }).then(() => (window.location.href = data.redirect));
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Errores en el formulario",
                    html: Object.values(data.errors).flat().join("<br>"),
                    confirmButtonText: "Entendido",
                });
            }
        } catch {
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "No se pudo completar el registro.",
            });
        }
    });
});
