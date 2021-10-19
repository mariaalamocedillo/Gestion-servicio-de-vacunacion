<?php
// Initialize the session
session_start();

// Si no está logeado como empleado, lo llevamos a la página de login.
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["num_identif"])){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config/configuracion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado vacunas</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        .wrapper{
            width: 900px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 120px;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
<!--barra de navegación-->
<nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
    <img class="d-block mb-4 justify-content-center mt-auto mb-auto" src="css/SaludMadrid.svg" width="70">
            <div class="nav-item">
                <a class="nav-link" href="inicio.php">Inicio</a>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="registros" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Registros</a>
                <div class="dropdown-menu" aria-labelledby="registros">
                    <a class="dropdown-item" href="listado_citas.php">Citas</a>
                    <a class="dropdown-item" href="listado_vacunados.php">Vacunados</a>
                </div>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="logout.php">Salir</a>
            </div>
</nav>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Lista vacunas</h2>
                        <a href="create.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nueva vacuna</a>
                    </div>
                    <?php

                    // Attempt select query execution
                    $sql = "SELECT * FROM vacuna";
                    if($result = $mysqli->query($sql)){
                        if($result->num_rows > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Nombre</th>";
                                        echo "<th>Fabricante</th>";
                                        echo "<th>Núm. dosis</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['nombre'] . "</td>";
                                        echo "<td>" . $row['fabricante'] . "</td>";
                                        echo "<td>" . $row['num_dosis'] . "</td>";
                                        echo "<td>";
                                            echo '<a href="read.php?id='. $row['id'] .'" class="mr-3" title="Detalles" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                            echo '<a href="update.php?id='. $row['id'] .'" class="mr-3" title="Actualizar" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                            echo '<a href="delete.php?id='. $row['id'] .'" title="Borrar" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo '<div class="alert alert-danger"><em>No se encontraron registros.</em></div>';
                        }
                    } else{
                        echo "Oops! Algo fue mal. Inténtelo más tarde.";
                    }
                    
                    // Close connection
                    $mysqli->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>