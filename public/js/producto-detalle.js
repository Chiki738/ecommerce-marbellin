document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formAgregarCarrito");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const token = form.querySelector('input[name="_token"]').value;

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

            Swal.fire({
                icon: response.ok ? "success" : "error",
                title: response.ok ? "¡Éxito!" : "¡Error!",
                text: data.message || data.error || "Algo salió mal",
                timer: 2500,
                showConfirmButton: false,
            });
        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: "error",
                title: "¡Error!",
                text: "Ocurrió un error inesperado",
                timer: 2500,
                showConfirmButton: false,
            });
        }
    });
});
