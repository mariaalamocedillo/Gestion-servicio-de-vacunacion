<?php
// Initialize the session
session_start();

// Si está logeado con cualquier tipo de cuenta, se redirige a la página de inicio correspondiente
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
    if(isset($_SESSION["DNI"])){
        header("location: inicio_pctes.php"); //es un paciente
        exit;
    } elseif(isset($_SESSION["num_identif"])){
        header("location: inicio.php"); //es un empleado
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Gestión vacunación COVID-19</title>
    <meta content="Página de inicio para los usuarios (sanitarios y otros empleados)" name="description">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="css/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">


    <!-- Template Main CSS File -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<!-- Navigation bar -->
<header id="header" class="d-flex align-items-center">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="logo">
            <a href="inicio.php"><img class="d-block mb-4 justify-content-center mt-auto mb-auto" src="css/SaludMadrid.svg" width="70"></a>
        </div>
    </div>
</header>
<!-- ======= Hero Section ======= -->
<section id="hero">
    <div class="hero-container">
        <div id="heroCarousel" data-bs-interval="5000" class="carousel slide carousel-fade" data-bs-ride="carousel">

            <div class="carousel-inner" role="listbox">
                <!-- Slide 1 -->
                <div class="carousel-item active" style="background: url(img/slide/slide-1.jpg)">
                    <div class="carousel-container">
                        <div class="carousel-content">
                            <h2 class="animate__animated animate__fadeInDown">Bienvenido al portal de <span>vacunación COVID-19</span></h2>
                            <div class="animate__animated animate__fadeInUp">
                                <a href="login.php" class="btn-get-started animate__animated animate__fadeInUp">Soy empleado</a>
                                <a href="identificacion.php" class="btn-get-started animate__animated animate__fadeInUp">Soy paciente</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- End Hero -->
</body>

</html>