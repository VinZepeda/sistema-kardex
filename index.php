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


// ========================================
// PROCESAR LOGIN
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";


    // ========================================
    // VALIDAR CAMPOS
    // ========================================

    if ($usuario === "" || $password === "") {

        $mensaje = "Por favor, completa todos los campos.";

    } else {


        // ========================================
        // BUSCAR USUARIO
        // ========================================

        $sql = "
            SELECT
                u.id_usuario,
                u.id_empleado,
                u.usuario,
                u.password,
                u.id_rol,
                u.activo

            FROM usuarios u

            WHERE u.usuario = ?

            LIMIT 1
        ";


        $stmt = $conexion->prepare($sql);

        $stmt->bind_param("s", $usuario);

        $stmt->execute();

        $resultado = $stmt->get_result();

        $usuarioBD = $resultado->fetch_assoc();


        // ========================================
        // VERIFICAR USUARIO Y CONTRASEÑA
        // ========================================

        if (!$usuarioBD) {

            $mensaje =
                "El usuario o la contraseña son incorrectos.";

        } elseif ((int)$usuarioBD["activo"] !== 1) {

            $mensaje =
                "Este usuario se encuentra inactivo.";

        } elseif (
            !password_verify(
                $password,
                $usuarioBD["password"]
            )
        ) {

            $mensaje =
                "El usuario o la contraseña son incorrectos.";

        } else {


            // ========================================
            // LOGIN CORRECTO
            // ========================================

            session_regenerate_id(true);


            $_SESSION["usuario"] =
                $usuarioBD["usuario"];

            $_SESSION["id_usuario"] =
                $usuarioBD["id_usuario"];

            $_SESSION["id_empleado"] =
                $usuarioBD["id_empleado"];

            $_SESSION["id_rol"] =
                $usuarioBD["id_rol"];


            // ========================================
            // ACTUALIZAR ÚLTIMO ACCESO
            // ========================================

            $sqlAcceso = "
                UPDATE usuarios

                SET ultimo_acceso = NOW()

                WHERE id_usuario = ?
            ";


            $stmtAcceso =
                $conexion->prepare($sqlAcceso);


            $stmtAcceso->bind_param(
                "i",
                $usuarioBD["id_usuario"]
            );


            $stmtAcceso->execute();

            $stmtAcceso->close();


            // ========================================
            // REDIRECCIÓN
            // ========================================

            header("Location: dashboard.php");

            exit();

        }


        $stmt->close();

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
        Iniciar sesión | Sistema de Kardex
    </title>


    <link
        rel="stylesheet"
        href="css/estilos.css"
    >

</head>


<body class="login-page">


<div class="login-layout">


    <!-- ========================================
         LADO IZQUIERDO
         ======================================== -->

    <section class="login-brand">


        <div class="brand-content">


            <!-- CÍRCULO -->

            <div class="brand-circle">


                <!-- TEXTO EN ARCO -->

                <svg
                    class="brand-title-svg"
                    viewBox="0 0 500 500"
                >

                    <defs>

                        <path
                            id="textoArco"
                            d="
                                M 85,250
                                A 165,165
                                0 0,1
                                415,250
                            "
                        />

                    </defs>


                    <text class="brand-arc-text">

                        <textPath
                            href="#textoArco"
                            startOffset="50%"
                        >

                            SISTEMA DE KARDEX

                        </textPath>

                    </text>

                </svg>


                <!-- IMAGEN CENTRAL -->

                <div class="brand-image">

                    <img
                        src="img/logo.png"
                        alt="Logo del Sistema de Kardex"
                    >

                </div>


            </div>


            <!-- TEXTO DEBAJO DEL CÍRCULO -->

            <div class="brand-subtitle">

                <span>
                    Sistema de control
                </span>

                <strong>
                    de personal
                </strong>

            </div>


        </div>


    </section>


    <!-- ========================================
         LADO DERECHO
         ======================================== -->

    <section class="login-form-section">


        <div class="login-form-card">


            <!-- ENCABEZADO -->

            <div class="form-header">

                <h1>
                    Bienvenido
                </h1>

                <p>
                    Inicia sesión para continuar
                </p>

            </div>


            <!-- MENSAJE DE ERROR -->

            <?php if ($mensaje !== ""): ?>

                <div class="login-message">

                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- ====================================
                 FORMULARIO
                 ==================================== -->

            <form
                method="POST"
                action="index.php"
                class="login-form"
            >


                <!-- USUARIO -->

                <div class="form-group">


                    <label for="usuario">
                        Usuario
                    </label>


                    <div class="input-wrapper">

                        <span class="input-icon">
                            👤
                        </span>


                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            placeholder="Ingresa tu usuario"
                            autocomplete="username"
                            required
                            value="<?= htmlspecialchars(
                                $_POST["usuario"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>"
                        >

                    </div>


                </div>


                <!-- CONTRASEÑA -->

                <div class="form-group">


                    <label for="password">
                        Contraseña
                    </label>


                    <div class="input-wrapper">

                        <span class="input-icon">
                            🔒
                        </span>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            required
                        >

                    </div>


                </div>


                <!-- BOTÓN LOGIN -->

                <button
                    type="submit"
                    class="login-button"
                >

                    Iniciar sesión

                </button>


            </form>


            <!-- ====================================
                 REGISTRO
                 ==================================== -->

            <div class="register-section">


                <p>
                    ¿No tienes una cuenta?
                </p>


                <a
                    href="registro.php"
                    class="register-button"
                >

                    Registrarse

                </a>


            </div>


            <!-- PIE -->

            <div class="login-footer">

                <span>
                    Sistema de Kardex
                </span>

            </div>


        </div>


    </section>


</div>


</body>

</html>

