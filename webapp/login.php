<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: listado_citas.php");
    exit;
}

// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$num_identif = $password = "";
$num_identif_err = $password_err = $login_err = "";

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
                        // Redirect user to welcome page
                        header("location: listado_citas.php");
                    }
                }
            } else{
                // Username doesn't exist, display a generic error message
                $login_err = "Número o contraseña inválidos.";
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
<body class="text-center">
<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-l-85 p-r-85 p-t-55 p-b-55">
            <form class="login100-form validate-form flex-sb flex-w" action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
					<span class="login100-form-title p-b-32">
						Login
					</span>

                <span class="p-b-11">
						Código de empleado o colegiado
					</span>
                <div class="wrap-input100 validate-input m-b-36" data-validate = "Num_identif is required">
                    <input type="text" id="input_num_id" name="num_identif" class="form-control" placeholder="Email address" required autofocus>
                </div>

                <span class=" p-b-11">
						Contraseña
					</span>
                <div class="wrap-input100 validate-input m-b-12" data-validate = "Password is required">
						<!--<span class="btn-show-pass">
							<i class="fa fa-eye" id="togglePassword"></i>
						</span>-->
                    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="flex-sb-m w-full p-b-48">
                    <div>
                        <a href="registro.php">
                            Crear una nueva cuenta
                        </a>
                        <a href="reset-password.php">
                            Olvidé mi contraseña
                        </a>
                    </div>
                </div>

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn">
                        Login
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<div id="dropDownSelect1"></div>

</body>
<script src="js/jquery-3.2.1.min.js">
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>

</html>

