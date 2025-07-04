document.addEventListener("DOMContentLoaded", () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // Eliminar producto
    document.querySelectorAll(".btnEliminarProducto").forEach((boton) => {
        boton.addEventListener("click", async () => {
            const url = boton.dataset.action;
            const confirmacion = await Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción no se puede deshacer",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
            });

            if (!confirmacion.isConfirmed) return;

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: new URLSearchParams({ _method: "DELETE" }),
                });

                if (!response.ok) throw new Error("No se pudo eliminar");

                Swal.fire({
                    icon: "success",
                    title: "Eliminado",
                    text: "Producto eliminado correctamente",
                    timer: 2000,
                    showConfirmButton: false,
                });

                boton.closest(".producto-item")?.remove();
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message || "Error al eliminar",
                });
            }
        });
    });

    // Actualización de cantidad y botones
    document.querySelectorAll(".form-actualizar-cantidad").forEach((form) => {
        const input = form.querySelector(".cantidad-input");
        const boton = form.querySelector(".actualizar-btn");
        const original = input.value;

        input.addEventListener("input", () => {
            boton.disabled = input.value === original;
        });

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const id = form.dataset.id;
            const cantidad = input.value;

            try {
                const response = await fetch(
                    `/admin/variantes/${id}/actualizar`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf,
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ cantidad }),
                    }
                );

                const data = await response.json();
                if (!response.ok)
                    throw new Error(data.message || "Error al actualizar");

                Swal.fire({
                    icon: "success",
                    title: "Actualizado",
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                });

                // Actualizar visualmente la fila
                const fila = form.closest("tr");
                fila.dataset.cantidad = cantidad;

                fila.className =
                    cantidad <= 5
                        ? "table-danger"
                        : cantidad <= 13
                        ? "table-warning"
                        : "table-success";

                boton.disabled = true;
                actualizarResumenStock();
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message,
                });
            }
        });
    });

    // Función para actualizar contadores y alerta
    function actualizarResumenStock() {
        const filas = document.querySelectorAll("tr[data-cantidad]");
        const stockBajo = new Set();
        const stockCritico = new Set();

        filas.forEach((fila) => {
            const cantidad = parseInt(fila.dataset.cantidad);
            const codigo = fila.dataset.producto;

            if (cantidad <= 5) stockCritico.add(codigo);
            else if (cantidad <= 13) stockBajo.add(codigo);
        });

        const bajoEl = document.querySelector(".stock-bajo-count");
        if (bajoEl) bajoEl.textContent = stockBajo.size;

        const criticoEl = document.querySelector(".stock-critico-count");
        if (criticoEl) criticoEl.textContent = stockCritico.size;

        const alerta = document.querySelector(".alert-warning");
        const lista = document.querySelector(".stock-alert-list");
        if (lista) lista.innerHTML = "";

        if (stockCritico.size || stockBajo.size) {
            alerta?.classList.add("show");
            alerta.style.display = "block";

            stockCritico.forEach((cod) =>
                lista?.insertAdjacentHTML(
                    "beforeend",
                    `<li>${cod} (stock crítico)</li>`
                )
            );
            stockBajo.forEach((cod) => {
                if (!stockCritico.has(cod)) {
                    lista?.insertAdjacentHTML(
                        "beforeend",
                        `<li>${cod} (stock bajo)</li>`
                    );
                }
            });
        } else {
            alerta?.classList.remove("show");
            alerta.style.display = "none";
        }
    }
});
