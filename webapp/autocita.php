<?php
// Include config file
require_once "config/configuracion.php";
// Initialize the session
session_start();

// Si no está logeado como paciente, le enviamos a la página de identificación
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["DNI"])){
    header("location: identificacion.php");
    exit;
}


// Define variables and initialize with empty values
$num_dosis = $centro_vacunacion = $fecha = $dia_cita = $rango = "";
$num_dosis_err = $centro_vacunacion_err = $fecha_err
    = $dia_cita_err = $rango_err = $vacunacion_err = "";

//Comprobamos que no tenga ninguna cita activa
$sql = "SELECT * FROM citas WHERE DNI = '".$_SESSION["DNI"]."'";
if($stmt = $mysqli->prepare($sql)) {
    if ($stmt->execute()) {
        // store result
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $vacunacion_err = "No puede solicitar otra cita porque ya tiene una activa; acceda al <a href='inicio_pctes.php'> inicio </a> para más información";
        }
    } else {
        echo "Oops! Algo salió mal. Inténtelo de nuevo más tarde.";
    }
}
// Close statement
$stmt->close();

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){



    //Validamos el centro
    if (trim($_POST["centro"]) == "0") {
        $centro_vacunacion_err = "Debe seleccionar un centro";
    } else {
        $centro_vacunacion = trim($_POST["centro"]);
    }

    //Validamos la fecha; añadimos la hora, la cual será aleatoria
    if (empty(trim($_POST["dia"]))) {
        $dia_cita_err = "Debe seleccionar un día";
    } else {
        $dia_cita = trim($_POST["dia"]);
    }

    if ($_POST["rango"] == "vacio") {
        $rango_err = "Debe seleccionar un rango horario";
    } else {
        $rango = trim($_POST["rango"]);
        //asignamos una hora aleatoria, en un intervalo de 3 min
        $hora = rand($rango, (int)$rango+3);
        if($hora < 10)
            $hora = "0".$hora;
        $min = 2;
        while (($min % 3) != 0){
            $min = rand(00, 59);
            if($min < 10)
                $min = "0".$min;
        }
        $horario = $hora . ":" . $min .":00";
    }

    if (empty($dia_cita_err) && empty($rango_err)) {
        $fecha = date('Y-m-d h:i:s', strtotime($dia_cita ." ". $horario));
    }

    //Buscamos cuántas dosis se le han aplicado
    $sql = "SELECT id FROM registro_vacunados WHERE DNI = ?";

    if($stmt = $mysqli->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_DNI);

        // Set parameters
        $param_DNI = $_SESSION["DNI"];

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // store result
            $stmt->store_result();

            if ($stmt->num_rows >= 2) {
                $vacunacion_err = "Su vacunación ya se ha completado";
                header("location: inicio_pctes.php");
                exit;
            } else {
                //sumamos uno al número de dosis que se le ha aplicado
                $num_dosis = $stmt->num_rows + 1;
            }
        } else {
            echo "Oops! Algo salió mal. Inténtelo de nuevo más tarde.";
        }
    }
        // Close statement
        $stmt->close();


    // Check input errors before inserting in database
    if(empty($centro_vacunacion_err) && empty($fecha_err) && empty($vacunacion_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO citas (DNI, num_dosis, centro_vacunacion, fecha) VALUES (?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_DNI, $param_numdosis,
                $param_centro, $param_fecha);

            // Set parameters
            $param_DNI = $_SESSION["DNI"];
            $param_numdosis = $num_dosis;
            $param_centro = $centro_vacunacion;
            $param_fecha = $fecha;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: inicio_pctes.php");
            } else {
                echo "Oops! Algo salió mal. Inténtelo de nuevo más tarde.";
            }

            // Close statement
            $stmt->close();
        }
    }
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
                <div class="col-md-8 lead mt-3">Indique sus preferencias para su cita</div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">

        <div class="col-md-10 order-md-1">

        <form style="" action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">

                <div class="col-md mb-3">
                    <label for="">Centro de vacunación</label>
                    <select class="form-control <?php echo (!empty($centro_vacunacion_err)) ? 'is-invalid' : ''; ?>
                                        custom-select d-block w-100" id="centro" name="centro" required="">
                        <option value="0">Seleccione...</option>
                        <?php
                        $sql = "SELECT * FROM centros WHERE vacunacion = 1";
                        if($result = $mysqli->query($sql)){
                            if($result->num_rows > 0){
                                while($row = $result->fetch_array()){
                                    echo "<option value='" .$row['nombre']."'>" .$row['nombre']. ", " .$row['localidad']. "</option>";
                                }
                                // Free result set
                                $result->free();
                            }
                        } else{
                            echo "Oops! Algo fue mal. Inténtelo más tarde.";
                        }
                        // Close connection
                        $mysqli->close();
                        ?>
                    </select>
                    <span class="invalid-feedback"><?php echo $centro_vacunacion_err; ?></span>
                </div>
                <div class="col-md mb-3">
                    <label>Día</label>
                    <input type="date" id="dia" name="dia"
                           class="form-control <?php echo (!empty($dia_cita_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $dia_cita; ?>" min="<?php echo date('Y-m-d'); ?>" max="2022-05-05">
                    <span class="invalid-feedback"><?php echo $dia_cita_err; ?></span>
                </div>

                <div class="col-md mb-3">
                    <label for="rango">Rango de horario</label>
                    <select class="form-control <?php echo (!empty($rango_err)) ? 'is-invalid' : ''; ?>
                                    custom-select d-block w-100" id="rango" name="rango" required="">
                        <option value="vacio">Seleccione...</option>
                        <option value="8">08:00 - 12:00</option>
                        <option value="12">12:00 - 16:00</option>
                        <option value="16">16:00 - 20:00</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $rango_err; ?></span>
                </div>

                <div class="flex-sb-m w-full p-b-48">
                    ¿Ya tienes cita?<a href="inicio_pctes.php">
                        Consultar citas
                    </a>
                </div>

                <span><?php echo $vacunacion_err ;?></span>
                <div class="form-group text-center mt-1">
                    <input type="submit" class="btn btn-primary" value="Confirmar" <?php if(!empty($vacunacion_err)) echo "disabled";?>>
                    <input type="reset" class="btn btn-secondary ml-2" value="Vaciar datos">
                </div>
        </form>

        </div>
</body>
</html>
