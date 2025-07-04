document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formAgregarCarrito");
    const toastEl = document.getElementById("successToast");
    const toastMsg = document.getElementById("successToastMsg");
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const token = document.querySelector('input[name="_token"]').value;

        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                toastMsg.innerText =
                    data.message || "Producto agregado al carrito";
                toast.show();
            } else {
                alert(data.error || "Error al agregar al carrito");
            }
        } catch (error) {
            console.error(error);
            alert("Ocurrió un error inesperado");
        }
    });
});
