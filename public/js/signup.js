document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formSign");
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");

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

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        form.classList.add("was-validated");

        if (!form.checkValidity()) {
            return Swal.fire({
                icon: "error",
                title: "Error al registrar",
                text: "Completa todos los campos correctamente.",
                confirmButtonText: "Entendido",
            });
        }

        try {
            const res = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
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
