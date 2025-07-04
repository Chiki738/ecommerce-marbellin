document.addEventListener("DOMContentLoaded", function () {
    // 🔍 Buscador de productos
    document
        .getElementById("buscarProducto")
        ?.addEventListener("input", function () {
            const filtro = this.value.trim().toLowerCase();
            document.querySelectorAll(".producto-item").forEach((item) => {
                const nombre = item.dataset.nombre || "";
                const codigo = item.dataset.codigo || "";
                item.style.display =
                    nombre.includes(filtro) || codigo.includes(filtro)
                        ? ""
                        : "none";
            });
        });

    // 🗑️ Eliminar producto
    document.querySelectorAll(".btnEliminarProducto").forEach((boton) => {
        boton.addEventListener("click", async function () {
            const url = this.dataset.action;

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
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: new URLSearchParams({
                        _method: "DELETE",
                    }),
                });

                if (!response.ok) throw new Error("No se pudo eliminar");

                Swal.fire({
                    icon: "success",
                    title: "Eliminado",
                    text: "Producto eliminado correctamente",
                    timer: 2000,
                    showConfirmButton: false,
                });

                this.closest(".producto-item")?.remove();
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message || "Error al eliminar",
                });
            }
        });
    });

    // 📦 Actualizar cantidad en variantes
    document.querySelectorAll(".form-actualizar-cantidad").forEach((form) => {
        const input = form.querySelector(".cantidad-input");
        const boton = form.querySelector(".actualizar-btn");
        let original = input.value;

        // Habilitar botón si cambia valor
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
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                        },
                        body: JSON.stringify({
                            cantidad,
                        }),
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

                // Actualizar visualmente
                const fila = form.closest("tr");
                fila.dataset.cantidad = cantidad;

                fila.className =
                    cantidad <= 5
                        ? "table-danger"
                        : cantidad <= 13
                        ? "table-warning"
                        : "table-success";

                actualizarResumenStock();
                original = cantidad;
                boton.disabled = true;
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message,
                });
            }
        });
    });

    function actualizarResumenStock() {
        const filas = document.querySelectorAll("tr[data-cantidad]");
        let totalStockBajo = 0;
        let totalStockCritico = 0;

        const stockCriticoProductos = new Set();
        const stockBajoProductos = new Set();

        filas.forEach((fila) => {
            const cantidad = parseInt(fila.dataset.cantidad);
            const codigo = fila.dataset.producto;
            const nombre = fila.dataset.nombre;

            if (cantidad <= 5) {
                totalStockCritico++;
                stockCriticoProductos.add(nombre + " (stock crítico)");
            } else if (cantidad <= 13) {
                totalStockBajo++;
                if (!stockCriticoProductos.has(nombre + " (stock crítico)")) {
                    stockBajoProductos.add(nombre + " (stock bajo)");
                }
            }
        });

        document.querySelector(".stock-bajo-count").textContent =
            totalStockBajo;
        document.querySelector(".stock-critico-count").textContent =
            totalStockCritico;

        const alerta = document.querySelector(".alert-warning");
        const lista = document.querySelector(".stock-alert-list");
        lista.innerHTML = "";

        if (stockCriticoProductos.size || stockBajoProductos.size) {
            alerta?.classList.add("show");
            alerta.style.display = "block";

            stockCriticoProductos.forEach((item) => {
                lista.innerHTML += `<li>${item}</li>`;
            });
            stockBajoProductos.forEach((item) => {
                lista.innerHTML += `<li>${item}</li>`;
            });
        } else {
            alerta?.classList.remove("show");
            alerta.style.display = "none";
        }
    }
});
