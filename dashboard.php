<?php

session_start();

require_once "conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION["usuario"];


$sql = "
    SELECT
        e.id_empleado,
        e.nombre,
        e.apellido_paterno,
        e.apellido_materno,
        e.curp,
        e.rfc,
        e.correo,
        e.telefono,
        e.fecha_nacimiento,
        e.fecha_ingreso,
        e.fecha_salida,
        e.horario,

        p.nombre AS puesto,
        a.nombre AS area,
        es.nombre AS estatus,
        pl.nombre AS plaza,
        n.nombre AS nivel

    FROM usuarios u

    INNER JOIN empleados e
        ON u.id_empleado = e.id_empleado

    LEFT JOIN puestos p
        ON e.id_puesto = p.id_puesto

    LEFT JOIN areas a
        ON e.id_area = a.id_area

    LEFT JOIN estatus es
        ON e.id_estatus = es.id_estatus

    LEFT JOIN plazas pl
        ON e.id_plaza = pl.id_plaza

    LEFT JOIN niveles n
        ON e.id_nivel = n.id_nivel

    WHERE u.usuario = ?
      AND u.activo = 1

    LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();
$empleado = $resultado->fetch_assoc();

$stmt->close();


if (!$empleado) {

    session_destroy();

    header("Location: index.php");
    exit();
}


$nombreCompleto = trim(
    $empleado["nombre"] . " " .
    $empleado["apellido_paterno"] . " " .
    $empleado["apellido_materno"]
);





$eventosCalendario = [];

$sqlCalendario = "
    SELECT
        c.id_calendario,
        c.id_empleado,
        c.fecha,
        c.tipo_dia,
        c.descripcion,

        e.nombre,
        e.apellido_paterno,
        e.apellido_materno

    FROM calendario_empleados c

    INNER JOIN empleados e
        ON c.id_empleado = e.id_empleado

    ORDER BY c.fecha ASC
";



$diasInhabiles = [];

$sqlInhabiles = "
SELECT
    fecha,
    descripcion,
    tipo
FROM dias_inhabiles
";

$resultadoInhabiles = $conexion->query($sqlInhabiles);

if($resultadoInhabiles){

    while($row = $resultadoInhabiles->fetch_assoc()){

        $diasInhabiles[] = $row;

    }

}



$resultadoCalendario = $conexion->query($sqlCalendario);

if ($resultadoCalendario) {

    while ($evento = $resultadoCalendario->fetch_assoc()) {

        $nombreEmpleado = trim(
            $evento["nombre"] . " " .
            ($evento["apellido_paterno"] ?? "") . " " .
            ($evento["apellido_materno"] ?? "")
        );


$tipo = "other";

switch ($evento["tipo_dia"]) {

    case "Día económico":
        $tipo = "economic";
        break;

    case "Incapacidad":
        $tipo = "incapacity";
        break;

    case "Vacaciones":
        $tipo = "vacation";
        break;

    case "Permiso":
        $tipo = "permission";
        break;

    case "Día inhábil":
        $tipo = "non-working";
        break;

    case "Festivo":
        $tipo = "holiday";
        break;

    default:
        $tipo = "other";
        break;
}



        $mio =
            ((int)$evento["id_empleado"] ===
             (int)$empleado["id_empleado"]);


        $eventosCalendario[] = [

            "id" =>
                (int)$evento["id_calendario"],

            "fecha" =>
                $evento["fecha"],

            "nombre" =>
                $nombreEmpleado,

            "tipo" =>
                $tipo,

            "tipoOriginal" =>
                $evento["tipo_dia"],

            "descripcion" =>
                $evento["descripcion"],

            "mio" =>
                $mio
        ];
    }
}


$diasInhabiles = [];

$sqlInhabiles = "
SELECT
    fecha,
    descripcion,
    tipo
FROM dias_inhabiles
";

$resultadoInhabiles =
    $conexion->query($sqlInhabiles);

if ($resultadoInhabiles) {

    while (
        $row =
            $resultadoInhabiles->fetch_assoc()
    ) {

        $diasInhabiles[] = $row;

    }

}


$titulo = "Dashboard";

require_once "includes/header.php";

include "includes/menu.php";

?>


<main class="main-content">


<?php

if (
    isset($_GET["justificante"])
) {

?>

<div
    style="
        background:#fff3cd;
        color:#856404;
        padding:15px;
        border-radius:10px;
        margin-bottom:20px;
        font-weight:bold;
    "
>

⚠️ Favor de marcar tus días de incapacidad
mencionados en tu justificante.
Sea honesto al registrarlos.

</div>

<?php

}

?>


    <div class="page-header">

        <div>

            <h2>
                Panel principal
            </h2>

            <p>
                Información de tu cuenta y datos laborales
            </p>

        </div>

    </div>

    <section class="welcome-card">

        <div>

            <h2>
                ¡Bienvenido,
                <?= htmlspecialchars($empleado["nombre"]) ?>!
            </h2>

            <p>
                Consulta la información registrada en tu Kardex.
            </p>

        </div>

    </section>


    <section class="dashboard-grid">


        <!-- NOMBRE -->

        <div class="dashboard-card">

            <div class="card-icon">
                👤
            </div>

            <div>

                <span class="card-label">
                    Nombre completo
                </span>

                <strong>
                    <?= htmlspecialchars($nombreCompleto) ?>
                </strong>

            </div>

        </div>


        <!-- PUESTO -->

        <div class="dashboard-card">

            <div class="card-icon">
                💼
            </div>

            <div>

                <span class="card-label">
                    Puesto
                </span>

                <strong>
                    <?= htmlspecialchars(
                        $empleado["puesto"] ?? "Sin asignar"
                    ) ?>
                </strong>

            </div>

        </div>


        <!-- ESTATUS -->

        <div class="dashboard-card">

            <div class="card-icon">
                📌
            </div>

            <div>

                <span class="card-label">
                    Estatus
                </span>

                <strong>
                    <?= htmlspecialchars(
                        $empleado["estatus"] ?? "Sin asignar"
                    ) ?>
                </strong>

            </div>

        </div>


    </section>


    <div class="calendar-top">


        <!-- CALENDARIO GENERAL -->

        <section class="calendar-info">

            <h2>
                Calendario General
            </h2>

            <p>
                Incapacidades, días económicos,
                días inhábiles y festivos.
            </p>

        </section>


        <section class="color-reference">

            <h3>
                Colores de referencia
            </h3>


            <div class="reference-items">


                <div class="reference-item">

                    <span class="reference-color my-economic"></span>

                    <span>
                        Tus días económicos
                    </span>

                </div>


                <div class="reference-item">

                    <span class="reference-color economic"></span>

                    <span>
                        Días económicos
                    </span>

                </div>


                <div class="reference-item">

                    <span class="reference-color incapacity"></span>

                    <span>
                        Incapacidades
                    </span>

                </div>


                <div class="reference-item">

                    <span class="reference-color non-working"></span>

                    <span>
                        Permisos informales
                    </span>

                </div>


                <div class="reference-item">

                    <span class="reference-color vacation"></span>

                    <span>
                        Vacaciones
                    </span>

                </div>


                <div class="reference-item">

                    <span class="reference-color holiday"></span>

                    <span>
                        Festivos
                    </span>

                </div>


                <div class="reference-item">

                    <span class="reference-warning">
                        ⚠️
                    </span>

                    <span>
                        Puentes en lunes y viernes
                    </span>

                </div>


            </div>

        </section>


    </div>


    <section class="calendar-container">


        <div class="calendar-header">


            <button
                type="button"
                class="calendar-button"
                onclick="mesAnterior()"
            >
                ←
            </button>


            <h3 id="mesActual">
                Octubre 2026
            </h3>


            <button
                type="button"
                class="calendar-button"
                onclick="mesSiguiente()">
                →
            </button>


        </div>

        <div class="calendar-week">

            <div>
                Lun
            </div>

            <div>
                Mar
            </div>

            <div>
                Mié
            </div>

            <div>
                Jue
            </div>

            <div>
                Vie
            </div>

            <div>
                Sáb
            </div>

            <div>
                Dom
            </div>

        </div>



        <div
            class="calendar-days"
            id="calendario"
        >

        </div>


    </section>


<div
    id="modalIncidencias"
    class="calendar-modal"
>

    <div class="calendar-modal-content">



        <div class="calendar-modal-header">

            <div>

                <span class="modal-small-title">
                    Incidencias del día
                </span>

                <h2 id="modalFecha">
                    Fecha
                </h2>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="cerrarModal()"
            >
                ×
            </button>

        </div>


            
        <div class="calendar-modal-body">





            <div class="modal-incidencias-section">

                <h3>
                    Incidencias registradas
                </h3>


                <div
                    id="listaIncidencias"
                    class="modal-incidencias-list"
                >

                    <div class="modal-empty">

                        📅

                        <p>
                            No hay incidencias registradas
                            para esta fecha.
                        </p>

                    </div>

                </div>

            </div>



            <div class="modal-register-section">

                <h3>
                    Registrar nueva incidencia
                </h3>


                <form
                    id="formIncidencia"
                >


                    <input
                        type="hidden"
                        name="fecha"
                        id="fechaIncidencia"
                    >


                    <input
                        type="hidden"
                        name="id_empleado"
                        value="<?= (int)$empleado["id_empleado"] ?>"
                    >


                    <div class="form-group">

                        <label for="tipoDia">
                            Tipo de incidencia
                        </label>

                        <select
                            name="tipo_dia"
                            id="tipoDia"
                            required
                        >

                            <option value="">
                                Selecciona una opción
                            </option>

                            <option value="Día económico">
                                 Día económico
                            </option>

                            <option value="Incapacidad">
                                Incapacidad
                            </option>

                            <option value="Vacaciones">
                                 Vacaciones
                            </option>

                            <option value="Permiso">
                                Permiso
                            </option>

                            <option value="Día inhábil">
                                Día inhábil
                            </option>

                            <option value="Festivo">
                                Festivo
                            </option>

                            <option value="Otro">
                                Otro
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            name="descripcion"
                            id="descripcion"
                            rows="3"
                            placeholder="Descripción opcional..."
                        ></textarea>

                    </div>


                    <div
                        id="mensajeIncidencia"
                        class="modal-message"
                    ></div>


                    <button
                        type="submit"
                        class="btn-registrar-incidencia"
                    >

                        + Registrar incidencia

                    </button>


                </form>

            </div>

        </div>

    </div>

</div>


   


</main>


<script>


const eventosCalendario = <?= json_encode(
    $eventosCalendario,
    JSON_UNESCAPED_UNICODE
) ?>;

const diasInhabiles =
<?= json_encode(
    $diasInhabiles,
    JSON_UNESCAPED_UNICODE
) ?>;




let fechaCalendario = new Date(2026, 9, 1);



const meses = [
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


function obtenerDiaInhabil(fecha){

    return diasInhabiles.find(
        item => item.fecha === fecha
    );

}



function generarCalendario() {


    const calendario =
        document.getElementById("calendario");


    const mesActual =
        document.getElementById("mesActual");


    calendario.innerHTML = "";


    const año =
        fechaCalendario.getFullYear();


    const mes =
        fechaCalendario.getMonth();


    mesActual.textContent =
        meses[mes] + " " + año;



    const primerDia =
        new Date(año, mes, 1);



    const ultimoDia =
        new Date(año, mes + 1, 0);



    let primerDiaSemana =
        primerDia.getDay() - 1;


    if (primerDiaSemana < 0) {

        primerDiaSemana = 6;

    }


    for (
        let i = 0;
        i < primerDiaSemana;
        i++
    ) {

        const espacio =
            document.createElement("div");

        espacio.className =
            "calendar-day empty";

        calendario.appendChild(espacio);

    }

    for (
        let dia = 1;
        dia <= ultimoDia.getDate();
        dia++
    ) {


        const celda =
            document.createElement("div");


        celda.className =
            "calendar-day";

        const mesNumero =
            String(mes + 1).padStart(2, "0");


        const diaNumero =
            String(dia).padStart(2, "0");


        const fecha =
            `${año}-${mesNumero}-${diaNumero}`;


        celda.dataset.fecha =
            fecha;

            celda.addEventListener(
    "click",
    function () {

        abrirModal(fecha);

    }
);




        const numeroDia =
            document.createElement("div");


        numeroDia.className =
            "calendar-day-number";


        numeroDia.textContent =
            dia;


        celda.appendChild(numeroDia);


        const eventos =
            eventosCalendario.filter(
                evento =>
                    evento.fecha === fecha
            );

const fechaObjeto =
    new Date(
        fecha + "T00:00:00"
    );

    const infoInhabil =
    obtenerDiaInhabil(
        fecha
    );

const diaSemana =
    fechaObjeto.getDay();



const esFinDeSemana =
    diaSemana === 0 ||
    diaSemana === 6;



if(infoInhabil){

    const etiqueta =
        document.createElement("div");

    etiqueta.className =
        "calendar-bridge";

    if(infoInhabil.tipo === "puente"){

        etiqueta.textContent =
            "⚠️ Puente";

    }
    else if(infoInhabil.tipo === "festivo"){

        etiqueta.textContent =
            "🎉 Festivo";

    }
    else{

        etiqueta.textContent =
            "🚫 Inhábil";

    }

    celda.appendChild(
        etiqueta
    );

    celda.classList.remove(
        "working"
    );

    celda.classList.add(
        "non-working"
    );

}








if (esFinDeSemana) {

    celda.classList.add(
        "non-working"
    );

}



if (!esFinDeSemana) {

    celda.classList.add(
        "working"
    );

}


if (infoInhabil) {

    celda.classList.remove(
        "working"
    );

    celda.classList.add(
        "non-working"
    );

    const aviso =
        document.createElement(
            "div"
        );

    aviso.className =
        "calendar-bridge";

    if (
        infoInhabil.tipo ===
        "puente"
    ) {

        aviso.textContent =
            "⚠️ Puente";

    }
    else if (
        infoInhabil.tipo ===
        "festivo"
    ) {

        aviso.textContent =
            "🎉 Festivo";

    }
    else {

        aviso.textContent =
            "🚫 Inhábil";

    }

    celda.appendChild(
        aviso
    );

}



        eventos.forEach(
            evento => {


                const empleado =
                    document.createElement("div");


                empleado.className =
                    "calendar-employee";




if (evento.tipo === "economic") {

    if (evento.mio) {

        empleado.classList.add(
            "employee-my-economic"
        );

    } else {

        empleado.classList.add(
            "employee-economic"
        );

    }

}

else if (evento.tipo === "incapacity") {

    empleado.classList.add(
        "employee-incapacity"
    );

}

else if (evento.tipo === "non-working") {

    empleado.classList.add(
        "employee-non-working"
    );

}

else if (evento.tipo === "holiday") {

    empleado.classList.add(
        "employee-holiday"
    );

}

else if (evento.tipo === "vacation") {

    empleado.classList.add(
        "employee-vacation"
    );

}

else if (evento.tipo === "permission") {

    empleado.classList.add(
        "employee-permission"
    );

}

else {

    empleado.classList.add(
        "employee-other"
    );

}

let icono = "📌";

switch (evento.tipo) {

    case "economic":
        icono = evento.mio ? "⭐" : "🟧";
        break;

    case "incapacity":
        icono = "🟪";
        break;

    case "non-working":
        icono = "🟢";
        break;

    case "holiday":
        icono = "🟣";
        break;

    case "vacation":
        icono = "🏖️";
        break;

    case "permission":
        icono = "🟨";
        break;

    default:
        icono = "📌";
        break;
}

                empleado.textContent =
                    icono + " " + evento.nombre;


                empleado.title =
                    evento.nombre;


                celda.appendChild(
                    empleado
                );

            }
        );


       

        if (eventos.length > 4) {


            const empleadosExtra =
                document.createElement("div");


            empleadosExtra.className =
                "calendar-more";


            empleadosExtra.textContent =
                "+" +
                (eventos.length - 4) +
                " empleados";


            celda.appendChild(
                empleadosExtra
            );

        }

        const hoy =
            new Date();


        if (
            dia === hoy.getDate() &&
            mes === hoy.getMonth() &&
            año === hoy.getFullYear()
        ) {

            celda.classList.add(
                "today"
            );

        }


        calendario.appendChild(
            celda
        );

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




function abrirModal(fecha) {



    const modal =
        document.getElementById(
            "modalIncidencias"
        );


    const modalFecha =
        document.getElementById(
            "modalFecha"
        );


    const fechaIncidencia =
        document.getElementById(
            "fechaIncidencia"
        );


    const lista =
        document.getElementById(
            "listaIncidencias"
        );



    fechaIncidencia.value =
        fecha;


    const fechaObjeto =
        new Date(
            fecha + "T00:00:00"
        );


    const opciones = {

        weekday: "long",

        day: "numeric",

        month: "long",

        year: "numeric"

    };


    let fechaTexto =
        fechaObjeto.toLocaleDateString(
            "es-MX",
            opciones
        );


    fechaTexto =
        fechaTexto.charAt(0).toUpperCase() +
        fechaTexto.slice(1);


    modalFecha.textContent =
        fechaTexto;



    lista.innerHTML = "";



    const eventos =
        eventosCalendario.filter(
            evento =>
                evento.fecha === fecha
        );


    if (eventos.length === 0) {

        lista.innerHTML = `

            <div class="modal-empty">

                <div class="modal-empty-icon">
                    📅
                </div>

                <p>
                    No hay incidencias registradas
                    para esta fecha.
                </p>

            </div>

        `;

    }


    eventos.forEach(
        evento => {

            let clase =
                "modal-event-other";

            let icono =
                "📌";


           switch (evento.tipo) {

    case "economic":

        icono = evento.mio
            ? "⭐"
            : "🟧";

        break;

    case "incapacity":

        icono = "🟪";

        break;

    case "non-working":

        icono = "🟢";

        break;

    case "holiday":

        icono = "🟣";

        break;

    case "vacation":

        icono = "🌴";

        break;

    case "permission":

        icono = "🟨";

        break;

    default:

        icono = "📌";

        break;
}


            const elemento =
                document.createElement(
                    "div"
                );


            elemento.className =
                "modal-event " + clase;


          elemento.innerHTML = `

    <div class="modal-event-icon">
        ${icono}
    </div>

    <div class="modal-event-info">

        <strong>
            ${escapeHtml(evento.nombre)}
        </strong>

        <span>
            ${escapeHtml(evento.tipoOriginal)}
        </span>

        ${
            evento.descripcion
            ? `
                <small>
                    ${escapeHtml(evento.descripcion)}
                </small>
              `
            : ""
        }

    </div>

    ${
        evento.mio
        ? `
            <button
                class="btn-eliminar-incidencia"
                onclick="eliminarIncidencia(${evento.id})"
            >
                🗑
            </button>
        `
        : ""
    }

`;


            lista.appendChild(
                elemento
            );

        }
    );



    modal.classList.add(
        "active"
    );


    document.body.classList.add(
        "modal-open"
    );

}



function cerrarModal() {

    const modal =
        document.getElementById(
            "modalIncidencias"
        );


    modal.classList.remove(
        "active"
    );


    document.body.classList.remove(
        "modal-open"
    );

}

async function eliminarIncidencia(id){

    const confirmar =
        confirm(
            "¿Deseas eliminar esta incidencia?"
        );

    if(!confirmar){

        return;

    }

    try{

        const respuesta =
            await fetch(
                "eliminar_incidencia.php",
                {
                    method:"POST",
                    headers:{
                        "Content-Type":
                        "application/x-www-form-urlencoded"
                    },
                    body:
                        "id=" + id
                }
            );

        const resultado =
            await respuesta.json();

        alert(
            resultado.message
        );

        if(
            resultado.success
        ){

            location.reload();

        }

    }
    catch(error){

        alert(
            "Error al eliminar."
        );
    }
}



document
    .getElementById(
        "modalIncidencias"
    )
    .addEventListener(
        "click",
        function (evento) {

            if (
                evento.target === this
            ) {

                cerrarModal();

            }

        }
    );



document.addEventListener(
    "keydown",
    function (evento) {

        if (
            evento.key === "Escape"
        ) {

            cerrarModal();

        }

    }
);


function escapeHtml(texto) {

    const div =
        document.createElement(
            "div"
        );

    div.textContent =
        texto ?? "";

    return div.innerHTML;

}



document
    .getElementById(
        "formIncidencia"
    )
    .addEventListener(
        "submit",
        async function (evento) {

            evento.preventDefault();


            const formulario =
                this;


            const boton =
                formulario.querySelector(
                    ".btn-registrar-incidencia"
                );


            const mensaje =
                document.getElementById(
                    "mensajeIncidencia"
                );


            const datos =
                new FormData(
                    formulario
                );


            boton.disabled =
                true;


            boton.textContent =
                "Guardando...";


            mensaje.className =
                "modal-message";


            mensaje.textContent =
                "";


            try {

                const respuesta =
                    await fetch(
                        "registrar_dia.php",
                        {

                            method: "POST",

                            body: datos

                        }
                    );


                const resultado =
                    await respuesta.json();


                if (
                    !resultado.success
                ) {

                    throw new Error(
                        resultado.message
                    );

                }


                mensaje.className =
                    "modal-message success";


                mensaje.textContent =
                    resultado.message;


                /*
                 * Recargar la información
                 * del calendario.
                 */

                const fecha =
                    datos.get(
                        "fecha"
                    );


                setTimeout(
                    function () {

                        cerrarModal();

                        location.reload();

                    },
                    700
                );


            } catch (error) {

                mensaje.className =
                    "modal-message error";


                mensaje.textContent =
                    error.message ||
                    "Ocurrió un error.";

            }


            boton.disabled =
                false;


            boton.textContent =
                "+ Registrar incidencia";

        }
    );


generarCalendario();

</script>


<?php

include "includes/footer.php";

?>