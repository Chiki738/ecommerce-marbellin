document.addEventListener("DOMContentLoaded", () => {
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");
    const emailInput = document.getElementById("email");

    let correoValido = false;

    // Mostrar ayuda para el correo
    const emailHelp = Object.assign(document.createElement("small"), {
        className: "text-danger d-none",
    });
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

    // Validación de formulario incluyendo el correo
    document.querySelectorAll(".needs-validation").forEach((form) => {
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
                emailHelp.classList.add("d-none");
            }
            form.classList.add("was-validated");
        });
    });
});
