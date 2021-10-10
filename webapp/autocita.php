<?php
// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$DNI = $apellidos = $nacimiento = $num_dosis = $centro_vacunacion = $fecha = "";
$DNI_err = $apellidos_err = $nacimiento_err = $num_dosis_err = $centro_vacunacion_err = $fecha_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate his/her identity
    if(empty(trim($_POST["apellidos"]))){
        $apellidos_err = "Introduzca sus apellidos.";
    }
    if(empty(trim($_POST["DNI"]))){
        $DNI_err = "Introduzca su DNI.";
    }
    if(empty(trim($_POST["nacimiento"]))){
        $nacimiento_err = "Introduzca su fecha de nacimiento.";
    }

    if ($nacimiento_err = "" && $DNI_err = "" && $apellidos_err = ""){
        // Prepare a select statement
        $sql = "SELECT id FROM pacientes WHERE apellidos = ? AND DNI = ? AND nacimiento = ?";

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
                $stmt->store_result();

                if($stmt->num_rows == 1){
                    $DNI = trim($_POST["DNI"]);
                    $nacimiento = trim($_POST["nacimiento"]);
                    $apellidos = trim($_POST["apellidos"]);
                } else{
                    $DNI_err = "No encontramos sus datos en nuestra base de datos. Comunique este error en su centro de salud para subsanarlo";
                }
            } else{
                echo "Oops! Algo salió mal. Inténtelo de nuevo más tarde.";
            }

            // Close statement
            $stmt->close();
        }
    }

    //Validate range
    if (!strcmp(trim($_POST["rango"]), "vacio")) {
        $rango_err = "Seleccione una opción.";
    } else {
        $rango = trim($_POST["rango"]);
    }

    // Validate employee number
    if(empty(trim($_POST["num_identif"]))){
        $num_identif_err = "Introduzca su número de empleado o de colegiado.";
    } elseif (strlen(trim($_POST["num_identif"])) < 8) {
        $name_err = "El número debe contener al menos 8 caracteres.";
    } else{
        $num_identif = trim($_POST["num_identif"]);
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Introduzca una contraseña.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirme la contraseña.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO usuarios (nombre, email, rango, num_identif, passwd) VALUES (?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssss", $param_name, $param_email,
                $param_rango, $param_num_identif, $param_password);

            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_rango = $rango;
            $param_num_identif = $num_identif;
            $param_password = password_hash($password, PASSWORD_BCRYPT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
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
    <script type="text/javascript">
        $(".form_datetime").datetimepicker({
            format: "yyyy-MM-dd - hh:ii",
            autoclose: true,
            todayBtn: true,
            minuteStep: 10
        });
    </script>
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
                    <input type="text" class="form-control" id="apellidos" name="apellidos"
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
                </div>


                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Siguiente">
                    <input type="reset" class="btn btn-secondary ml-2" value="Vaciar formulario">
                </div>
            </form>

      <!--      <form style="" action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">

                <div class="col-md mb-3">
                    <label>Día</label>
                    <input type="date" id="nacimiento" name="nacimiento"
                           class="form-control <?php echo (!empty($dia_cita_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $dia_cita; ?>" min="<?php echo date('Y-m-d'); ?>" max="2022-05-05">
                </div>


                <div class="col-md mb-3">
                    <label for="">Rango de horario</label>
                    <select class="form-control <?php echo (!empty($rango_err)) ? 'is-invalid' : ''; ?>
                                    custom-select d-block w-100" id="rango" name="rango" required="">
                        <option value="vacio">Seleccione...</option>
                        <option>Sanitario</option>
                        <option>Gestor</option>
                        <option>Informatico</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $rango_err; ?></span>
                </div>


                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Siguiente">
                    <input type="reset" class="btn btn-secondary ml-2" value="Vaciar datos">
                </div>
            </form>
-->

        </div>
</body>
</html>
