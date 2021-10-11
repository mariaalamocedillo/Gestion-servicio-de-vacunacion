<?php
session_start();
echo "Parece ser que se produjo un error <br>";
if(isset($_SESSION["DNI"])){
    echo "<a href='inicio_pctes.php'> Vuelva al inicio de pacientes </a>";
} elseif (isset($_SESSION["num_identif"])){
    echo "<a href='inicio.php'> Vuelva al inicio de empleados </a>";
} else{
    header("location: index.php");
    exit;
}
