<?php
// Initialize the session
session_start();
require_once "config/configuracion.php";

// Si no está logeado como empleado, lo llevamos a la página de login.
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["num_identif"])){
    header("location: login.php");
    exit;
}

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
    <link rel="stylesheet" href="css/busqueda.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        //función para guardar el centro de trabajo
        function guardar(){
            var str = document.getElementById("input_searchbox").value;

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                document.getElementById("result").innerHTML = this.responseText;
            }
            xmlhttp.open("GET","confirm-backend.php?centros="+str,true);
            xmlhttp.send();
        }
        $(document).ready(function(){
            $('.search-box input[type="text"]').on("keyup input", function(){
                // Get input value on change
                var inputVal = $(this).val();
                var resultDropdown = $(this).siblings(".result");
                if(inputVal.length){
                    $.get("buscar-backend.php", {term: inputVal}).done(function(data){
                        // Display the returned data in browser
                        resultDropdown.html(data);
                    });
                } else{
                    resultDropdown.empty();
                }
            });

            // Set search input value on click of result item
            $(document).on("click", ".result p", function(){
                $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
                $(this).parent(".result").empty();
            });

        })
        /**/
    </script>
</head>
<body>
<!-- Navigation bar -->
<header id="header" class="d-flex align-items-center">
    <div class="container d-flex justify-content-between align-items-center">

        <div class="logo">
            <a href="inicio.php"><img class="d-block mb-4 justify-content-center mt-auto mb-auto" src="css/SaludMadrid.svg" width="70" alt=""></a>
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
                <!-- Slide 1 -->
                <div class="carousel-item active" style="background: url(img/slide/slide-1.jpg)">
                    <div class="carousel-container">
                        <div class="carousel-content">
                            <h2 class="animate__animated animate__fadeInDown">Bienvenido al portal de <br> gestión de <span>vacunación COVID-19</span></h2>
                            <p class="animate__animated animate__fadeInUp">Este portal está orientado a la gestión de las citas de vacunación contra el coronavirus. El uso de este portal es exclusivo para sanitarios. Podrán ver información de los suministros de las vacunasa, las citas de cada centro y el registro de los pacientes con el número de dosis que se les ha aplicado.</p>
                                <div class="search-box animate__animated animate__fadeInUp">
                                    <label for="input_searchbox">Introduzca su centro de trabajo: </label>
                                    <input class="form-control" name="centro_trab" id="input_searchbox"
                                                 type="text" autocomplete="off" placeholder="Establecer centro..."/>
                                    <div class="result"><?php
                                        if (isset($_COOKIE["centro_trab"]))
                                            echo "Su centro de trabajo es " . $_COOKIE["centro_trab"];
                                        else if (isset($_COOKIE["localidad_trab"]))
                                            echo "Su localidad de trabajo es " . $_COOKIE["localidad_trab"];
                                        ?></div>
                                </div>
                                <input type="submit" class="btn btn-primary btn-get-started" value="Confirmar" onclick="guardar()">
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
                <div class="col-lg-4">
                    <div class="icon-box">
                        <i class="bi bi-binoculars"></i>
                        <h3><a href="listado_vacunas.php">Vacunas</a></h3>
                        <p>En el siguiente listado se muestra la información de cada vacuna según su fabricante.</p>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="icon-box">
                        <i class="bi bi-bar-chart"></i>
                        <h3><a href="listado_citas.php">Citas</a></h3>
                        <p>En este registro se muestran las personas citadas, con la información sobre el centro y fecha </p>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="icon-box">
                        <i class="bi bi-card-checklist"></i>
                        <h3><a href="listado_vacunados.php">Vacunados</a></h3>
                        <p>Aquí se muestra un resumen de los pacientes que ya han sido vacunados, con la
                            información sobre la vacuna administrada</p>
                    </div>
                </div>
            </div>

        </div>
    </section><!-- End Featured Section -->

</body>

</html>