<head>
    <link href="https://fonts.googleapis.com/icon?family=Material" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <title> Movimiento específico</title>
    <meta charset="utf-8">
</head>


<html>
<body>
    <nav class="navbar navbar-expand-sm bg-danger navbar-dark">
        <ul class="navbar-nav">
            
            <li class="nav-item active">
                <a class="nav-link" href="../index.php">Movimientos</a>
            </li>
                            
            <li class="nav-item">
                <a class="nav-link" href="../contenedores.php">Contenedores Con Número</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="./contenedoressn.php">Contenedores Sin Número</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../clientes.php">Clientes y obras</a>
            </li>
            
        </ul>
    </nav>

    <?php

        include("conexion.php");
        $numero = $_GET['NAlb'];
        $tipo = $_GET['tipo'];
        $boolSN = $_GET['ContenedorSN'];        

        if(! isset($_GET['enlazado']) ){
            $enlace = null;
        } 
        else{
            $enlace = $_GET['enlazado'];
        }
        $tipo_entrega = strcasecmp($tipo,"ENTREGA");

        echo "<div class='Cuadro_General'>";
            if($tipo_entrega == 0 ){
                //Entrega
                RellenarCuadro($conexion,"Entrega",$numero,$enlace,$tipo_entrega,$boolSN);
                if(!is_null($enlace)){
                    //Buscamos la recogida así que tipo_entrega no puede ser 0
                    RellenarCuadro($conexion,"Recogida",$enlace,$numero,1,$boolSN);
                }
            }
            else{
                if(!is_null($enlace)){
                    //tiene enlace y es recogida
                    RellenarCuadro($conexion,"Entrega",$enlace,$numero,0,$boolSN);
                    RellenarCuadro($conexion,"Recogida",$numero,$enlace,$tipo_entrega,$boolSN);
                }
                else{
                    // No tiene enlace pero es recogida
                    RellenarCuadro($conexion,"Recogida",$numero,$enlace,$tipo_entrega,$boolSN);
                }
            }

            echo "<form action='../index.php' method='post'>";
                echo "<input type='hidden' id='numAlb' name='numAlb' value=$numero >";
                echo "<button type='submit' name='BorrarMov' style='margin-bottom:5px;' class='btn btn-danger'>";
                if(!is_null($enlace))
                    echo "Borrar Ambos";
                else
                    echo "Borrar";
                echo "</button>";
            echo "</form>";

        echo "</div>";

        function MostrarValores($conexion,$albaran,$boolSN,$AlbEnlace,$tipo_entrega){
            $res = mysqli_query($conexion, "SELECT * FROM albaran 
                INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran 
                and albaran.NumAlbaran = '".$albaran."' ");
                
            if($res !== false && $res->num_rows > 0){
                while($a_res =  mysqli_fetch_assoc($res)){
                    echo "<tr>";
                    echo "<td>";
                        echo $a_res['NumAlbaran'];
                    echo "</td>";
                
                    echo "<td>";
                        $daux = DateTime::createFromFormat('Y-m-d',$a_res['fecha']);
                        echo $daux->format('d-m-Y');
                    echo "</td>";
                           
                    echo "<td>";
                        echo $a_res['nomEmp'];
                    echo "</td>";

                    echo "<td>";
                        echo $a_res['localizacion'];
                    echo "</td>";
                
                    $res2 = false;
                    
                    if($boolSN == 0){
                        //Sin Número
                        //Lo hacemos con los específicos para luego sacar el contenedor
                        $res2 = mysqli_query($conexion, "SELECT * FROM solicita INNER JOIN contenedorsn ON solicita.IdCont = contenedorsn.Id and solicita.EmpresaCont = contenedorsn.Empresa and solicita.NumAlbaran = '".$a_res['NumAlbaran']."' ");
                    }
                    else{
                        $res2 = mysqli_query($conexion,"SELECT * FROM solicita INNER JOIN contenedor ON solicita.IdCont = contenedor.Id and solicita.EmpresaCont = contenedor.Empresa and solicita.NumAlbaran = '".$a_res['NumAlbaran']."' ");
                    }
                    $fecha_pagado = null;

                    if($res2 !== false){
                        while($a_res2 =  mysqli_fetch_assoc($res2)){
                            $fecha_pagado = $a_res2['fecha_pagado'];

                            if($boolSN == 0){
                                echo "<td>";
                                echo $a_res2['Capacidad'];
                                echo " </td>";

                                echo "<td>";
                                echo $a_res2['Empresa'];
                                echo "</td>";
                            }
                            else{
                                echo "<td>";
                                echo $a_res2['numCont'];
                                echo "</td>";

                                echo "<td>";
                                echo $a_res2['Empresa'];
                                echo "</td>";

                                echo "<td>";
                                echo $a_res2['tamaño'];
                                echo "</td>";
                            }
                        }
                    }
                    else{
                        
                        echo "<tr><td><div class='alert alert-danger'>
                            <strong> Error en la Base de Datos </strong>
                        </div></td></tr>";
                    }

                    // FASE ENLACE

                    if(!is_null($AlbEnlace)){
                        $res3 = false;
                        
                        if($tipo_entrega == 0){
                            $res3 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NEntrega = '".$a_res['NumAlbaran']."' ");
                        }
                        else{
                            $res3 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NRecogida = '".$a_res['NumAlbaran']."' ");
                        }
                        
                        if($res3 !== false){
                            if($res3->num_rows > 0){
                                //algo enlazado
                                while($a_res3 =  mysqli_fetch_assoc($res3)){
                                    echo "<td>";
                                    if(! is_null($a_res3['residuo']) )
                                        echo $a_res3['residuo'];
                                    else
                                        echo "-";

                                    echo "</td>";
                                
                                    echo "<td>";
                                    echo $a_res3['dias'];
                                    echo "</td>";
                        
                                }
                            }
                        
                        }
                        else{
                            echo "de res3";
                            echo "<tr><td><div class='alert alert-danger'>
                                <strong> Error en la Base de Datos </strong>
                            </div></td></tr>";
                        }
                    }
                    else{
                        echo "<td>";
                            echo "-";
                        echo "</td>";
                    
                        echo "<td>";
                            echo "-";
                        echo "</td>";
                    }




                    echo "<td>";
                    echo "<a href= 'modificarMovNuevo.php?num=".$albaran."&tipo=".$tipo_entrega." '><i class='fas fa-pencil-alt'></i></a>";
                    //text-decoration:none quitar color azul de hipervinculo
                    //echo "<a style= 'margin-left: 30px;text-decoration:none;color:red;' href='./modules/modal.php?numBorrar=".$albaran." '><i style='width:5px;'class='fas fa-eraser'></i></a>";
                    

                
                    echo "</td>";

                    echo "</tr>";
                }//fin while
            }
            else{
            echo "<tr><td><div class='alert alert-danger'>
                <strong> Error</strong>
            </div></td></tr>";
            }
        }

        function RellenarCuadro($conexion,$titulo,$numero,$enlace,$tipo_entrega,$boolSN){
            echo "<div class='Cuadro_Mostrar_Especificos'>";
                echo "<h1>";
                echo $titulo;
                echo "</h1>";
                echo "<table class='table table-sm'>";

                    echo "<thead class='p-3 mb-2 bg-primary text-white'>";
                        echo "<tr>";
                            echo "<th scope='col'>Número de Albaran</th>";
                            echo "<th scope='col'>Fecha</th>";
                            echo "<th scope='col'>Empresa</th>";
                            echo "<th scope='col'>Localización</th>";
                        
                            if($boolSN == 0){
                                echo "<th scope='col'>Capacidad</th>";
                                echo "<th scope='col'>Empresa Contenedor</th>";
                            }
                            else{
                                echo "<th scope='col'>Número de contenedor</th>";
                                echo "<th scope='col'>Empresa Contenedor</th>";
                                echo "<th scope='col'> Tamaño </th>";
                            }
                    
                            echo "<th scope='col'>Residuo</th>";
                            echo "<th scope='col'>Dias</th>";

                            echo "<th scope='col'>Modificar</th>";
                        echo "</tr>";
                    echo "</thead>";
        
                    echo "<tbody>";
                    
                    MostrarValores($conexion,$numero,$boolSN,$enlace,$tipo_entrega);
                    
                
                    echo "</tbody>";
                echo "</table>";
            echo"</div>";
        }

    ?>
    
    


</body>
</html>