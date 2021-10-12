<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
} else{
    //si no está iniciada con un número de empleado, se cierra su sesión y redirige
    if(!isset($_SESSION["num_identif"])){
        session_unset();
        session_destroy();
        header("location: login.php");
        exit;
    }
}
$centro_trab = "";
//TODO que no recargue la página- solo cree una cookie + aplicarla
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty(trim($_POST["centro_trab"]))) {
        $centro_trab = trim($_POST["centro_trab"]);
        setcookie("centro_trab", $centro_trab, time() + (30 * 24 * 3600), "/");
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


    <!-- Favicons -->
    <link href="img/favicon.png" rel="icon">
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

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
        $(document).ready(function(){
            $('.search-box input[type="text"]').on("keyup input", function(){
                /* Get input value on change */
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
        });
    </script>
</head>
<body>
<!-- Navigation bar -->
<header id="header" class="d-flex align-items-center">
    <div class="container d-flex justify-content-between align-items-center">

        <div class="logo">
            <a href="inicio.php"><img class="d-block mb-4 justify-content-center mt-auto mb-auto" src="css/SaludMadrid.svg" width="70"></a>
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
                            <p class="animate__animated animate__fadeInUp">Introduzca su centro de trabajo: </p>
                            <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post" class=" animate__animated animate__fadeInUp">
                                <div class="search-box">
                                    <input class="form-control <?php echo (!empty($centro_trab)) ? 'is-invalid' : ''; ?>" name="centro_trab"
                                           value="<?php echo $centro_trab; ?>" type="text" autocomplete="off" placeholder="Buscar centro..." />
                                    <div class="result"></div>
                                </div>
                                <input type="submit" class="btn btn-primary btn-get-started" value="Confirmar">
                            </form>
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