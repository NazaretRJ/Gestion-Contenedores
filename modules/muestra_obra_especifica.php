<?php
    $empresa = $_GET['empresa'];
    $localizacion = $_GET['localizacion'];
    $titulo_pag = $localizacion;
?>

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <title><?php echo $titulo_pag; ?></title>
    <meta charset="utf-8">
</head>


<html>
<nav class="navbar navbar-expand-sm bg-danger navbar-dark">
        <ul class="navbar-nav">
            
            <li class="nav-item active">
                <a class="nav-link" href="../index.php">Movimientos</a>
            </li>
                            
            <li class="nav-item">
                <a class="nav-link" href="../contenedores.php">Contenedores Con Número</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../contenedoressn.php">Contenedores Sin Número</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../clientes.php">Clientes y obras</a>
            </li>
            
        </ul>
    </nav>
</html>


<?php

    /*
        Utilidad: ver los contenedores que hay en esta localización y de qué tipo son

        TODO: preguntar si quieren saber qué residuo es el que llevan
    */

    include("conexion.php");


    //coger los contenedores ordenados por tipo que no tengan enlace
    /*
    $res = mysqli_query($conexion,"SELECT * FROM albaran NATURAL JOIN solicita 
        WHERE albaran.tipo = 'Entrega'
        and albaran.nomEmp='".$empresa."' and albaran.localizacion='".$localizacion."' 
        and albaran.NumAlbaran NOT IN
        (
            SELECT NEntrega FROM enlace
        ) ORDER BY solicita.tipoCont
        ");


        SELECT Capacidad, COUNT(*) FROM solicita 
        INNER JOIN contenedorsn ON contenedorsn.Id = solicita.IdCont
        WHERE NumAlbaran NOT IN(
            SELECT NEntrega FROM enlace
        ) 
        GROUP BY (Capacidad)

    */

    $res = mysqli_query($conexion,"SELECT Capacidad,contenedorsn.Empresa,COUNT(*) as total FROM solicita 
        INNER JOIN contenedorsn ON contenedorsn.Id = solicita.IdCont INNER JOIN albaran ON solicita.NumAlbaran = albaran.NumAlbaran
        WHERE albaran.tipo='Entrega' 
        and solicita.NumAlbaran NOT IN(
            SELECT NEntrega FROM enlace
        ) 
        and albaran.nomEmp = '".$empresa."' and albaran.localizacion = '".$localizacion."'
        GROUP BY (Capacidad)");

    /*
    SELECT * FROM solicita 
        INNER JOIN contenedorsn ON contenedorsn.Id = solicita.IdCont INNER JOIN albaran ON 				solicita.NumAlbaran = albaran.NumAlbaran
        WHERE albaran.tipo='Entrega' and solicita.NumAlbaran NOT IN(
            SELECT NEntrega FROM enlace
        ) 
        GROUP BY (Capacidad)
    */

    if( $res !== false && $res->num_rows > 0){
        echo "<div class='Cuadro_Mostrar_Especificos'>";
        echo "<h1>Sin Número</h1>";
        RellenarCuadroSN();
        echo "<tbody>";
        while($a_res = mysqli_fetch_assoc($res) ){
            echo "<tr>";
                //Rellenar
                    echo "<td>";
                        echo $a_res['Capacidad'];
                    echo "</td>";

                    echo "<td>";
                        echo $a_res['Empresa'];
                    echo "</td>";

                    echo "<td>";
                        echo $a_res['total'];
                    echo "</td>";
                echo "</tr>";
            
        }
        echo "</tbody>";
        echo "</table>";
        echo"</div>";
    }
    
    function RellenarCuadroSN(){

        echo "<table class='table table-sm'>";

            echo "<thead class='p-3 mb-2 bg-primary text-white'>";
                echo "<tr>";
                    echo "<th scope='col'>Capacidad</th>";
                    echo "<th scope='col'>Empresa</th>";
                    echo "<th scope='col'>Tienen</th>";
                echo "</tr>";
        echo "</thead>";
    }

    //ahora con número
    $res2 = mysqli_query($conexion,"SELECT numCont,contenedor.Empresa,solicita.NumAlbaran FROM solicita 
        INNER JOIN contenedor ON contenedor.Id = solicita.IdCont INNER JOIN albaran ON solicita.NumAlbaran = albaran.NumAlbaran
        WHERE contenedor.estado = 'ocupado'
        and albaran.nomEmp = '".$empresa."' and albaran.localizacion = '".$localizacion."' ");

    if( $res2 !== false && $res2->num_rows > 0){
        echo "<div class='Cuadro_Mostrar_Especificos'>";
        echo "<h1>Numerado</h1>";
        RellenarCuadro();
        echo "<tbody>";
        while($a_res2 = mysqli_fetch_assoc($res2) ){
            echo "<tr>";
                //Rellenar
                    echo "<td>";
                        echo $a_res2['numCont'];
                    echo "</td>";

                    echo "<td>";
                        echo $a_res2['Empresa'];
                    echo "</td>";

                    echo "<td>";
                        echo $a_res2['NumAlbaran'];
                    echo "</td>";

                echo "</tr>";
            
        }
        echo "</tbody>";
        echo "</table>";
        echo"</div>";
    }
    
    function RellenarCuadro(){

        echo "<table class='table table-sm'>";

            echo "<thead class='p-3 mb-2 bg-primary text-white'>";
                echo "<tr>";
                    echo "<th scope='col'>Número Contenedor</th>";
                    echo "<th scope='col'>Empresa</th>";
                    echo "<th scope='col'>Número de Albarán</th>";
                echo "</tr>";
        echo "</thead>";
    }

?>