<?php

session_start();

require_once "conexion.php";

if (
    !isset($_SESSION["usuario"]) ||
    !isset($_SESSION["id_empleado"])
) {

    header("Location:index.php");
    exit();

}

$idEmpleado =
    (int)$_SESSION["id_empleado"];

$tipoJustificante =
    trim(
        $_POST["tipo_justificante"]
        ?? ""
    );

$tipoIncapacidad =
    trim(
        $_POST["tipo_incapacidad"]
        ?? ""
    );

$origen =
    trim(
        $_POST["origen"]
        ?? ""
    );

$fechaInicio =
    $_POST["fecha_inicio"]
    ?? "";

$fechaFin =
    $_POST["fecha_fin"]
    ?? "";

if (

    empty($tipoJustificante) ||
    empty($tipoIncapacidad) ||
    empty($origen) ||
    empty($fechaInicio) ||
    empty($fechaFin)

) {

    die(
        "Todos los campos son obligatorios."
    );

}

if (
    !isset($_FILES["archivo"])
) {

    die(
        "Debe seleccionar un archivo."
    );

}

$archivo =
    $_FILES["archivo"];

if (
    $archivo["error"] !==
    UPLOAD_ERR_OK
) {

    die(
        "Error al subir el archivo."
    );

}

$extensionesPermitidas =
[
    "pdf",
    "jpg",
    "jpeg",
    "png"
];

$extension =
    strtolower(
        pathinfo(
            $archivo["name"],
            PATHINFO_EXTENSION
        )
    );

if (
    !in_array(
        $extension,
        $extensionesPermitidas
    )
) {

    die(
        "Formato no permitido."
    );

}

$carpetaDestino =
    "uploads/justificantes/";

if (
    !is_dir(
        $carpetaDestino
    )
) {

    mkdir(
        $carpetaDestino,
        0777,
        true
    );

}


$nombreArchivo =
    "justificante_" .
    $idEmpleado .
    "_" .
    date("YmdHis") .
    "." .
    $extension;

$rutaArchivo =
    $carpetaDestino .
    $nombreArchivo;


if (
    !move_uploaded_file(
        $archivo["tmp_name"],
        $rutaArchivo
    )
) {

    die(
        "No fue posible guardar el archivo."
    );

}


$sql = "

INSERT INTO justificantes
(

    id_empleado,

    tipo_justificante,

    tipo_incapacidad,

    origen,

    archivo,

    fecha_inicio,

    fecha_fin

)
VALUES
(

    ?,

    ?,

    ?,

    ?,

    ?,

    ?,

    ?

)

";

$stmt =
    $conexion->prepare(
        $sql
    );

if (!$stmt) {

    die(
        $conexion->error
    );

}

$stmt->bind_param(

    "issssss",

    $idEmpleado,

    $tipoJustificante,

    $tipoIncapacidad,

    $origen,

    $rutaArchivo,

    $fechaInicio,

    $fechaFin

);

if (
    !$stmt->execute()
) {

    die(
        $stmt->error
    );

}

$stmt->close();

$conexion->close();


header(
    "Location: dashboard.php?justificante=1"
);

exit();

?>