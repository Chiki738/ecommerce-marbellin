const distritosPorDepartamento = {
    Lima: ["Miraflores", "San Isidro", "Surco", "Callao", "Chosica"],
    Arequipa: ["Cayma", "Yanahuara", "Cerro Colorado"],
};

const departamentoSelect = document.getElementById("departamento");
const distritoSelect = document.getElementById("distrito");
const direccionInput = document.getElementById("direccion");

if (departamentoSelect && distritoSelect && direccionInput) {
    departamentoSelect.addEventListener("change", () => {
        const departamento = departamentoSelect.value;
        distritoSelect.innerHTML = `<option value="">Seleccionar</option>`;

        if (departamento && distritosPorDepartamento[departamento]) {
            distritosPorDepartamento[departamento].forEach((distrito) => {
                const option = document.createElement("option");
                option.value = distrito;
                option.textContent = distrito;
                distritoSelect.appendChild(option);
            });
        }

        direccionInput.value = "";
    });

    distritoSelect.addEventListener("change", () => {
        const departamento = departamentoSelect.value;
        const distrito = distritoSelect.value;
        if (departamento && distrito) {
            direccionInput.value = `${distrito}, ${departamento}`;
        }
    });
}
