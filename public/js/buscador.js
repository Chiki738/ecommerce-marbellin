document.addEventListener("DOMContentLoaded", () => {
    const input = document.querySelector(".buscador-autocompletado input");
    const resultados = document.getElementById("resultados-autocompletado");

    if (!input || !resultados) return;

    input.addEventListener("input", async () => {
        const query = input.value.trim();

        if (query.length < 2) {
            resultados.style.display = "none";
            resultados.innerHTML = "";
            return;
        }

        try {
            const response = await fetch(
                `/productos/buscar?query=${encodeURIComponent(query)}`
            );
            const data = await response.json();

            resultados.innerHTML = data.length
                ? data
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
                      .join("")
                : '<div class="p-2 text-muted">Sin resultados</div>';

            resultados.style.display = "block";
        } catch (error) {
            console.error("Error al buscar:", error);
        }
    });

    // Cierra resultados si se hace clic fuera
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".buscador-autocompletado")) {
            resultados.style.display = "none";
        }
    });
});
