<?php
// Initialize the session
session_start();

//si no se ha identificado como paciente, le llevamos a identificacion
if(!isset($_SESSION["DNI"])){
    header("location: identificacion.php");
    exit;
}

// Include config file
require_once "config/configuracion.php";

$num_dosis = $centro_vacunacion = $fecha = "";
$DNI = $_SESSION["DNI"];
$sindosis = $sincita = false;

// Buscamos si tiene citas
$sql = "SELECT num_dosis, centro_vacunacion, fecha FROM citas WHERE DNI = ?";

if ($stmt = $mysqli->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $param_DNI);

    // Set parameters
    $param_DNI = $DNI;

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if($stmt->num_rows() == 0){
            $sindosis = true;
        } else {
            //asignamos los valores
            $num_dosis = $row["num_dosis"];
            $centro_vacunacion = $row["centro_vacunacion"];
            $fecha = $row["fecha"];
        }
    } else {
        echo "Oops! Algo fue mal. Inténtelo más tarde.";
    }
}

// Close statement
$stmt -> close();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Gestión vacunación COVID-19</title>
    <meta content="Página de inicio para los usuarios (sanitarios y otros empleados)" name="description">

    <!-- Favicons -->
    <link rel="shortcut icon" href="https://www.comunidad.madrid/sites/all/themes/drpl/favicon.ico" type="image/vnd.microsoft.icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="css/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="css/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="css/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<!-- Navigation bar -->
<header id="header" class="d-flex align-items-center">
    <div class="container d-flex justify-content-between align-items-center">

        <div class="logo">
            <a href="inicio_pctes.php"><img class="d-block mb-4 justify-content-center mt-auto mb-auto" src="css/SaludMadrid.svg" width="70"></a>
        </div>
        <div class="botones">
            <a href="logout.php">Salir</a>
        </div>

    </div>
</header>
<!-- ======= Hero Section ======= -->
<section id="hero">
    <div class="hero-container">
        <div id="heroCarousel" data-bs-interval="5000" class="carousel slide carousel-fade" data-bs-ride="carousel">


            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active" style="background: url(img/slide/slide-1.jpg)">
                    <div class="carousel-container">
                        <div class="carousel-content">
                            <h2 class="animate__animated animate__fadeInDown">Bienvenido al portal de <span>vacunación COVID-19</span></h2>
                            <p class="animate__animated animate__fadeInUp">
                                En este portal podrá encontrar información sobre sus citas para vacunarse, solicitar citas o obtener información sobre sus previas dosis
                                Si quiere solicitar una cita para vacunarse, entre en el siguiente enlace:
                            </p>
                            <a href="autocita.php" class="btn-get-started animate__animated animate__fadeInUp">Autocita</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- End Hero -->

<main id="main">

    <!-- ======= Featured Section ======= -->
    <section id="featured" class="featured">
        <div class="container">

            <div class="row">
                <div class="col">
                        <h3>Citas pendientes</h3>
                        <?php
                        if ($sincita){
                            echo "No tiene ninguna cita pendiente";
                        } else{
                            echo "Tiene una cita en el centro " . $row["centro_vacunacion"] . " para su " . $row["num_dosis"] . " dosis el día: " . $row["fecha"];
                        }
                        ?>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col">
                        <h3>Vacunaciones previas</h3>
                        <?php
                        // Buscamos si le han aplicado alguna dosis
                        $sql = "SELECT * FROM registro_vacunados WHERE DNI = ?";

                        if ($stmt = $mysqli->prepare($sql)) {
                            // Bind variables to the prepared statement as parameters
                            $stmt->bind_param("s", $param_DNI);

                            // Set parameters
                            $param_DNI = $DNI;

                            // Attempt to execute the prepared statement
                            if ($stmt->execute()) {
                                $result = $stmt->get_result();
                                if($result->num_rows == 0){
                                    echo "Aún no se le ha aplicado ninguna dosis";
                                } else{
                                    while($fila = $result->fetch_array()) {
                                        echo "<div> Se le aplicó su " .$fila["num_dosis"]. "º dosis en el centro " .$fila["centro_vacunacion"]. ":";
                                        echo "<br>&nbsp;&nbsp; Fecha:" .$fila["fecha_vacunacion"];
                                        echo "<br>&nbsp;&nbsp; Num. lote: ".$fila["num_lote"];
                                        echo "<br>&nbsp;&nbsp; Fabricante: ".$fila["fabricante"].".</div>" ;
                                    }
                                    if ($result->num_rows == 2){
                                        echo "\n---Ya se ha completado su vacunación---";
                                    }
                                }
                            } else {
                                echo "Oops! Algo fue mal. Inténtelo más tarde.";
                            }
                        }

                        // Close statement
                        $stmt -> close();

                        $mysqli->close();
                        ?>
                </div>
            </div>
        </div>

    </section><!-- End Featured Section -->

</body>

</html>