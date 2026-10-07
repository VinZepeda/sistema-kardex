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
// CALENDARIO
// ========================================

let fechaCalendario = new Date();


function generarCalendario() {

    const calendario = document.getElementById("calendario");

    const tituloMes = document.getElementById("mesActual");

    if (!calendario || !tituloMes) {

        return;

    }


    calendario.innerHTML = "";


    const año = fechaCalendario.getFullYear();

    const mes = fechaCalendario.getMonth();


    const nombresMeses = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    ];


    tituloMes.textContent =
        nombresMeses[mes] + " " + año;


    let primerDia = new Date(año, mes, 1).getDay();

    let ultimoDia = new Date(año, mes + 1, 0).getDate();


    // Convertir domingo = 0 a lunes = 0
    primerDia = primerDia === 0 ? 6 : primerDia - 1;


    // Espacios antes del primer día

    for (let i = 0; i < primerDia; i++) {

        const espacio = document.createElement("div");

        espacio.classList.add("calendar-day", "empty");

        calendario.appendChild(espacio);

    }


    // Días del mes

    for (let dia = 1; dia <= ultimoDia; dia++) {

        const elementoDia = document.createElement("div");

        elementoDia.classList.add("calendar-day");

        elementoDia.textContent = dia;


        const hoy = new Date();


        if (
            dia === hoy.getDate() &&
            mes === hoy.getMonth() &&
            año === hoy.getFullYear()
        ) {

            elementoDia.classList.add("today");

        }


        calendario.appendChild(elementoDia);

    }

}


function mesAnterior() {

    fechaCalendario.setMonth(
        fechaCalendario.getMonth() - 1
    );

    generarCalendario();

}


function mesSiguiente() {

    fechaCalendario.setMonth(
        fechaCalendario.getMonth() + 1
    );

    generarCalendario();

}


// ========================================
// BUSCAR EMPLEADO
// ========================================

function buscarEmpleado() {

    const input =
        document.getElementById("buscarPersonal");

    const tabla =
        document.getElementById("tablaPersonal");


    if (!input || !tabla) {

        return;

    }


    const texto =
        input.value.toLowerCase();


    const filas =
        tabla.getElementsByTagName("tr");


    for (let i = 0; i < filas.length; i++) {

        const contenido =
            filas[i].textContent.toLowerCase();


        if (contenido.includes(texto)) {

            filas[i].style.display = "";

        } else {

            filas[i].style.display = "none";

        }

    }

}


// ========================================
// INICIALIZACIÓN
// ========================================

document.addEventListener("DOMContentLoaded", function () {

    generarCalendario();

});