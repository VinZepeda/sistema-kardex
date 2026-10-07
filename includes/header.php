<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $titulo ?? "Sistema de Kardex" ?></title>

    <link rel="stylesheet" href="css/estilos.css">

</head>
 
<body>

<div class="app-container">

    <header class="topbar">

        <div class="topbar-left">

            <button
                type="button"
                class="menu-button"
                onclick="alternarMenu()"
            >
                ☰
            </button>

            <h1>
                Sistema de Kardex
            </h1>

        </div>

        <div class="topbar-user">

            <span>
                👤
                <?= htmlspecialchars($_SESSION["usuario"] ?? "Usuario") ?>
            </span>

        </div>

    </header>