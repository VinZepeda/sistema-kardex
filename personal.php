<?php

session_start();

if (!isset($_SESSION["usuario"])) {

    header("Location: index.php");
    exit();

}

$titulo = "Personal | Sistema de Kardex";

include "includes/header.php";
include "includes/menu.php";

?>

<main class="main-content">

    <div class="page-header">

        <div>

            <h2>
                Personal
            </h2>

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
                onkeyup="buscarEmpleado()"
            >

        </div>

    </section>


    <section class="personal-table-container">

        <table class="personal-table">

            <thead>

                <tr>

                    <th>
                        Empleado
                    </th>

                    <th>
                        Puesto
                    </th>

                    <th>
                        Área
                    </th>

                    <th>
                        Estatus
                    </th>

                </tr>

            </thead>

            <tbody id="tablaPersonal">

                <tr>

                    <td>
                    <?= htmlspecialchars($empleado["nombre"] ?? "Sin asignar") ?>

                    
                    </td>

                    <td>
                       <?= htmlspecialchars($empleado["puesto"] ?? "Sin asignar") ?>

                    </td>

                    <td>
                        <?= htmlspecialchars($empleado["area"] ?? "Sin asignar") ?>

                    </td>

                    <td>

                        <span class="status-active">
                        <?= htmlspecialchars($empleado["estatus"] ?? "Sin asignar") ?>

                        </span>

                    </td>

                </tr>

                <tr>

                    <td>
                    
                        <?= htmlspecialchars($empleado["nombre"] ?? "Sin asignar") ?>


                    </td>

                    <td>
                        <?= htmlspecialchars($empleado["puesto"] ?? "Sin asignar") ?>

                    </td>

                    <td>
                         <?= htmlspecialchars($empleado["area"] ?? "Sin asignar") ?>

                    </td>

                    <td>

                        <span class="status-active">
                            Activo
                        </span>

                    </td>
                        <?= htmlspecialchars($empleado["estatus"] ?? "Sin asignar") ?>

                </tr>

            </tbody>

        </table>

    </section>

</main>

<?php

include "includes/footer.php";

?>