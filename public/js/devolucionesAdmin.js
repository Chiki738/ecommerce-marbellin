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
        .textContent.replace("#", "");
    const estadoSeleccionado = document.getElementById("accionProcesar").value;
    const comentarioAdmin = document.getElementById("comentarioAdmin").value;

    const producto = document.getElementById("nuevoProducto");
    const talla = document.getElementById("nuevaTalla");
    const color = document.getElementById("nuevoColor");

    const productoId = producto?.selectedOptions[0]?.dataset?.id ?? "";
    const tallaValor = talla?.value ?? "";
    const colorValor = color?.value ?? "";

    let varianteNuevaId = null;

    if (estadoSeleccionado === "cambiar") {
        if (!productoId || !tallaValor || !colorValor) {
            Swal.fire(
                "Faltan datos",
                "Completa todos los campos del nuevo producto.",
                "warning"
            );
            return;
        }
        varianteNuevaId = `${productoId}-${tallaValor}-${colorValor}`;
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
                estadoSeleccionado === "aprobar"
                    ? "Aprobado"
                    : estadoSeleccionado === "rechazar"
                    ? "Rechazado"
                    : "Cambiado",
            comentario_admin: comentarioAdmin,
            variante_nueva_id: varianteNuevaId,
            notificar: true, // 👈 esto permite que se envíe el correo
        }),
    })
        .then((res) => {
            if (!res.ok) throw new Error("Error al procesar solicitud");
            return res.json();
        })
        .then((data) => {
            Swal.fire({
                icon: "success",
                title: "¡Procesado!",
                text: data.message,
            }).then(() => location.reload());
        })
        .catch(() => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Ocurrió un problema al procesar la solicitud.",
            });
        });
}
