<?php
// Initialize the session
session_start();

// Si ya está logeado con una sesión de paciente, redirigimos al inicio. Si es un empleado, cerramos su sesión
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["DNI"])) {
        header("location: inicio_pctes.php");
        exit;
    } else if (isset($_SESSION["num_identif"])) {
        session_unset();
        session_destroy();
        session_start();
    }
}

// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$DNI = $apellidos = $nacimiento = "";
$DNI_err = $apellidos_err = $nacimiento_err = $vacunacion_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate his/her identity
    if(empty(trim($_POST["apellidos"]))){
        $apellidos_err = "Introduzca sus apellidos.";
    } else{
        $apellidos = trim($_POST["apellidos"]);
    }
    if(empty(trim($_POST["DNI"])) || !preg_match('/^\d{8}[a-zA-Z]$/', trim($_POST["DNI"]))){
        $DNI_err = "Introduzca su DNI.";
    } else{
        $DNI = trim($_POST["DNI"]);
    }
    if(empty(trim($_POST["nacimiento"]))){
        $nacimiento_err = "Introduzca su fecha de nacimiento.";
    } else{
        $nacimiento = trim($_POST["nacimiento"]);
    }

    if (empty($nacimiento_err) && empty($DNI_err) && empty($apellidos_err)){
        // Prepare a select statement
        $sql = "SELECT * FROM pacientes WHERE apellidos = ? AND DNI = ? AND nacimiento = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_apellido, $param_DNI, $param_nacimiento);

            // Set parameters
            $param_apellido = strtoupper(trim($_POST["apellidos"]));
            $param_DNI = trim($_POST["DNI"]);
            $param_nacimiento = trim($_POST["nacimiento"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $result = $stmt->get_result();
                //si se encuentra registros sobre dicho paciente, le redirigimos a la siguiente página creando una sesión
                if($result->num_rows > 0){
                    // Bind result variables
                    $fila = $result->fetch_assoc();

                    if(!isset($_SESSION)) {
                        session_start();
                    }

                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["DNI"] = $fila["DNI"];
                    $_SESSION["nombre"] = $fila["nombre"];
                    $_SESSION['start'] = time();
                    $_SESSION['expire'] = $_SESSION['start'] + (30 * 60); //por seguridad, las sesiones duran 30 min

                    // Redirect user to inicio
                    header("location: inicio_pctes.php");
                } else{ //si no se encuentra, ponemos un mensaje de error (usamos el del DNI)
                    $vacunacion_err = "No encontramos sus datos en nuestra base de datos. Comunique este error a su centro de salud para subsanarlo";
                }
            } else{
                echo "Oops! Algo salió mal. Inténtelo de nuevo más tarde.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autocita</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }

    </style>
</head>
<body class="bg-light">

<div class="container">

    <div class="py-5 align-items-center">
        <div class="col-md-10 ml-5">
            <h2 class="text-center">Servicio de citación de vacuna SARS-CoV-2 (COVID19)</h2>
            <div class="row">
                <div class="col-md-4 float-left">
                    <img class="d-block mx-auto mb-4" src="css/SaludMadrid.svg" alt="" width="150" height="150">
                </div>
                <div class="col-md-8 lead mt-3">Si usted es menor y no dispone de DNI o ha tenido problemas para
                    acceder de alguna de las maneras, por favor póngase en contacto con el 900102112.</div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">

        <div class="col-md-10 order-md-1">

            <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">

                <div class="col-md mb-3">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos"
                           class="form-control <?php echo (!empty($apellidos_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $apellidos; ?>">
                    <span class="invalid-feedback"><?php echo $apellidos_err; ?></span>
                </div>

                <div class="col-md mb-3">
                    <label for="DNI">DNI/NIE</label>
                    <input type="text" name="DNI"
                           class="form-control <?php echo (!empty($DNI_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $DNI; ?>">
                    <span class="invalid-feedback"><?php echo $DNI_err; ?></span>
                </div>

                <div class="col-md mb-3">
                    <label>Fecha de nacimiento</label>
                    <input type="date" id="nacimiento" name="nacimiento"
                           class="form-control <?php echo (!empty($nacimiento_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $nacimiento; ?>" min="1900-01-01" max="<?php echo date('Y-m-d'); ?>">
                    <span class="invalid-feedback"><?php echo $nacimiento_err; ?></span>
                </div>

                <div class="text-center"><?php echo $vacunacion_err ;?></div>

                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Siguiente">
                    <input type="reset" class="btn btn-secondary ml-2" value="Vaciar formulario">
                </div>
            </form>

        </div>
</body>
</html>
