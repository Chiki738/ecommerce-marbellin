function abrirModal(button) {
    const data = JSON.parse(button.getAttribute("data-cambio"));

    document.getElementById("codigoSolicitud").textContent = `#${data.id}`;
    document.getElementById("modalCodigo").textContent = `#${data.id}`;
    document.getElementById("modalFecha").textContent = new Date(
        data.created_at
    ).toLocaleString();
    document.getElementById("modalCliente").textContent =
        data.pedido?.cliente?.nombre ?? "—";
    document.getElementById("modalEmail").textContent =
        data.pedido?.cliente?.email ?? "—";
    document.getElementById("modalEstado").textContent =
        data.estado ?? "Pendiente";
    document.getElementById("motivoCliente").textContent =
        data.comentario_cliente ?? "—";

    const producto = data.detalle?.producto?.nombre ?? "—";
    const talla = data.detalle?.variante?.talla ?? "—";
    const color = data.detalle?.variante?.color ?? "—";

    document.getElementById("productoOriginalInfo").innerHTML = `
        <div><strong>${producto}</strong></div>
        <div class="text-muted">Talla: ${talla}</div>
        <div class="text-muted">Color: ${color}</div>
    `;

    const modal = new bootstrap.Modal(document.getElementById("modalProcesar"));
    modal.show();
}

document.addEventListener("DOMContentLoaded", () => {
    const accion = document.getElementById("accionProcesar");
    accion.addEventListener("change", () => {
        const mostrar = accion.value === "cambiar";
        document.getElementById("seccionNuevoProducto").style.display = mostrar
            ? "block"
            : "none";
        document.getElementById("seccionVariantes").style.display = mostrar
            ? "block"
            : "none";
    });
});

function procesarSolicitud() {
    const id = document
        .getElementById("modalCodigo")
        .textContent.replace("#", "")
        .trim();
    const estadoSeleccionado = document.getElementById("accionProcesar").value;
    const comentarioAdmin = document.getElementById("comentarioAdmin").value;

    let varianteNuevaId = null;

    if (estadoSeleccionado === "cambiar") {
        const productoId = document
            .getElementById("nuevoProducto")
            .value.trim();
        const talla = document.getElementById("nuevaTalla").value.trim();
        const color = document.getElementById("nuevoColor").value.trim();

        if (!productoId || !talla || !color) {
            Swal.fire(
                "Faltan datos",
                "Debes seleccionar producto, talla y color para procesar el cambio.",
                "warning"
            );
            return;
        }

        varianteNuevaId = `${productoId}-${talla}-${color}`;
    }

    fetch(`/admin/cambios/${id}/procesar`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({
            estado:
                estadoSeleccionado === "cambiar"
                    ? "Cambiado"
                    : estadoSeleccionado === "aprobar"
                    ? "Aprobado"
                    : "Rechazado",
            comentario_admin: comentarioAdmin,
            variante_nueva_id: varianteNuevaId,
            notificar: true,
        }),
    })
        .then((res) => res.json())
        .then((data) => {
            Swal.fire("Listo", data.message, "success").then(() => {
                location.reload();
            });
        })
        .catch(() => {
            Swal.fire(
                "Error",
                "Ocurrió un error al procesar la solicitud.",
                "error"
            );
        });
}
