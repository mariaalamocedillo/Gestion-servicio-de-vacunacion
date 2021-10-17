<?php
// Include config file
require_once "config/configuracion.php";


    //eliminamos cuaquier cookie que pueda estar aún abierta para escribir otra
    setcookie("centro_trab",null, time() - (3600));
    setcookie("localidad_trab",null, time() - (3600));

    $param_centro = str_replace("-", " ", $_GET['centros']);


$sql = "SELECT * FROM centros WHERE nombre = '" . $param_centro . "'";

    if ($stmt = $mysqli->prepare($sql)) {
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // Check number of rows in the result set
            if ($result->num_rows > 0) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                //si es centro de vacunación, guardaremos el nombre, en caso de que no lo sea, guardamos la localidad
                if ($row["vacunacion"] == 1) {
                    setcookie("centro_trab", str_replace(" ", "-", $row["nombre"]), time() + (60 * 60), "/");
                } else {
                    setcookie("localidad_trab", str_replace(" ", "-", $row["localidad"]), time() + (60 * 60), "/");
                }
            } else {
                echo "No se encontró dicho centro de trabajo";
            }
        }


    }

    // Close statement
    $stmt->close();

// Close connection
$mysqli->close();
