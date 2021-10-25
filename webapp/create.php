<?php
// Include config file
require_once "config/configuracion.php";
session_start();
// Si no está logeado como empleado, lo llevamos a la página de login.
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["num_identif"])){
    header("location: login.php");
    exit;
}

// Definimos las variables
$nombre = $nombrelargo = $fabricante = $numdosis = "";
$tiempominimo =  $tiempomaximo = "";
$nombre_err = $nombrelargo_err = $fabricante_err = $numdosis_err ="";
$tiempominimo_err =  $tiempomaximo_err = "";
 
// procesamos la información del formulaario
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validar nombre
    $input_nombre = trim($_POST["nombre"]);
    if(empty($input_nombre)){
        $nombre_err = "Introduzca un nombre.";
    } elseif(!filter_var($input_nombre, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $nombre_err = "Introduzca un nombre válido.";
    } else{
        $nombre = $input_nombre;
    }

    // Validar el nombre completo
    $input_nombrelargo = trim($_POST["nombrelargo"]);
    if(empty($input_nombre)){
        $nombrelargo_err = "Introduzca el nombre completo.";
    } elseif(!filter_var($input_nombrelargo, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $nombrelargo_err = "Introduzca un nombre completo válido.";
    } else{
        $nombrelargo = $input_nombrelargo;
    }
    
    // Validar fabricante
    $input_fabricante = trim($_POST["fabricante"]);
    if(empty($input_fabricante)){
        $fabricante_err = "Introduzca el fabricante.";
    } else{
        $fabricante = $input_fabricante;
    }
    
    // Validar numdosis
    $input_numdosis = trim($_POST["numdosis"]);
    if(empty($input_numdosis)){
        $numdosis_err = "Introduzca el número de dosis necesarias.";
    } elseif(!ctype_digit($input_numdosis)){
        $numdosis_err = "Introduzca un número válido.";
    } else{
        $numdosis = $input_numdosis;
    }

    // Validar min time
    $input_tiempominimo = trim($_POST["tiempominimo"]);
    if(!ctype_digit($input_tiempominimo)){
        $tiempominimo_err = "Introduzca un número válido.";
    } else{
        $tiempominimo = $input_tiempominimo;
    }

    // Validar max time
    $input_tiempomaximo = trim($_POST["tiempomaximo"]);
    if(!ctype_digit($input_tiempomaximo)){
        $tiempomaximo_err = "Introduzca un número válido.";
    } else{
        $tiempomaximo = $input_tiempomaximo;
    }
    
    // Confirmamos que no hay errores y almacenamos en la base de datos
    if(empty($nombre_err) && empty($fabricante_err) && empty($nombrelargo_err)&& empty($numdosis_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO vacuna (nombre, nombre_largo, fabricante, num_dosis, tiempo_minimo, tiempo_maximo) VALUES (?, ?, ?, ?, ?, ?)";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssiii", $param_nombre, $param_nombrelargo, $param_fabricante,
                $param_numdosis, $param_tiempominimo, $param_tiempomaximo);
            
            // Set parameters
            $param_nombre = $nombre;
            $param_nombrelargo = $nombrelargo;
            $param_fabricante = $fabricante;
            $param_numdosis = $numdosis;
            $param_tiempominimo = $tiempominimo;
            $param_tiempomaximo = $tiempomaximo;

            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: listado_vacunas.php");
                exit();
            } else{
                echo "Oops! Algo fue mal. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $mysqli->close();
}
?>
 
 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Crear Vacuna</h2>
                    <p>Cumplimente la información solicitada sobre la vacuna.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control <?php echo (!empty($nombre_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombre; ?>">
                            <span class="invalid-feedback"><?php echo $nombre_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Nombre completo</label>
                            <input type="text" name="nombrelargo" class="form-control <?php echo (!empty($nombrelargo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombrelargo; ?>">
                            <span class="invalid-feedback"><?php echo $nombrelargo_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Fabricante</label>
                            <input name="fabricante" class="form-control <?php echo (!empty($fabricante_err)) ? 'is-invalid' : ''; ?>"><?php echo $fabricante; ?></input>
                            <span class="invalid-feedback"><?php echo $fabricante_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Número de dosis</label>
                            <input type="text" name="numdosis" class="form-control <?php echo (!empty($numdosis_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $numdosis; ?>">
                            <span class="invalid-feedback"><?php echo $numdosis_err;?></span>
                        </div>
                        <div class="row">
                            <div class="col form-group">
                                <label>Tiempo mínimo</label>
                                <input type="text" name="tiempominimo" class="form-control <?php echo (!empty($tiempominimo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tiempominimo; ?>">
                                <span class="invalid-feedback"><?php echo $tiempominimo_err;?></span>
                            </div>
                            <div class="col form-group">
                                <label>Tiempo máximo</label>
                                <input type="text" name="tiempomaximo" class="form-control <?php echo (!empty($tiempomaximo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tiempomaximo; ?>">
                                <span class="invalid-feedback"><?php echo $tiempomaximo_err;?></span>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Crear">
                        <a href="listado_vacunas.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>