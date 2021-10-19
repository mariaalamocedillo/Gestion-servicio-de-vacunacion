<?php
// Include config file
require_once "config/configuracion.php";
// Initialize the session
session_start();

// Si esté logeado como empleado, se redirige al inicio. Si es como paciente, se cierra sus sesión
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if (isset($_SESSION["num_identif"])){
        header("location: inicio.php");
        exit;
    } elseif (isset($_SESSION["DNI"])){
        session_unset();
        session_destroy();
        session_start();
    }
}

// Define variables and initialize with empty values
$num_identif = $password = "";
$num_identif_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["num_identif"]))){
        $num_identif_err = "Introduzca un número de identificación válido";
    } else{
        $num_identif = trim($_POST["num_identif"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Introduzca su contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($num_identif_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, num_identif, passwd FROM usuarios WHERE num_identif = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_num_identif);

            // Set parameters (in the correct variable type)
            $param_num_identif = (int)$num_identif;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $result = $stmt->get_result();

                // Check if the id number exists, if yes then verify password
                if($result->num_rows == 1){
                    // Bind result variables
                    $fila = $result->fetch_assoc();
                    if(password_verify($password, $fila["passwd"])){
                        // Password is correct, so start a new session

                        if(!isset($_SESSION)) {
                            session_start();
                        }

                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $fila["id"];
                        $_SESSION["num_identif"] = $fila["num_identif"];
                        $_SESSION['start'] = time();
                        $_SESSION['expire'] = $_SESSION['start'] + (30 * 60); //por seguridad, las sesiones duran 30 min
                        // Redirect user to welcome page
                        header("location: inicio.php");
                    }else{
                        //contraseña incorrecta; no concretamos que es la contraseña por seguridad
                        $password_err = "Número o contraseña inválidos.";
                    }
                }else{
                    // Username doesn't exist, display a generic error message
                    $password_err = "Número o contraseña inválidos.";
                }
            }
        } else{
            echo "Oops! Algo salió mal. Inténtelo de nuevo.";
        }

            // Close statement
            $stmt->close();

    }

    // Close connection
    $mysqli->close();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Sign in</title>


    <!-- Bootstrap core CSS-->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/utillogin.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">

    <style>
        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

</head>
<body class="text-center bg-light">
    <div class="container">
        <div class="col-6 m-auto mt-5">
            <form class=" validate-form flex-sb flex-w"
                  action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">

                <img class="col-3 mb-4" src="css/SaludMadrid.svg" width=50>
                <div class="col-7 mb-4">
                    <span class="login100-form-title text-center mb-3">Login</span>
                    <span>Este portal es de uso exclusivo para sanitarios. Si desea acceder a la información
                        sobre su vacunación, o desea solicitar una cita, acceda <a href="identificacion.php">aquí</a></span>
                </div>

                <span class="p-b-11 p-t-11">Código de empleado o colegiado </span>
                    <input type="text" id="input_num_id" name="num_identif"  class="form-control <?php echo (!empty($num_identif_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $num_identif; ?>"
                           placeholder="Codigo identificacion" autofocus>
                <span class="invalid-feedback"><?php echo $num_identif_err; ?></span>
                <span class="p-b-11 p-t-11">Contraseña</span>
                    <input type="password" id="inputPassword" name="password"  class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>"
                           placeholder="Password" >
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <div class="flex-sb-m w-full p-b-48">
                        <a href="registro.php" style="float: right">
                            Crear una nueva cuenta
                        </a>
                </div>

                <div class="m-auto">
                    <button class="btn btn-primary text-center sizefull">
                        Login
                    </button>
                </div>

            </form>
        </div>
    </div>
</body>
<script src="js/jquery-3.2.1.min.js">
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>

</html>

