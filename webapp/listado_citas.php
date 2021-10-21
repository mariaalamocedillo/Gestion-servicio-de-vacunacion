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
    <title>Listado citas</title>
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
        //por defecto muestra todas las citas
        document.addEventListener("DOMContentLoaded", function event() {
            showCentro('all');
        });
        //función que mostrará las citas de los centros disponibles, según el valor seleccionado
        function showCentro(str) {
            if (str === "") {
                document.getElementById("tabla").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                        document.getElementById("tabla").innerHTML = this.responseText;
                };
                xmlhttp.open("GET","gettable.php?centros="+str,true);
                xmlhttp.send();
            }
        }
    </script>
</head>
<body>
<!--barra de navegación-->
<nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
    <img class="d-block mb-4 justify-content-center mt-auto mb-auto" src="css/SaludMadrid.svg" width="70" alt="">
            <div class="nav-item">
                <a class="nav-link" href="inicio.php">Inicio</a>
            </div>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="registros" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Registros</a>
                <div class="dropdown-menu" aria-labelledby="registros">
                    <a class="dropdown-item" href="listado_vacunas.php">Vacunas</a>
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
                        <h2 class="pull-left">Lista citas</h2>
                    <?php
                    //mostramos un desplegable con los centros en los cuales buscar las citas
                    echo "<form class='pull-right mt-3'>"
                        . "<select name='filtro_centro' onchange='showCentro(this.value)'>"
                        . "<option value='all'>Todos los centros</option>";
                    //comprobamos si tenemos su centro de trabajo o localidad; en el caso de la localidad, mostramos los centros de su zona
                    if(isset($_COOKIE["centro_trab"])) {
                        //guardamos el centro de trabajo desde la cookie creada
                        $centro_trabajo = str_replace("-", " ", $_COOKIE["centro_trab"]);

                        //creamos un comando sql que mostrará el resto de centros (no mostrará la cookie, que será la primera mostrada
                        $sql = "SELECT * FROM centros WHERE nombre NOT LIKE '" . $centro_trabajo . "' AND vacunacion = 1";

                        //mostramos primero su centro de trabajo y luego el resto de centros (en valor necesitamos que esté separado por guiones, por eso reusamos la cookie)
                        echo "<option value=" . $_COOKIE["centro_trab"] . ">Mi centro (" . $centro_trabajo . ")";

                    } elseif (isset($_COOKIE["localidad_trab"])){
                        $localidad = str_replace("-", " ", $_COOKIE["localidad_trab"]);

                        //creamos un comando sql que mostrará los de la localidad
                        $sql = "SELECT * FROM centros WHERE localidad LIKE '" . $localidad . "' AND vacunacion = 1";
                    }
                    if ($result = $mysqli->query($sql)) {
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_array()) {
                                    echo "<option value='" . str_replace(" ", "-", $row["nombre"]) . "'>" . $row["nombre"] . "</option>";
                            }
                        }
                    }
                    // Free result set
                    $result->free();

                    echo "</select>"
                          . "</form>"
                    . "</div>";
                    //en este div se mostrarán las citas
                    echo "<div id='tabla'></div>"

                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>