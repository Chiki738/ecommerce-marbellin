document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector(".buscador-autocompletado");
    const input = container?.querySelector("input");
    const resultados = document.getElementById("resultados-autocompletado");

    if (!input || !resultados) return;

    const renderResultados = (productos) => {
        if (!productos.length) {
            resultados.innerHTML =
                '<div class="p-2 text-muted">Sin resultados</div>';
            return;
        }

        resultados.innerHTML = productos
            .map(
                (p) => `
            <a href="/producto/${
                p.codigo
            }" class="d-flex align-items-center p-2 text-decoration-none text-dark border-bottom">
                <img src="${p.imagen}" alt="${
                    p.nombre
                }" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <div>
                    <strong>${p.nombre}</strong><br>
                    <small>S/ ${parseFloat(p.precio).toFixed(2)}</small>
                </div>
            </a>
        `
            )
            .join("");
    };

    input.addEventListener("input", async () => {
        const query = input.value.trim();

        if (query.length < 2) {
            resultados.style.display = "none";
            resultados.innerHTML = "";
            return;
        }

        try {
            const res = await fetch(
                `/productos/buscar?query=${encodeURIComponent(query)}`
            );
            const data = await res.json();

            renderResultados(data);
            resultados.style.display = "block";
        } catch (error) {
            console.error("Error al buscar:", error);
        }
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".buscador-autocompletado")) {
            resultados.style.display = "none";
        }
    });
});
