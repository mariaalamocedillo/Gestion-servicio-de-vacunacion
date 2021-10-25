<?php
session_start();
// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$DNI = $num_dosis = $centro_vacunacion = $fabricante = $num_lote = "";
$fabricante_err = $num_lote_err = $id_err = "";

//almacenamos el id en una sesión
if(!isset($_COOKIE['id_cita'])){
    setcookie("id_cita", trim($_GET["id"]), time() + (60 * 5), "/");
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Validar fabricante
    if(!strcmp(trim($_POST["fabricante"]), "vacio")){
        $fabricante_err = "Introduzca el fabricante.";
    } else{
        $fabricante = trim($_POST["fabricante"]);
    }

    // Validar lote
    if(empty(trim($_POST["num_lote"]))){
        $num_lote_err = "Introduzca el número de lote.";
    } elseif (!is_numeric(trim($_POST["num_lote"]))){
        $num_lote_err = "El número de lote debe ser numérico.";
    } else{
        $num_lote = trim($_POST["num_lote"]);
    }

    // Process confirm operation after submit
    if (isset($_COOKIE['id_cita'])) {

        // Recogemos los datos de la cita para usarlos posteriormente en el registro de vacunado
        $sql = "SELECT DNI, num_dosis, centro_vacunacion FROM citas WHERE id_cita = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_id);

            // Set parameters
            $param_id = $_COOKIE['id_cita'];

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                //asignamos los valores
                if($result->num_rows == 1){
                    $DNI = $row["DNI"];
                    $num_dosis = $row["num_dosis"];
                    $centro_vacunacion = $row["centro_vacunacion"];
                } else {
                    echo "Oops! Algo fue mal. Inténtelo más tarde.";
                    $id_err = "Se produjo un error";
                }
            } else {
                echo "Oops! Algo fue mal. Inténtelo más tarde.";
                $id_err = "Se produjo un error";
            }
        }

        // Close statement
        $stmt -> close();


        //Insertamos los datos en la tabla de registros de vacunados
        $sqlins = "INSERT INTO registro_vacunados (DNI, num_dosis, fabricante, num_lote, centro_vacunacion) VALUES (?, ?, ?, ?, ?)";

        if(empty($fabricante_err) && empty($num_lote_err) && empty($id_err)){
            // Prepare an insert statement
            if($stmt = $mysqli->prepare($sqlins)){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("sisis", $param_DNI, $param_numdosis, $param_fabricante,
                    $param_numlote, $param_centrovacunacion);

                // Set parameters
                $param_DNI = $DNI;
                $param_numdosis = $num_dosis;
                $param_fabricante = $fabricante;
                $param_numlote = $num_lote;
                $param_centrovacunacion = $centro_vacunacion;


                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Records created successfully. Redirect to landing page
                } else{
                    echo "Oops! Algo fue mal. Inténtelo más tarde.";
                }
            }
            //Ahora borramos el registro de dicha cita en la tabla de citados
            $sqldel = "DELETE FROM citas WHERE id_cita = ?";

            if ($stmt = $mysqli->prepare($sqldel)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("i", $param_id);

                // Set parameters
                $param_id = $_COOKIE['id_cita'];

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Records deleted successfully. Redirect to landing page
                    setcookie("id_cita", "", time() - (60 * 5), "/");
                    header("location: listado_vacunados.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }

    } else{
        echo "Parece ser que no tiene establecido un id de cita.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar cita</title>
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
                    <h2 class="mt-5 mb-3">Confirmar cita</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
                        <p>Para confirmar la vacunación, introduzca los siguientes datos:</p>
                        <div>
                            <label>Número de lote</label>
                            <input type="text" name="num_lote" class="form-control <?php echo (!empty($num_lote_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $num_lote; ?>">
                            <span class="invalid-feedback"><?php echo $num_lote_err; ?></span>
                        </div>
                        <div>
                            <label for="fabricante">Fabricante</label>
                            <select name="fabricante" class="form-control custom-select d-block w-100 <?php echo (!empty($fabricante_err)) ? 'is-invalid' : ''; ?>">
                                <option value= 'vacio'>Seleccione...</option>
                            <?php
                            $sql = "SELECT * FROM vacuna";
                            if ($result = $mysqli->query($sql)) {
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_array()) {
                                        echo "<option value='" .$row["fabricante"]. "'> " . $row["fabricante"] . "</option>";
                                    }
                                }
                                // Free result set
                                $result->free();
                            }

                            //close connection
                            $mysqli->close();
                            ?>
                            </select>

                            <span class="invalid-feedback"><?php echo $fabricante_err; ?></span>

                        </div>


                        <div class="mt-2 text-center">
                            <input type="submit" value="Confirmar" class="btn btn-danger">
                            <a href="listado_vacunas.php" class="btn btn-secondary ml-2">Cancelar</a>
                        </div>

                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>