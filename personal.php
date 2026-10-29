<?php

session_start();

require_once "conexion.php";

if (!isset($_SESSION["usuario"])) {

    header("Location: index.php");
    exit();

}

$usuario = $_SESSION["usuario"];


$sqlUsuario = "

SELECT

    e.id_empleado,

    e.id_superior,

    r.id_rol,

    r.nombre AS rol,

    r.nivel_jerarquico

FROM usuarios u

INNER JOIN empleados e
    ON u.id_empleado = e.id_empleado

INNER JOIN roles r
    ON u.id_rol = r.id_rol

WHERE u.usuario = ?

LIMIT 1

";

$stmt = $conexion->prepare($sqlUsuario);

$stmt->bind_param(
    "s",
    $usuario
);

$stmt->execute();

$resultadoUsuario =
    $stmt->get_result();

$usuarioActual =
    $resultadoUsuario->fetch_assoc();

$stmt->close();

if (!$usuarioActual) {

    session_destroy();

    header("Location: index.php");
    exit();

}

$idEmpleadoActual =
(int)$usuarioActual["id_empleado"];

$nivelJerarquico =
(int)$usuarioActual["nivel_jerarquico"];



if ($nivelJerarquico == 4) {


    $sqlPersonal = "

    SELECT

        e.id_empleado,

        CONCAT(
            e.nombre,' ',
            COALESCE(e.apellido_paterno,''),
            ' ',
            COALESCE(e.apellido_materno,'')
        ) AS nombre,

        p.nombre AS puesto,

        a.nombre AS area,

        es.nombre AS estatus

    FROM empleados e

    LEFT JOIN puestos p
        ON e.id_puesto = p.id_puesto

    LEFT JOIN areas a
        ON e.id_area = a.id_area

    LEFT JOIN estatus es
        ON e.id_estatus = es.id_estatus

    ORDER BY nombre

    ";

    $personal =
        $conexion->query(
            $sqlPersonal
        );
}
else if ($nivelJerarquico >= 2) {

    /*
    Subdirector o Jefe
    Ve sólo subordinados directos
    */

    $sqlPersonal = "

    SELECT

        e.id_empleado,

        CONCAT(
            e.nombre,' ',
            COALESCE(e.apellido_paterno,''),
            ' ',
            COALESCE(e.apellido_materno,'')
        ) AS nombre,

        p.nombre AS puesto,

        a.nombre AS area,

        es.nombre AS estatus

    FROM empleados e

    LEFT JOIN puestos p
        ON e.id_puesto = p.id_puesto

    LEFT JOIN areas a
        ON e.id_area = a.id_area

    LEFT JOIN estatus es
        ON e.id_estatus = es.id_estatus

    WHERE e.id_superior = ?

    ORDER BY nombre

    ";

    $stmt =
        $conexion->prepare(
            $sqlPersonal
        );

    $stmt->bind_param(
        "i",
        $idEmpleadoActual
    );

    $stmt->execute();

    $personal =
        $stmt->get_result();
}
else {

    /*
    Empleado
    Sólo se ve a sí mismo
    */

    $sqlPersonal = "

    SELECT

        e.id_empleado,

        CONCAT(
            e.nombre,' ',
            COALESCE(e.apellido_paterno,''),
            ' ',
            COALESCE(e.apellido_materno,'')
        ) AS nombre,

        p.nombre AS puesto,

        a.nombre AS area,

        es.nombre AS estatus

    FROM empleados e

    LEFT JOIN puestos p
        ON e.id_puesto = p.id_puesto

    LEFT JOIN areas a
        ON e.id_area = a.id_area

    LEFT JOIN estatus es
        ON e.id_estatus = es.id_estatus

    WHERE e.id_empleado = ?

    ";

    $stmt =
        $conexion->prepare(
            $sqlPersonal
        );

    $stmt->bind_param(
        "i",
        $idEmpleadoActual
    );

    $stmt->execute();

    $personal =
        $stmt->get_result();
}

$titulo = "Personal | Sistema de Kardex";

include "includes/header.php";
include "includes/menu.php";

?>

<main class="main-content">

    <div class="page-header-personal">

        <div>

            <h2>Personal</h2>

            <p>
                Personal disponible según tus permisos
            </p>

        </div>

    </div>

   <section class="search-container">

    <div class="form-group">

        <label for="buscarPersonal">
            Buscar empleado
        </label>

        <input
            type="text"
            id="buscarPersonal"
            placeholder="Nombre del empleado..."
        >

    </div>

    <div class="resultados-info">

        Mostrando
        <strong id="contadorResultados">0</strong>
        empleados

    </div>




</section>




    </section>

    <section class="personal-table-container">

        <table class="personal-table">

           <thead>
<tr>
    <th>Empleado</th>
    <th>Puesto</th>
    <th>Área</th>
    <th>Estatus</th>
    <th>Acciones</th>
</tr>
</thead>

            <tbody id="tablaPersonal">

<?php while($empleado = $personal->fetch_assoc()): ?>

<tr>

    <td><?= htmlspecialchars($empleado["nombre"]) ?></td>

    <td><?= htmlspecialchars($empleado["puesto"]) ?></td>

    <td><?= htmlspecialchars($empleado["area"]) ?></td>

    <td><?= htmlspecialchars($empleado["estatus"]) ?></td>

    <td>
        <a
    href="ver_kardex.php?id=<?= $empleado["id_empleado"] ?>"
    class="btn-ver-kardex"
>
    👁 Ver Kardex
</a>
    </td>

</tr>

<?php endwhile; ?>

</tbody>
        </table>

    </section>

</main>





<?php

include "includes/footer.php";

?>