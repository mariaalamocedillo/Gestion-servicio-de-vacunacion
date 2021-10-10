<?php
// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$email = $password = $confirm_password = $name = $rango = $num_identif = "";
$email_err = $password_err = $confirm_password_err = $name_err = $rango_err = $num_identif_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Introduzca su nombre.";
    } elseif (strlen(trim($_POST["name"])) < 3) {
        $name_err = "El nombre debe contener al menos 3 caracteres.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Introduzca un email.";
    } elseif(!preg_match('/^\S+@\S+\.\S+$/', trim($_POST["email"]))){
        $email_err = "Debe tener el patrón de email; 'ejemplo@correo.com' ";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM usuarios WHERE email = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1){
                    $email_err = "Este email ya está dado de alta.";
                } else{
                    $email = trim($_POST["email"]);
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
        $rango_err = "Seleccione su puesto de trabajo.";
    } else {
        $rango = trim($_POST["rango"]);
    }

    // Validate employee number
    if(empty(trim($_POST["num_identif"]))){
        $num_identif_err = "Introduzca su número de empleado o de colegiado.";
    } elseif (!is_numeric(trim($_POST["num_identif"]))){
        $num_identif_err = "El código debe ser numérico.";
    } elseif ( ($rango == "Informatico"|| $rango == "Gestor")
                    && strlen(trim($_POST["num_identif"])) != 8) {
        $num_identif_err = "El número de empleado debe contener 8 caracteres.";
    } elseif ( $rango == "Sanitario" && strlen(trim($_POST["num_identif"])) != 9) {
        $num_identif_err = "El número de colegiado debe contener 9 caracteres.";
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
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)
        && empty($num_identif_err) && empty($nombre_err) && empty($rango_err)){

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
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
    </style>
</head>
<body class="bg-light">

<div class="container">

    <div class="py-5">
        <h2 class="text-center">Creación de cuenta</h2>
        <div class="row">
            <div class="col-md-3">
                <img class="d-block mx-auto mb-4" src="css/SaludMadrid.svg" alt="" width="150" height="150">
            </div>
                <div class="lead col-md mt-3">Este registro está orientado a los empleados, tanto técnicos como sanitarios,
                que se encargan de gestionar la vacunación en la Comunidad de Madrid. Si ha entrado
                aquí por error para solicitar su cita de vacunación, dirígase al siguiente
                <a href="identificacion.php">enlace de Autocitas</a></div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12 order-md-1">

        <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name">Nombre completo</label>
                    <input type="text" class="form-control" id="name" name="name"
                           class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $name; ?>">
                    <span class="invalid-feedback"><?php echo $name_err; ?></span>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md mb-3">
                    <label for="rango">Rango</label>
                    <select class="form-control <?php echo (!empty($rango_err)) ? 'is-invalid' : ''; ?>
                                        custom-select d-block w-100" id="rango" name="rango" required="">
                        <option value="vacio">Seleccione...</option>
                        <option>Sanitario</option>
                        <option>Gestor</option>
                        <option>Informatico</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $rango_err; ?></span>
                </div>

                <div class="col-md-9 mb-3">
                    <label>Número de identificación (introduzca su número de empleado o de colegiado, según su puesto de trabajo)</label>
                    <input type="text" name="num_identif" class="form-control <?php echo (!empty($num_identif_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $num_identif; ?>">
                    <span class="invalid-feedback"><?php echo $num_identif_err; ?></span>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="psswd">Contraseña</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Crear cuenta">
                <input type="reset" class="btn btn-primary ml-2" value="Vaciar formulario">
            </div>
            <p>¿Ya tiene una cuenta? <a href="login.php">Entre aquí</a>.</p>
        </form>
    </div>
</body>
</html>
