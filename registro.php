<?php

session_start();

require_once "conexion.php";


// ========================================
// SI YA HAY UNA SESIÓN ACTIVA
// ========================================

if (isset($_SESSION["usuario"])) {

    header("Location: dashboard.php");
    exit();

}


// ========================================
// VARIABLES
// ========================================

$mensaje = "";
$tipoMensaje = "";


// ========================================
// PROCESAR REGISTRO
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ----------------------------------------
    // DATOS PERSONALES
    // ----------------------------------------

    $nombre = trim($_POST["nombre"] ?? "");

    $apellido_paterno =
        trim($_POST["apellido_paterno"] ?? "");

    $apellido_materno =
        trim($_POST["apellido_materno"] ?? "");

    $curp =
        strtoupper(trim($_POST["curp"] ?? ""));

    $rfc =
        strtoupper(trim($_POST["rfc"] ?? ""));

    $correo =
        trim($_POST["correo"] ?? "");

    $telefono =
        trim($_POST["telefono"] ?? "");


    // ----------------------------------------
    // DATOS DE ACCESO
    // ----------------------------------------

    $usuario =
        trim($_POST["usuario"] ?? "");

    $password =
        $_POST["password"] ?? "";

    $password_confirmar =
        $_POST["password_confirmar"] ?? "";


    // ========================================
    // VALIDAR CAMPOS OBLIGATORIOS
    // ========================================

    if (
        $nombre === "" ||
        $apellido_paterno === "" ||
        $curp === "" ||
        $rfc === "" ||
        $correo === "" ||
        $usuario === "" ||
        $password === "" ||
        $password_confirmar === ""
    ) {

        $mensaje =
            "Por favor, completa todos los campos obligatorios.";

        $tipoMensaje = "error";

    }

    // ========================================
    // VALIDAR CONTRASEÑAS
    // ========================================

    elseif ($password !== $password_confirmar) {

        $mensaje =
            "Las contraseñas no coinciden.";

        $tipoMensaje = "error";

    }

    // ========================================
    // VALIDAR LONGITUD DE CONTRASEÑA
    // ========================================

    elseif (strlen($password) < 6) {

        $mensaje =
            "La contraseña debe tener al menos 6 caracteres.";

        $tipoMensaje = "error";

    }

    // ========================================
    // VALIDAR CORREO
    // ========================================

    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje =
            "Ingresa un correo electrónico válido.";

        $tipoMensaje = "error";

    }

    // ========================================
    // VALIDAR CURP
    // ========================================

    elseif (strlen($curp) !== 18) {

        $mensaje =
            "La CURP debe tener 18 caracteres.";

        $tipoMensaje = "error";

    }

    // ========================================
    // VALIDAR RFC
    // ========================================

    elseif (strlen($rfc) < 12 || strlen($rfc) > 13) {

        $mensaje =
            "El RFC debe tener entre 12 y 13 caracteres.";

        $tipoMensaje = "error";

    }

    else {

        // ========================================
        // COMPROBAR DATOS DUPLICADOS
        // ========================================

        $sql = "
            SELECT
                e.id_empleado,
                e.curp,
                e.rfc,
                e.correo,
                u.usuario

            FROM empleados e

            LEFT JOIN usuarios u
                ON e.id_empleado = u.id_empleado

            WHERE
                e.curp = ?
                OR e.rfc = ?
                OR e.correo = ?
                OR u.usuario = ?

            LIMIT 1
        ";


        $stmt = $conexion->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $curp,
            $rfc,
            $correo,
            $usuario
        );

        $stmt->execute();

        $resultado = $stmt->get_result();

        $existente = $resultado->fetch_assoc();

        $stmt->close();


        if ($existente) {

            if (
                isset($existente["curp"]) &&
                $existente["curp"] === $curp
            ) {

                $mensaje =
                    "La CURP ya está registrada.";

            } elseif (
                isset($existente["rfc"]) &&
                $existente["rfc"] === $rfc
            ) {

                $mensaje =
                    "El RFC ya está registrado.";

            } elseif (
                isset($existente["correo"]) &&
                $existente["correo"] === $correo
            ) {

                $mensaje =
                    "El correo electrónico ya está registrado.";

            } else {

                $mensaje =
                    "El nombre de usuario ya está registrado.";

            }

            $tipoMensaje = "error";

        } else {


            // ========================================
            // COMENZAR TRANSACCIÓN
            // ========================================

            $conexion->begin_transaction();


            try {

                // ========================================
                // BUSCAR ROL EMPLEADO
                // ========================================

                $sqlRol = "
                    SELECT id_rol
                    FROM roles
                    WHERE nombre = 'Empleado'
                      AND activo = 1
                    LIMIT 1
                ";

                $resultadoRol =
                    $conexion->query($sqlRol);

                $rol = $resultadoRol->fetch_assoc();


                if (!$rol) {

                    throw new Exception(
                        "No existe el rol Empleado."
                    );

                }


                $id_rol =
                    (int)$rol["id_rol"];


                // ========================================
                // BUSCAR ESTATUS ACTIVO
                // ========================================

                $sqlEstatus = "
                    SELECT id_estatus
                    FROM estatus
                    WHERE nombre = 'Activo'
                      AND activo = 1
                    LIMIT 1
                ";

                $resultadoEstatus =
                    $conexion->query($sqlEstatus);

                $estatus =
                    $resultadoEstatus->fetch_assoc();


                if (!$estatus) {

                    throw new Exception(
                        "No existe el estatus Activo."
                    );

                }


                $id_estatus =
                    (int)$estatus["id_estatus"];


                // ========================================
                // INSERTAR EMPLEADO
                // ========================================

                $sqlEmpleado = "
                    INSERT INTO empleados (
                        nombre,
                        apellido_paterno,
                        apellido_materno,
                        curp,
                        rfc,
                        correo,
                        telefono,
                        id_estatus
                    )

                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ";


                $stmtEmpleado =
                    $conexion->prepare($sqlEmpleado);


                $stmtEmpleado->bind_param(
                    "sssssssi",
                    $nombre,
                    $apellido_paterno,
                    $apellido_materno,
                    $curp,
                    $rfc,
                    $correo,
                    $telefono,
                    $id_estatus
                );


                if (!$stmtEmpleado->execute()) {

                    throw new Exception(
                        "No se pudo registrar al empleado."
                    );

                }


                // ========================================
                // OBTENER ID DEL EMPLEADO
                // ========================================

                $id_empleado =
                    $conexion->insert_id;


                $stmtEmpleado->close();


                // ========================================
                // GENERAR HASH DE CONTRASEÑA
                // ========================================

                $password_hash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                if ($password_hash === false) {

                    throw new Exception(
                        "No se pudo proteger la contraseña."
                    );

                }


                // ========================================
                // INSERTAR USUARIO
                // ========================================

                $sqlUsuario = "
                    INSERT INTO usuarios (
                        id_empleado,
                        usuario,
                        password,
                        id_rol,
                        activo
                    )

                    VALUES (?, ?, ?, ?, 1)
                ";


                $stmtUsuario =
                    $conexion->prepare($sqlUsuario);


                $stmtUsuario->bind_param(
                    "issi",
                    $id_empleado,
                    $usuario,
                    $password_hash,
                    $id_rol
                );


                if (!$stmtUsuario->execute()) {

                    throw new Exception(
                        "No se pudo crear el usuario."
                    );

                }


                $stmtUsuario->close();


                // ========================================
                // CONFIRMAR TRANSACCIÓN
                // ========================================

                $conexion->commit();


                $mensaje =
                    "Registro completado correctamente. "
                    . "Ahora puedes iniciar sesión.";

                $tipoMensaje = "exito";


                // Limpiar campos

                $nombre = "";
                $apellido_paterno = "";
                $apellido_materno = "";
                $curp = "";
                $rfc = "";
                $correo = "";
                $telefono = "";
                $usuario = "";


            } catch (Exception $e) {

                // ========================================
                // DESHACER REGISTRO
                // ========================================

                $conexion->rollback();


                $mensaje =
                    "No se pudo completar el registro. "
                    . $e->getMessage();

                $tipoMensaje = "error";

            }

        }

    }

}

?>


<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Registrarse | Sistema de Kardex
    </title>


    <link
        rel="stylesheet"
        href="css/estilos.css"
    >

</head>


<body>


<div class="login-container">


    <div class="login-card registro-card">


        <!-- ========================================
             ENCABEZADO
             ======================================== -->

        <div class="login-header">

            <h1>
                Sistema de Kardex
            </h1>

            <p>
                Crear una cuenta
            </p>

        </div>


        <!-- ========================================
             MENSAJE
             ======================================== -->

        <?php if ($mensaje !== ""): ?>

            <div
                class="login-message
                <?= $tipoMensaje === "exito"
                    ? "mensaje-exito"
                    : "mensaje-error" ?>"
            >

                <?= htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>

            </div>

        <?php endif; ?>


        <!-- ========================================
             FORMULARIO
             ======================================== -->

        <form
            method="POST"
            action="registro.php"
        >


            <!-- ====================================
                 DATOS PERSONALES
                 ==================================== -->

            <h3 class="form-section-title">
                Datos personales
            </h3>


            <div class="form-group">

                <label for="nombre">
                    Nombre *
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ingresa tu nombre"
                    required
                    value="<?= htmlspecialchars(
                        $nombre ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="apellido_paterno">
                    Apellido paterno *
                </label>

                <input
                    type="text"
                    id="apellido_paterno"
                    name="apellido_paterno"
                    placeholder="Apellido paterno"
                    required
                    value="<?= htmlspecialchars(
                        $apellido_paterno ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="apellido_materno">
                    Apellido materno
                </label>

                <input
                    type="text"
                    id="apellido_materno"
                    name="apellido_materno"
                    placeholder="Apellido materno"
                    value="<?= htmlspecialchars(
                        $apellido_materno ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="curp">
                    CURP *
                </label>

                <input
                    type="text"
                    id="curp"
                    name="curp"
                    maxlength="18"
                    placeholder="CURP"
                    required
                    value="<?= htmlspecialchars(
                        $curp ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="rfc">
                    RFC *
                </label>

                <input
                    type="text"
                    id="rfc"
                    name="rfc"
                    maxlength="13"
                    placeholder="RFC"
                    required
                    value="<?= htmlspecialchars(
                        $rfc ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="correo">
                    Correo electrónico *
                </label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="correo@ejemplo.com"
                    required
                    value="<?= htmlspecialchars(
                        $correo ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="telefono">
                    Teléfono
                </label>

                <input
                    type="text"
                    id="telefono"
                    name="telefono"
                    maxlength="20"
                    placeholder="Número telefónico"
                    value="<?= htmlspecialchars(
                        $telefono ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <!-- ====================================
                 DATOS DE ACCESO
                 ==================================== -->

            <h3 class="form-section-title">
                Datos de acceso
            </h3>


            <div class="form-group">

                <label for="usuario">
                    Nombre de usuario *
                </label>

                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    maxlength="50"
                    placeholder="Crea tu usuario"
                    required
                    value="<?= htmlspecialchars(
                        $usuario ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Contraseña *
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Mínimo 6 caracteres"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password_confirmar">
                    Confirmar contraseña *
                </label>

                <input
                    type="password"
                    id="password_confirmar"
                    name="password_confirmar"
                    placeholder="Repite tu contraseña"
                    minlength="6"
                    required
                >

            </div>


            <!-- ====================================
                 INFORMACIÓN DEL SISTEMA
                 ==================================== -->

            <div class="registro-info">

                <strong>
                    Información importante
                </strong>

                <p>
                    Al registrarte se creará una cuenta
                    con el rol de Empleado.
                </p>

                <p>
                    Los datos laborales como área,
                    puesto, plaza y nivel serán asignados
                    posteriormente por un administrador.
                </p>

            </div>


            <!-- ====================================
                 BOTÓN REGISTRAR
                 ==================================== -->

            <button
                type="submit"
                class="login-button"
            >
                Registrarse
            </button>


        </form>


        <!-- ========================================
             REGRESAR AL LOGIN
             ======================================== -->

        <div class="register-section">

            <p>
                ¿Ya tienes una cuenta?
            </p>


            <a
                href="index.php"
                class="register-button"
            >
                Iniciar sesión
            </a>

        </div>


    </div>


</div>


</body>

</html>
