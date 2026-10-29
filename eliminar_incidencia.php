<?php

session_start();

require_once "conexion.php";

header(
    "Content-Type: application/json"
);

if(
    !isset(
        $_SESSION["id_empleado"]
    )
){

    echo json_encode([
        "success"=>false,
        "message"=>"Sesión inválida."
    ]);

    exit;

}

$idEmpleado =
    (int)$_SESSION["id_empleado"];

$id =
    isset($_POST["id"])
    ? (int)$_POST["id"]
    : 0;

$sql = "

DELETE FROM calendario_empleados

WHERE id_calendario = ?

AND id_empleado = ?

";

$stmt =
    $conexion->prepare(
        $sql
    );

$stmt->bind_param(
    "ii",
    $id,
    $idEmpleado
);

if(
    $stmt->execute()
){

    echo json_encode([

        "success"=>true,

        "message"=>
        "Incidencia eliminada correctamente."

    ]);

}
else{

    echo json_encode([

        "success"=>false,

        "message"=>
        "No fue posible eliminar."

    ]);

}

$stmt->close();

$conexion->close();