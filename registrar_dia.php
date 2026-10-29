<?php

session_start();

require_once "conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=utf-8");




if (!isset($_SESSION["usuario"]) || empty($_SESSION["id_empleado"])) {

    echo json_encode([
        "success" => false,
        "message" => "Sesión no válida."
    ]);

    exit();
}



$idEmpleado = (int) $_SESSION["id_empleado"];



$fecha = $_POST["fecha"] ?? "";

$tipoDia = $_POST["tipo_dia"] ?? "";

$descripcion = trim(
    $_POST["descripcion"] ?? ""
);




$fechaObjeto = DateTime::createFromFormat(
    "Y-m-d",
    $fecha
);

if (
    !$fechaObjeto ||
    $fechaObjeto->format("Y-m-d") !== $fecha
) {

    echo json_encode([
        "success" => false,
        "message" => "La fecha no es válida."
    ]);

    exit();
}



$tiposPermitidos = [

    "Día económico",
    "Incapacidad",
    "Vacaciones",
    "Permiso",
    "Día inhábil",
    "Festivo",
    "Otro"

];


if (!in_array($tipoDia, $tiposPermitidos, true)) {

    echo json_encode([
        "success" => false,
        "message" => "El tipo de incidencia no es válido."
    ]);

    exit();
}



$sqlEmpleado = "
    SELECT
        id_empleado,
        nombre,
        apellido_paterno,
        apellido_materno
    FROM empleados
    WHERE id_empleado = ?
    LIMIT 1
";

$stmtEmpleado =
    $conexion->prepare($sqlEmpleado);

$stmtEmpleado->bind_param(
    "i",
    $idEmpleado
);

$stmtEmpleado->execute();

$resultadoEmpleado =
    $stmtEmpleado->get_result();

$empleado =
    $resultadoEmpleado->fetch_assoc();

$stmtEmpleado->close();


if (!$empleado) {

    echo json_encode([
        "success" => false,
        "message" => "No se encontró el empleado de la sesión."
    ]);

    exit();
}



$sqlDuplicado = "
    SELECT id_calendario
    FROM calendario_empleados
    WHERE id_empleado = ?
      AND fecha = ?
      AND tipo_dia = ?
    LIMIT 1
";

$stmtDuplicado =
    $conexion->prepare($sqlDuplicado);

$stmtDuplicado->bind_param(
    "iss",
    $idEmpleado,
    $fecha,
    $tipoDia
);

$stmtDuplicado->execute();

$resultadoDuplicado =
    $stmtDuplicado->get_result();

$duplicado =
    $resultadoDuplicado->fetch_assoc();

$stmtDuplicado->close();


if ($duplicado) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Ya tienes registrada esta incidencia para ese día."
    ]);

    exit();
}




if ($tipoDia === "Día económico") {



   $hoy = new DateTime();

$fechaEconomica = new DateTime($fecha);

if ($fechaEconomica <= $hoy) {
    
        echo json_encode([
            "success" => false,
            "message" => "El día económico debe solicitarse para una fecha futura."
        ]);

        exit();
    }

    $diasAnticipacion =
        (int)$hoy->diff($fechaEconomica)->days;

    if ($diasAnticipacion < 15) {

        echo json_encode([
            "success" => false,
            "message" => "El día económico requiere al menos 15 días de anticipación."
        ]);

        exit();
    }

   

    $diaSemana =
        (int)$fechaEconomica->format('N');

    if (!in_array($diaSemana, [2, 3, 4])) {

        echo json_encode([
            "success" => false,
            "message" => "Los días económicos únicamente pueden solicitarse en martes, miércoles o jueves."
        ]);

        exit();
    }

 

    $sqlInhabil = "
        SELECT id
        FROM dias_inhabiles
        WHERE fecha = ?
        LIMIT 1
    ";

    $stmtInhabil =
        $conexion->prepare($sqlInhabil);

    $stmtInhabil->bind_param(
        "s",
        $fecha
    );

    $stmtInhabil->execute();

    $resultadoInhabil =
        $stmtInhabil->get_result();

    if ($resultadoInhabil->num_rows > 0) {

        $stmtInhabil->close();

        echo json_encode([
            "success" => false,
            "message" => "No es posible solicitar un día económico en un día inhábil, festivo o puente."
        ]);

        exit();
    }

    $stmtInhabil->close();

 

    if ($diaSemana === 2) {

        $lunesAnterior =
            date(
                "Y-m-d",
                strtotime($fecha . " -1 day")
            );

        $stmtLunes =
            $conexion->prepare(
                "
                SELECT id
                FROM dias_inhabiles
                WHERE fecha = ?
                LIMIT 1
                "
            );

        $stmtLunes->bind_param(
            "s",
            $lunesAnterior
        );

        $stmtLunes->execute();

        $resultadoLunes =
            $stmtLunes->get_result();

        if ($resultadoLunes->num_rows > 0) {

            $stmtLunes->close();

            echo json_encode([
                "success" => false,
                "message" => "No puedes solicitar un día económico en martes porque el lunes anterior es festivo, puente o inhábil."
            ]);

            exit();
        }

        $stmtLunes->close();
    }


    if ($diaSemana === 4) {

        $viernesSiguiente =
            date(
                "Y-m-d",
                strtotime($fecha . " +1 day")
            );

        $stmtViernes =
            $conexion->prepare(
                "
                SELECT id
                FROM dias_inhabiles
                WHERE fecha = ?
                LIMIT 1
                "
            );

        $stmtViernes->bind_param(
            "s",
            $viernesSiguiente
        );

        $stmtViernes->execute();

        $resultadoViernes =
            $stmtViernes->get_result();

        if ($resultadoViernes->num_rows > 0) {

            $stmtViernes->close();

            echo json_encode([
                "success" => false,
                "message" => "No puedes solicitar un día económico en jueves porque el viernes siguiente es festivo, puente o inhábil."
            ]);

            exit();
        }

        $stmtViernes->close();
    }
}

$sql = "
    INSERT INTO calendario_empleados
    (
        id_empleado,
        fecha,
        tipo_dia,
        descripcion
    )
    VALUES (?, ?, ?, ?)
";


$stmt =
    $conexion->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" =>
            "No fue posible preparar el registro."
    ]);

    exit();
}


$stmt->bind_param(
    "isss",
    $idEmpleado,
    $fecha,
    $tipoDia,
    $descripcion
);


if ($stmt->execute()) {

    $nombreEmpleado = trim(
        $empleado["nombre"] . " " .
        ($empleado["apellido_paterno"] ?? "") . " " .
        ($empleado["apellido_materno"] ?? "")
    );


    echo json_encode([

        "success" => true,

        "message" =>
            "La incidencia se registró correctamente.",

        "id" =>
            $stmt->insert_id,

        "empleado" =>
            $nombreEmpleado,

        "fecha" =>
            $fecha,

        "tipo" =>
            $tipoDia

    ]);

} else {

    echo json_encode([

        "success" => false,

        "message" =>
            "No fue posible registrar la incidencia."

    ]);

}


$stmt->close();

$conexion->close();

?>