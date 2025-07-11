document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formAgregarCarrito");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]').content;

        try {
            const res = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await res.json();
            const success = res.ok;

            Swal.fire({
                icon: success ? "success" : "error",
                title: success ? "¡Éxito!" : "¡Error!",
                text: data.message || data.error || "Algo salió mal",
                timer: 2500,
                showConfirmButton: false,
            });
        } catch (err) {
            console.error(err);
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
