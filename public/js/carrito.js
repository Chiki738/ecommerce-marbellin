function showAlert(message, type = "info") {
    const container = document.getElementById("alert-container");
    if (!container) return;

    const alert = document.createElement("div");
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 4000);
}

async function actualizarCantidad(event) {
    event.preventDefault();
    const form = event.target;
    const id = form.dataset.id;
    const cantidad = form.querySelector('[name="cantidad"]').value;

    try {
        const res = await fetch(`/carrito/actualizar/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({ cantidad }),
        });

        if (res.ok) {
            showAlert("✅ Cantidad actualizada", "success");
            setTimeout(() => location.reload(), 700);
        } else {
            const data = await res.json();
            showAlert(`❌ ${data.error || "Error al actualizar"}`, "danger");
        }
    } catch (error) {
        showAlert("❌ Error inesperado al actualizar", "danger");
    }
}

async function eliminarProducto(event) {
    event.preventDefault();
    const form = event.target.closest("form");
    const id = form.dataset.id;

    try {
        const res = await fetch(`/carrito/eliminar/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });

        if (res.ok) {
            showAlert("🗑️ Producto eliminado", "warning");
            setTimeout(() => location.reload(), 700);
        } else {
            const data = await res.json();
            showAlert(`❌ ${data.error || "Error al eliminar"}`, "danger");
        }
    } catch (error) {
        showAlert("❌ Error inesperado al eliminar", "danger");
    }
}

async function vaciarCarrito(event) {
    event.preventDefault();

    try {
        const res = await fetch(`/carrito/vaciar`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });

        if (res.ok) {
            showAlert("🧹 Carrito vaciado", "info");
            setTimeout(() => location.reload(), 700);
        } else {
            const data = await res.json();
            showAlert(`❌ ${data.error || "Error al vaciar"}`, "danger");
        }
    } catch (error) {
        showAlert("❌ Error inesperado al vaciar", "danger");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    // Actualizar cantidad
    document.querySelectorAll(".form-actualizar").forEach((form) => {
        form.addEventListener("submit", actualizarCantidad);
    });

    // Eliminar producto
    document.querySelectorAll(".form-eliminar").forEach((form) => {
        form.addEventListener("submit", eliminarProducto);
    });

    // Vaciar carrito
    const formVaciar = document.querySelector(".form-vaciar");
    if (formVaciar) {
        formVaciar.addEventListener("submit", vaciarCarrito);
    }

    // Botón de PayPal
    if (typeof paypal !== "undefined" && window.marbellinData) {
        paypal
            .Buttons({
                createOrder: (data, actions) =>
                    actions.order.create({
                        purchase_units: [
                            {
                                amount: { value: window.marbellinData.total },
                            },
                        ],
                    }),
                onApprove: (data, actions) =>
                    actions.order.capture().then(() => {
                        fetch(
                            `/pago/exito?pedido_id=${window.marbellinData.pedidoId}`,
                            {
                                headers: {
                                    "X-Requested-With": "XMLHttpRequest",
                                },
                            }
                        )
                            .then((res) => res.json())
                            .then((data) => {
                                if (data.success) {
                                    showAlert(
                                        "✅ Pedido generado correctamente",
                                        "success"
                                    );
                                    setTimeout(
                                        () => (location.href = "/carrito"),
                                        1000
                                    );
                                } else {
                                    showAlert(
                                        "❌ Error al actualizar pedido",
                                        "danger"
                                    );
                                }
                            })
                            .catch(() => {
                                showAlert(
                                    "❌ Error inesperado al procesar el pago",
                                    "danger"
                                );
                            });
                    }),
                onCancel: () => (location.href = "/pago/cancelado"),
            })
            .render("#paypal-button-container");
    }
});
