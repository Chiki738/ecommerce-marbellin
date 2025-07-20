document.addEventListener("DOMContentLoaded", () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const mostrarAlerta = (tipo, titulo, texto, timer = 2000) => {
        Swal.fire({
            icon: tipo,
            title: titulo,
            text: texto,
            timer,
            showConfirmButton: false,
        });
    };

    // Eliminar producto
    document.querySelectorAll(".btnEliminarProducto").forEach((btn) => {
        btn.addEventListener("click", async () => {
            const { isConfirmed } = await Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción no se puede deshacer",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
            });

            if (!isConfirmed) return;

            try {
                const res = await fetch(btn.dataset.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: new URLSearchParams({ _method: "DELETE" }),
                });

                if (!res.ok) throw new Error("No se pudo eliminar");

                mostrarAlerta(
                    "success",
                    "Eliminado",
                    "Producto eliminado correctamente"
                );
                btn.closest(".producto-item")?.remove();
            } catch (err) {
                mostrarAlerta("error", "Error", err.message);
            }
        });
    });

    // Actualizar cantidad
    document.querySelectorAll(".form-actualizar-cantidad").forEach((form) => {
        const input = form.querySelector(".cantidad-input");
        const btn = form.querySelector(".actualizar-btn");
        const original = parseInt(input.value);

        input.addEventListener("input", () => {
            const actual = parseInt(input.value);
            btn.disabled = actual === original || isNaN(actual);
        });

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const id = form.dataset.id;
            const cantidad = parseInt(input.value);

            try {
                const res = await fetch(`/admin/variantes/${id}/actualizar`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrf,
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ cantidad }),
                });

                const data = await res.json();
                if (!res.ok)
                    throw new Error(data.message || "Error al actualizar");

                mostrarAlerta("success", "Actualizado", data.message, 1500);

                const fila = form.closest("tr");
                fila.dataset.cantidad = cantidad;

                fila.className =
                    cantidad <= 5
                        ? "table-danger"
                        : cantidad <= 13
                        ? "table-warning"
                        : "table-success";

                input.defaultValue = cantidad;
                btn.disabled = true;
                actualizarResumenStock();
            } catch (err) {
                mostrarAlerta("error", "Error", err.message);
            }
        });
    });

    // Actualizar contadores y alerta de stock
    function actualizarResumenStock() {
        const filas = document.querySelectorAll("tr[data-cantidad]");
        const stockCritico = new Set();
        const stockBajo = new Set();

        filas.forEach(({ dataset }) => {
            const cantidad = parseInt(dataset.cantidad);
            const codigo = dataset.producto;

            if (cantidad <= 5) stockCritico.add(codigo);
            else if (cantidad <= 13) stockBajo.add(codigo);
        });

        const criticoEl = document.querySelector(".stock-critico-count");
        const bajoEl = document.querySelector(".stock-bajo-count");

        if (criticoEl) criticoEl.textContent = stockCritico.size;
        if (bajoEl) bajoEl.textContent = stockBajo.size;

        const alerta = document.querySelector(".alert-warning");
        const lista = document.querySelector(".stock-alert-list");

        if (!alerta || !lista) return;

        lista.innerHTML = "";

        if (stockCritico.size || stockBajo.size) {
            alerta.classList.add("show");
            alerta.style.display = "block";

            stockCritico.forEach((cod) =>
                lista.insertAdjacentHTML(
                    "beforeend",
                    `<li>${cod} (stock crítico)</li>`
                )
            );
            stockBajo.forEach((cod) => {
                if (!stockCritico.has(cod)) {
                    lista.insertAdjacentHTML(
                        "beforeend",
                        `<li>${cod} (stock bajo)</li>`
                    );
                }
            });
        } else {
            alerta.classList.remove("show");
            alerta.style.display = "none";
        }
    }
});
