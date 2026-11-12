<?php

session_start();

require_once "conexion.php";

if (isset($_SESSION["usuario"])) {

    header("Location: dashboard.php");
    exit();
}

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($usuario === "" || $password === "") {
        $mensaje = "Por favor, completa todos los campos.";
    } else {
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

        if (!$usuarioBD) { $mensaje = "El usuario o la contraseña son incorrectos.";
        
        } elseif ((int)$usuarioBD["activo"] !== 1) {
            $mensaje =
                "Este usuario se encuentra inactivo.";
        } elseif (
            !password_verify(
                $password,
                $usuarioBD["password"]
            )
        ) { $mensaje =
                "El usuario o la contraseña son incorrectos.";
        } else {
            session_regenerate_id(true);
            $_SESSION["usuario"] =
                $usuarioBD["usuario"];
            $_SESSION["id_usuario"] =
                $usuarioBD["id_usuario"];
            $_SESSION["id_empleado"] =
                $usuarioBD["id_empleado"];
            $_SESSION["id_rol"] =
                $usuarioBD["id_rol"];

            $sqlAcceso = "
                UPDATE usuarios
                SET ultimo_acceso = NOW()
                WHERE id_usuario = ?";

            $stmtAcceso =
                $conexion->prepare($sqlAcceso);
            $stmtAcceso->bind_param(
                "i",
                $usuarioBD["id_usuario"]
            );

            $stmtAcceso->execute();
            $stmtAcceso->close();


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



    <div class="welcome-title">
        KARDEX
    </div>

    <section class="login-form-section">


        <div class="login-form-card">



            <div class="login-form-header">

                <h1>
                    Iniciar sesión
                </h1>

                

            </div>



            <?php if ($mensaje !== ""): ?>

                <div class="login-message">

                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </div>

            <?php endif; ?>




        <form method="POST" action="index.php" class="login-form">


                <div class="login-form-group">


                    <label for="usuario">
                        Usuario
                    </label>


                    <div class="login-input-wrapper">

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



                <div class="login-form-group">


                    <label for="password">
                        Contraseña
                    </label>


                    <div class="login-input-wrapper">


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

<div class="buttons-container">
    
              <button type="submit" class="login-button">
            Iniciar sesión
        </button>

        </form>

     <div class="register-section">

        <a href="registro.php" class="register-button">
            Registrarse
        </a>

        <p>¿No tienes una cuenta?</p>

      </div>

     </div>

        </div>


    </section>


</div>


</body>

</html>

