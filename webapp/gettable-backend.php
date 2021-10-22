<?php
require_once "config/configuracion.php";
//cambiamos los guiones por espacios, y almacenamos el centro o localidad
$centro = str_replace("-", " ", $_GET['centros']);

if (!$mysqli) {
    die('Could not connect: ' . mysqli_error($mysqli));
}
//asignamos el comando sql que corresponda
if (!strcmp($centro,"all")){
    $sql = "SELECT * FROM citas";
} else{
    $sql = "SELECT * FROM citas WHERE centro_vacunacion = '".$centro."'";
}
if($result = $mysqli->query($sql)){
    if($result->num_rows > 0){
        echo '<table class="table table-bordered table-striped">';
        echo "<thead>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th>DNI</th>";
        echo "<th>Dosis a aplicar</th>";
        echo "<th>Centro de vacunación</th>";
        echo "<th>Fecha y hora</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while($row = $result->fetch_array()){
            echo "<tr>";
            echo "<td>" . $row['id_cita'] . "</td>";
            echo "<td>" . $row['DNI'] . "</td>";
            echo "<td>" . $row['num_dosis'] . "</td>";
            echo "<td>" . $row['centro_vacunacion'] . "</td>";
            echo "<td>" . $row['fecha'] . "</td>";
            echo "<td>";
            echo '<a href="delete_cita.php?id='. $row['id_cita'] .'" class="mr-3" title="Anular" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
            echo '<a href="confirmar_vacunado.php?id='. $row['id_cita'] .'" title="Completada" data-toggle="tooltip"><span class="fa fa-check-square"></span></a>';
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        // Free result set
        $result->free();
    } else{
        echo '<div class="alert alert-danger"><em>No se encontraron registros.</em></div>';
    }
} else{
    echo "Oops! Algo fue mal. Inténtelo más tarde.";
}

// Close connection
$mysqli->close();







