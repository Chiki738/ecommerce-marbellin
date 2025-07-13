document.addEventListener("DOMContentLoaded", () => {
    const pedidoId = document.getElementById("pedidoId")?.value;

    if (pedidoId) {
        paypal
            .Buttons({
                createOrder: (data, actions) => {
                    return actions.order.create({
                        purchase_units: [
                            {
                                amount: {
                                    value: document
                                        .getElementById("total-pedido")
                                        .textContent.replace(",", ""),
                                },
                            },
                        ],
                    });
                },
                onApprove: async (data, actions) => {
                    const resStock = await fetch(
                        `/carrito/verificar-stock/${pedidoId}`,
                        {
                            method: "GET",
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                        }
                    );
                    const stockData = await resStock.json();

                    if (!stockData.success) {
                        Swal.fire({
                            icon: "error",
                            html: stockData.errores.join("<br>"),
                            confirmButtonText: "Aceptar",
                        });
                        return;
                    }

                    await actions.order.capture();
                    try {
                        const res = await fetch(
                            `/pago/exito?pedido_id=${pedidoId}`,
                            {
                                method: "GET",
                                headers: {
                                    "X-Requested-With": "XMLHttpRequest",
                                },
                            }
                        );
                        const dataJson = await res.json();
                        Swal.fire({
                            icon: dataJson.success ? "success" : "error",
                            text: dataJson.success
                                ? "Tu pedido fue generado correctamente."
                                : "Error al actualizar el pedido.",
                            timer: 2500,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    } catch (error) {
                        console.error("Error en pago:", error);
                        Swal.fire({
                            icon: "error",
                            text: "Error al procesar el pago.",
                            timer: 2500,
                            showConfirmButton: false,
                        });
                    }
                },
                onCancel: () => {
                    Swal.fire({
                        icon: "error",
                        text: "El pago fue cancelado.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
            })
            .render("#paypal-button-container");
    }

    // Actualizar cantidad
    document.querySelectorAll(".form-actualizar").forEach((form) => {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const url = form.action;
            const formData = new FormData(form);
            const cantidad = formData.get("cantidad");

            try {
                const res = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": formData.get("_token"),
                    },
                    body: formData,
                });

                const data = await res.json();

                Swal.fire("Actualizado", data.message, "success");
                form
                    .closest(".border")
                    .querySelector(".cantidad-texto").textContent = cantidad;
                form
                    .closest(".border")
                    .querySelector(".subtotal-texto").textContent = parseFloat(
                    data.subtotal
                ).toFixed(2);
                document.getElementById("total-pedido").textContent =
                    parseFloat(data.total).toFixed(2);

                const resumenItem = [
                    ...document.querySelectorAll(".resumen-item"),
                ].find((item) => item.dataset.producto === data.producto);
                if (resumenItem) {
                    resumenItem.querySelector(".resumen-cantidad").textContent =
                        data.resumenCantidad;
                    resumenItem.querySelector(".resumen-subtotal").textContent =
                        parseFloat(data.resumenSubtotal).toFixed(2);
                }
            } catch (error) {
                Swal.fire("Error", "No se pudo actualizar", "error");
            }
        });
    });

    document.querySelectorAll(".form-eliminar").forEach((form) => {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const productoCard = form.closest(".card"); // Tarjeta del producto
            const resumenItem = form
                .closest(".card")
                ?.querySelector(".resumen-item");

            try {
                const res = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": formData.get("_token"),
                    },
                    body: formData,
                });

                const data = await res.json();

                Swal.fire(
                    "Eliminado",
                    "Producto eliminado del carrito",
                    "success"
                );

                // Eliminar el bloque del detalle
                const bloque = form.closest(".border");
                if (bloque) bloque.remove();

                // Si no quedan detalles dentro de la tarjeta, eliminarla
                const detallesRestantes =
                    productoCard.querySelectorAll(".border");
                if (detallesRestantes.length === 0) {
                    productoCard.remove();
                }

                // Actualizar total
                document.getElementById("total-pedido").textContent =
                    parseFloat(data.total).toFixed(2);

                // Si el total es 0, mostrar mensaje de carrito vacío
                if (parseFloat(data.total) === 0) {
                    document.querySelector(".col-lg-8").innerHTML = `
                    <div class="alert alert-info">Tu carrito está vacío. ¡Agrega productos para comenzar tu compra!</div>
                `;
                    const resumen = document.querySelector(
                        ".col-lg-4 .card-body"
                    );
                    if (resumen) resumen.innerHTML = "";
                }
            } catch (error) {
                Swal.fire("Error", "No se pudo eliminar el producto", "error");
            }
        });
    });

    // Vaciar carrito sin recargar
    const formVaciar = document.querySelector(".form-vaciar");
    if (formVaciar) {
        formVaciar.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(formVaciar);

            Swal.fire({
                icon: "warning",
                title: "¿Vaciar carrito?",
                text: "Se eliminarán todos los productos del carrito.",
                showCancelButton: true,
                confirmButtonText: "Sí, vaciar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(formVaciar.action, {
                            method: "POST",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": formData.get("_token"),
                            },
                            body: formData,
                        });

                        const data = await res.json();

                        Swal.fire(
                            "Carrito vacío",
                            "Todos los productos fueron eliminados",
                            "success"
                        );

                        // Limpiar contenido del carrito
                        document.querySelector(".col-lg-8").innerHTML = `
                        <div class="alert alert-info">Tu carrito está vacío. ¡Agrega productos para comenzar tu compra!</div>
                    `;

                        // Limpiar resumen
                        const resumen = document.querySelector(
                            ".col-lg-4 .card-body"
                        );
                        if (resumen) resumen.innerHTML = "";
                    } catch (error) {
                        Swal.fire(
                            "Error",
                            "No se pudo vaciar el carrito",
                            "error"
                        );
                    }
                }
            });
        });
    }
});
