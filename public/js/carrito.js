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
});
