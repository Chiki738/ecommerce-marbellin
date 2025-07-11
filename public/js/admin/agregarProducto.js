document.addEventListener("DOMContentLoaded", () => {
    const getToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.content || "";

    const mostrarAlerta = (icon, title, text, timer = 2000) => {
        Swal.fire({ icon, title, text, timer, showConfirmButton: false });
    };

    // 🔍 Buscador
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
        boton.addEventListener("click", async () => {
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
                const res = await fetch(boton.dataset.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": getToken(),
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
                boton.closest(".producto-item")?.remove();
            } catch (err) {
                mostrarAlerta(
                    "error",
                    "Error",
                    err.message || "Error al eliminar"
                );
            }
        });
    });

    // 📦 Actualizar cantidad
    document.querySelectorAll(".form-actualizar-cantidad").forEach((form) => {
        const input = form.querySelector(".cantidad-input");
        const btn = form.querySelector(".actualizar-btn");
        let original = input.value;

        input.addEventListener("input", () => {
            btn.disabled = input.value === original;
        });

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const id = form.dataset.id;
            const cantidad = input.value;

            try {
                const res = await fetch(`/admin/variantes/${id}/actualizar`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getToken(),
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

                original = cantidad;
                btn.disabled = true;
                actualizarResumenStock();
            } catch (err) {
                mostrarAlerta("error", "Error", err.message);
            }
        });
    });

    // 🔄 Actualizar resumen de stock
    function actualizarResumenStock() {
        const filas = document.querySelectorAll("tr[data-cantidad]");
        const critico = new Set(),
            bajo = new Set();
        let totalCritico = 0,
            totalBajo = 0;

        filas.forEach((fila) => {
            const { cantidad, producto, nombre } = fila.dataset;
            const valor = parseInt(cantidad);

            if (valor <= 5) {
                critico.add(`${nombre} (stock crítico)`);
                totalCritico++;
            } else if (valor <= 13) {
                if (!critico.has(`${nombre} (stock crítico)`)) {
                    bajo.add(`${nombre} (stock bajo)`);
                    totalBajo++;
                }
            }
        });

        document.querySelector(".stock-critico-count").textContent =
            totalCritico;
        document.querySelector(".stock-bajo-count").textContent = totalBajo;

        const alerta = document.querySelector(".alert-warning");
        const lista = document.querySelector(".stock-alert-list");

        if (!alerta || !lista) return;

        lista.innerHTML = "";

        if (critico.size || bajo.size) {
            alerta.classList.add("show");
            alerta.style.display = "block";
            critico.forEach((item) =>
                lista.insertAdjacentHTML("beforeend", `<li>${item}</li>`)
            );
            bajo.forEach((item) =>
                lista.insertAdjacentHTML("beforeend", `<li>${item}</li>`)
            );
        } else {
            alerta.classList.remove("show");
            alerta.style.display = "none";
        }
    }
});
