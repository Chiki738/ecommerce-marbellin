document.addEventListener("DOMContentLoaded", async () => {
    const departamentoSelect = document.getElementById("departamento");
    const provinciaSelect = document.getElementById("provincia");
    const distritoSelect = document.getElementById("distrito");

    try {
        // Cargar los departamentos
        const response = await fetch("/ubigeo/departamentos");
        const departamentos = await response.json();

        departamentos.forEach((dep) => {
            const option = new Option(dep.nombre, dep.idDepartamento);
            departamentoSelect.add(option);
        });

        // Cuando cambia el departamento
        departamentoSelect.addEventListener("change", async () => {
            provinciaSelect.innerHTML =
                '<option value="">Seleccionar Provincia</option>';
            distritoSelect.innerHTML =
                '<option value="">Seleccionar Distrito</option>';

            const selectedDep = departamentoSelect.value;
            if (selectedDep) {
                const responseProvincias = await fetch(
                    `/ubigeo/provincias/${selectedDep}`
                );
                const provincias = await responseProvincias.json();

                provincias.forEach((prov) => {
                    const option = new Option(prov.nombre, prov.idProvincia);
                    provinciaSelect.add(option);
                });
            }
        });

        // Cuando cambia la provincia
        provinciaSelect.addEventListener("change", async () => {
            distritoSelect.innerHTML =
                '<option value="">Seleccionar Distrito</option>';

            const selectedProv = provinciaSelect.value;
            if (selectedProv) {
                const responseDistritos = await fetch(
                    `/ubigeo/distritos/${selectedProv}`
                );
                const distritos = await responseDistritos.json();

                distritos.forEach((dist) => {
                    const option = new Option(dist.nombre, dist.idDistrito);
                    distritoSelect.add(option);
                });
            }
        });
    } catch (error) {
        console.error("Error al cargar los datos de ubigeo:", error);
    }
});
