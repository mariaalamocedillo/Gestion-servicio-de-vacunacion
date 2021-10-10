<?php
// Include config file
require_once "config/configuracion.php";
 
// Define variables and initialize with empty values
$nombre = $nombrelargo = $fabricante = $numdosis = "";
$tiempominimo =  $tiempomaximo = "";
$nombre_err = $nombrelargo_err = $fabricante_err = $numdosis_err ="";
$tiempominimo_err =  $tiempomaximo_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];

    // Validate nombre
    $input_nombre = trim($_POST["nombre"]);
    if(empty($input_nombre)){
        $nombre_err = "Please enter a name.";
    } elseif(!filter_var($input_nombre, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $nombre_err = "Please enter a valid name.";
    } else{
        $nombre = $input_nombre;
    }

    // Validate nombre_largo
    $input_nombrelargo = trim($_POST["nombrelargo"]);
    if(empty($input_nombrelargo)){
        $nombrelargo_err = "Please enter a name.";
    } elseif(!filter_var($input_nombrelargo, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $nombrelargo_err = "Please enter a valid name.";
    } else{
        $nombrelargo = $input_nombrelargo;
    }

    // Validate fabricante
    $input_fabricante = trim($_POST["fabricante"]);
    if(empty($input_fabricante)){
        $fabricante_err = "Please enter an address.";
    } else{
        $fabricante = $input_fabricante;
    }

    // Validate numdosis
    $input_numdosis = trim($_POST["numdosis"]);
    if(empty($input_numdosis)){
        $numdosis_err = "Please enter the salary amount.";
    } elseif(!ctype_digit($input_numdosis)){
        $numdosis_err = "Please enter a positive integer value.";
    } else{
        $numdosis = $input_numdosis;
    }

    $tiempominimo = trim($_POST["tiempominimo"]);
    $tiempomaximo = trim($_POST["tiempomaximo"]);
    
    // Check input errors before inserting in database
    if(empty($nombre_err) && empty($fabricante_err) && empty($nombrelargo_err)&& empty($numdosis_err)){
        // Prepare an update statement
        $sql = "UPDATE vacuna SET nombre=?, nombre_largo=?, fabricante=?, num_dosis=?, tiempo_minimo=?, tiempo_maximo=? WHERE id=?";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssiiii", $param_nombre, $param_nombrelargo, $param_fabricante,
                $param_numdosis, $param_tiempominimo, $param_tiempomaximo, $param_id);

            // Set parameters
            $param_nombre = $nombre;
            $param_nombrelargo = $nombrelargo;
            $param_fabricante = $fabricante;
            $param_numdosis = $numdosis;
            $param_tiempominimo = $tiempominimo;
            $param_tiempomaximo = $tiempomaximo;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
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
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM vacuna WHERE id = ?";
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $nombre = $row["nombre"];
                    $nombrelargo = $row["nombre_largo"];
                    $fabricante = $row["fabricante"];
                    $numdosis = $row["num_dosis"];
                    $tiempominimo = $row["tiempo_minimo"];
                    $tiempomaximo = $row["tiempo_maximo"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Algo fue mal. Please try again later.";
            }
        }
        
        // Close statement
        $stmt->close();
        
        // Close connection
        $mysqli->close();
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar vacuna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                    <h2 class="mt-5">Actualizar Vacuna</h2>
                    <p>Por favor, edita los campos de Vacuna que desees modificar.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control <?php echo (!empty($nombre_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombre; ?>">
                            <span class="invalid-feedback"><?php echo $nombre_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Nombre largo</label>
                            <input type="text" name="nombrelargo" class="form-control <?php echo (!empty($nombrelargo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombrelargo; ?>">
                            <span class="invalid-feedback"><?php echo $nombrelargo_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Fabricante</label>
                            <textarea name="fabricante" class="form-control <?php echo (!empty($fabricante_err)) ? 'is-invalid' : ''; ?>"><?php echo $fabricante; ?></textarea>
                            <span class="invalid-feedback"><?php echo $fabricante_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Número de dosis</label>
                            <input type="text" name="numdosis" class="form-control <?php echo (!empty($numdosis_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $numdosis; ?>">
                            <span class="invalid-feedback"><?php echo $numdosis_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Tiempo mínimo</label>
                            <input type="text" name="tiempominimo" class="form-control <?php echo (!empty($tiempominimo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tiempominimo; ?>">
                            <span class="invalid-feedback"><?php echo $tiempominimo_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Tiempo máximo</label>
                            <input type="text" name="tiempomaximo" class="form-control <?php echo (!empty($tiempomaximo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tiempomaximo; ?>">
                            <span class="invalid-feedback"><?php echo $tiempomaximo_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Guardar">
                        <a href="listado_vacunas.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>