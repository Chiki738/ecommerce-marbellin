document.addEventListener("DOMContentLoaded", function () {
    const input = document.querySelector(".buscador-autocompletado input");
    const resultados = document.getElementById("resultados-autocompletado");

    input.addEventListener("input", function () {
        const query = this.value.trim();

        if (query.length < 2) {
            resultados.style.display = "none";
            resultados.innerHTML = "";
            return;
        }

        fetch(`/buscar?query=${encodeURIComponent(query)}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.length === 0) {
                    resultados.innerHTML =
                        '<div class="p-2 text-muted">Sin resultados</div>';
                } else {
                    resultados.innerHTML = data
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
                                <small>S/ ${parseFloat(p.precio).toFixed(
                                    2
                                )}</small>
                            </div>
                        </a>
                    `
                        )
                        .join("");
                }
                resultados.style.display = "block";
            });
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".buscador-autocompletado")) {
            resultados.style.display = "none";
        }
    });
});
