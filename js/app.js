// ========================================
// MENÚ LATERAL
// ========================================

function alternarMenu() {

    const sidebar = document.getElementById("sidebar");

    if (sidebar) {

        sidebar.classList.toggle("sidebar-hidden");

    }

}


// ========================================
// BUSCAR EMPLEADO
// ========================================

function buscarEmpleado() {

    const input =
        document.getElementById("buscarPersonal");

    const tabla =
        document.getElementById("tablaPersonal");

    const contador =
        document.getElementById(
            "contadorResultados"
        );

    if (!input || !tabla) {
        return;
    }

    const texto =
        input.value.toLowerCase();

    const filas =
        tabla.getElementsByTagName("tr");

    let visibles = 0;

    for (let i = 0; i < filas.length; i++) {

        const contenido =
            filas[i].textContent.toLowerCase();

        if (contenido.includes(texto)) {

            filas[i].style.display = "";
            visibles++;

        } else {

            filas[i].style.display = "none";

        }

    }

    if (contador) {
        contador.textContent = visibles;
    }
}

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const input =
            document.getElementById(
                "buscarPersonal"
            );

        if (input) {

            buscarEmpleado();

            input.addEventListener(
                "input",
                buscarEmpleado
            );

        }

    }
);


// MENÚ DESPLEGABLE DE PERFIL

const profileButton = document.getElementById("profileButton");
const profileDropdown = document.getElementById("profileDropdown");

if (profileButton && profileDropdown) {

    profileButton.addEventListener("click", function(event) {

        event.stopPropagation();

        profileDropdown.classList.toggle("show");

    });

    document.addEventListener("click", function(event) {

        if (!profileDropdown.contains(event.target)) {

            profileDropdown.classList.remove("show");

        }

    });

}
