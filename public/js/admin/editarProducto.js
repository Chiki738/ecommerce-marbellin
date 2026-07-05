const form = document.getElementById("formEditarProducto");
const previewImg = document.querySelector("#previewImagen img");
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const imageInput = form?.querySelector('[name="imagen"]');
const field = (name) => form?.elements.namedItem(name);

document.querySelectorAll(".btnEditarProducto").forEach((btn) => {
    btn.addEventListener("click", () => {
        const { codigo, nombre, precio, categoria, descripcion, imagen } =
            btn.dataset;

        form.action = `/admin/productos/${codigo}`;
        field("codigo").value = codigo;
        field("nombre").value = nombre;
        field("precio").value = precio;
        field("categoria_id").value = categoria;
        field("descripcion").value = descripcion;

        if (imagen) {
            previewImg.src = imagen;
            previewImg.style.display = "block";
        } else {
            previewImg.src = "";
            previewImg.style.display = "none";
        }
    });
});

imageInput?.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewImg.style.display = "block";
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.src = "";
        previewImg.style.display = "none";
    }
});

form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    formData.append("_method", "PUT");

    fetch(form.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        body: formData,
    })
        .then((res) => res.json())
        .then((data) => {
            Swal.fire({
                icon: "success",
                title: "¡Actualizado!",
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
            });

            bootstrap.Modal.getInstance(
                document.getElementById("editarProducto")
            ).hide();

            const codigo = field("codigo").value.toLowerCase();
            const nombre = field("nombre").value;
            const precio = parseFloat(field("precio").value).toFixed(2);
            const categoria = field("categoria_id").selectedOptions[0].text;
            const descripcion = field("descripcion").value;

            const item = document.querySelector(
                `.producto-item[data-codigo="${codigo}"]`
            );
            if (item) {
                const button = item.querySelector(".accordion-button");
                button.querySelector(
                    "strong"
                ).textContent = `${codigo.toUpperCase()} - ${nombre}`;
                button.querySelector(
                    ".text-primary"
                ).textContent = `S/ ${precio}`;

                const body = item.querySelector(".accordion-body");
                body.querySelector(
                    "p:nth-of-type(1)"
                ).innerHTML = `<strong>Categoría:</strong> ${categoria}`;
                body.querySelector(
                    "p:nth-of-type(2)"
                ).innerHTML = `<strong>Descripción:</strong> ${descripcion}`;

                const nuevaImagen = imageInput?.files[0];
                if (nuevaImagen) {
                    const reader = new FileReader();
                    reader.onload = (e) =>
                        (item.querySelector("img").src = e.target.result);
                    reader.readAsDataURL(nuevaImagen);
                }
            }
        })
        .catch((err) => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: err.message || "Hubo un problema al actualizar",
            });
        });
});
