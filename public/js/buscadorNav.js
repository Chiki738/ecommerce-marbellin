document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector(".buscador-autocompletado");
    const input = container?.querySelector("input");
    const resultados = document.getElementById("resultados-autocompletado");

    if (!input || !resultados) return;

    let debounceTimer;

    const mostrarResultados = (html) => {
        resultados.innerHTML = html;
        resultados.style.display = "block";
    };

    const ocultarResultados = () => {
        resultados.style.display = "none";
        resultados.innerHTML = "";
    };

    const crearHTMLProducto = ({ codigo, nombre, precio, imagen }) => `
        <a href="/producto/${codigo}" class="d-flex align-items-center p-2 text-decoration-none text-dark border-bottom">
            <img src="${imagen}" alt="${nombre}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
            <div>
                <strong>${nombre}</strong><br>
                <small>S/ ${(+precio).toFixed(2)}</small>
            </div>
        </a>
    `;

    const renderResultados = (productos) => {
        const html = productos.length
            ? productos.map(crearHTMLProducto).join("")
            : '<div class="p-2 text-muted">Sin resultados</div>';
        mostrarResultados(html);
    };

    const buscarProductos = async (query) => {
        try {
            const res = await fetch(
                `/productos/autocomplete?query=${encodeURIComponent(query)}`
            );
            if (!res.ok) throw new Error("Error de red");
            const data = await res.json();
            renderResultados(data);
        } catch (err) {
            console.error("Error en búsqueda:", err);
            ocultarResultados();
        }
    };

    input.addEventListener("input", () => {
        const query = input.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) return ocultarResultados();

        debounceTimer = setTimeout(() => buscarProductos(query), 300);
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".buscador-autocompletado")) ocultarResultados();
    });
});
