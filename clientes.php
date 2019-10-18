<html>
<?php
    $titulo_pag = "Clientes";
    include "modules/head.php";
?>

<head>
    <script src="js/buscarclnt.js"></script>
    <?php
      include "modules/header.php";
    ?>
</head>


<body>

    <nav class="navbar navbar-expand-sm bg-danger navbar-dark" style="margin-top: 200px;">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="./#">Movimientos</a>
            </li>
                
            <li class="nav-item">
                <a class="nav-link" href="./contenedores.php">Contenedores Con Número</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./contenedoressn.php">Contenedores Sin Número</a>
            </li> 

            <li class="nav-item active">
                <a class="nav-link" href="./clientes.php">Clientes y obras</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./imprimir.php">Imprimir</a>
            </li>

        </ul>
    </nav>
   


<!--PARA QUERY-->
<div class= "container_cln">

<!--<div class= "resultados">-->

    <div class= "panel panel-primary">
        <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Buscar Cliente</b></div>
    </div>
    
    <div class= "panel-body" style="margin-top:5px; margin-bottom: 4px"> 
        <input list='muestra_clientes' oninput='buscarCliente(this.value);' onselect="location = this.value;" id="buscamoscliente" type="text" name ="busquedacliente" placeholder = "Buscar Cliente" style="width:75%; margin-left: 10%">
        <datalist id="muestra_clientes">
        </datalist>
   
    </div>


<?php
    
    include "modules/conexion.php";

    class panel_Clientes {
        private $conexion;

        function _construct($conexion) {
            $this->conexion = $conexion; 
        }

        function buscarObraCliente($conexion,$nomEmp){
            
            $res = mysqli_query($conexion,"SELECT * FROM obracivil WHERE nomEmp = '".$nomEmp."' ");
            
            echo "<div class='Cuadro_General'>";
             echo "<div class='panel panel-primary' style='text-align:center;'>";
                echo "<div class='panel-heading' style='margin-top:10px;'>";
                echo "<h3>Obras</h3>" ;
                echo "</div>";

                echo "<div class='panel-body'>";
                if ($res->num_rows > 0) {
                    echo "<ul class='list-group' style='margin:1%'>";
                    while($a_res =  mysqli_fetch_assoc($res)) {
                        echo " <li class='list-group-item d-flex justify-content-between align-items-center' style='text-align:center;'>";
                        echo $a_res['localizacion'];
                        
                        //echo "<a href= 'modules/CambiarVisible.php?id=".$a_res['nomEmp']."&obra=".$a_res['localizacion']."'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                        
                        $res2 = mysqli_query($conexion,"SELECT COUNT(NumAlbaran) FROM albaran WHERE tipo = 'Entrega' 
                        and nomEmp = '".$nomEmp."' and localizacion='".$a_res['localizacion']."'
                        and NumAlbaran NOT IN (
                            SELECT NEntrega FROM enlace 
                        )" );

                        if( $res2!==false && $res2->num_rows>0
                            && $a_res2=mysqli_fetch_assoc($res2) ){ 
                                if($a_res2['COUNT(NumAlbaran)'] > 0){
                                    echo"<span class='badge badge-primary badge-pill'>";
                                    echo $a_res2['COUNT(NumAlbaran)'];
                                    echo "</span>";
                                    echo "<a href= 'modules/muestra_Obra_especifica.php?empresa=".$nomEmp."&localizacion=".$a_res['localizacion']." '> <i style='width:5px;'class='fas fa-eye'></i></a>";
                                }
                        }

                        echo"</li>";
                    }
                    echo "</ul>";
                }
                else {
                    echo "<div class='alert alert-danger'>
                        <strong> No se han encontrado obras.</strong>
                    </div>";
                }
                echo "</div>";
                echo "</div>";
            echo "</div>";
            $_GET = array();//para que se quede vacío
            
        }

        function addCliente($conexion,$nomEmp,$tlf){
            if($nomEmp != null){
                
                $res = mysqli_query($conexion,"INSERT INTO cliente (nomEmp,telefono) VALUES ('$nomEmp','$tlf') ");
                echo "<hr>";
                if($res) {
                    echo "<div class='alert alert-success'>
                            <strong> Se ha insertado correctamente. </strong>
                        </div>";
                }
                else {
                    echo "<div class='alert alert-danger'>
                            <strong> No se ha insertado .</strong>
                        </div>";
                }        
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong> Cadena vacía.</strong>
                </div>";
            }
            unset($_POST['anadircliente']);
        }

        function addObraCivil($conexion,$nomEmp,$obra){
            if($obra != null){
                $res = mysqli_query($conexion,"INSERT INTO obracivil (localizacion,nomEmp) VALUES ('$obra','$nomEmp') ");
                echo "<hr>";
                if ($res) {
                    echo "<div class='alert alert-success'>
                            <strong> Se ha insertado correctamente. </strong>
                        </div>";
                        
                }
                else {
                    echo "<div class='alert alert-danger'>
                            <strong> No se ha insertado .</strong>
                        </div>";
                }
            }
            else{
                echo "<div class='alert alert-danger'>
                <strong> campo vacío .</strong>
                </div>";
            }
            
           unset($_POST['anadircliente']);
           unset($_POST['anadirObra']);
        }

        function modObra($conexion,$nomEmp,$obra,$nueva){
            if($nueva != null && $nomemp != null && $obra !=null){
                $res = mysqli_query($conexion,"SELECT * FROM obracivil WHERE nomEmp ='".$nomEmp."' and localizacion = '".$obra."'");
                if($res->num_rows > 0){
                    $res = mysqli_query($conexion,"UPDATE contenedor SET localizacion = '".$nueva."' WHERE localizacion = '".$obra."' and nomEmp ='".$nomEmp."' ");
                    if($res){
                        echo "<div class='alert alert-success'>
                            <strong> Se ha insertado correctamente. </strong>
                        </div>";
                    }
                    else{
                        echo "<div class='alert alert-danger'>
                            <strong> No se ha podido cambiar.</strong>
                        </div>";
                    }
                }
                else{
                    echo "<div class='alert alert-danger'>
                        <strong> No se ha encontrado.</strong>
                    </div>";
                }
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong> Algún campo está vacío.</strong>
                </div>";
            }
        }
        
        function modCliente($conexion,$clnt,$nueva){
            if($clnt != null && $nueva != null){
                $res = mysqli_query($conexion,"SELECT * FROM cliente WHERE nomEmp = '".$clnt."' ");
                if($res->num_rows > 0){
                    //modifiamos las obras ascociadas
                    $res2 = mysqli_query($conexion,"UPDATE obracivil SET nomEmp = '".$nueva."' WHERE nomEmp = '".$clnt."' ");
                    if($res2){
                        $res3 = mysqli_query($conexion,"UPDATE cliente SET nomEmp = '".$nueva."' WHERE nomEmp = '".$clnt."' ");
                        if($res3){
                            echo "<div class='alert alert-success'>
                                <strong> Se ha cambiado correctamente. </strong>
                            </div>";
                        }
                        else{
                            echo "<div class='alert alert-danger'>
                                <strong> No se ha podido cambiar.</strong>
                            </div>";
                            $res2 = mysqli_query($conexion,"UPDATE obracivil SET nomEmp = '".$clnt."' WHERE nomEmp = '".$nueva."' ");
                            if(!$res2){
                                echo "<div class='alert alert-danger'>
                                    <strong>Obras no corresponden a su localización.</strong>
                                </div>";
                            }
                        }
                    }
                    else{
                        echo "<div class='alert alert-danger'>
                            <strong> No se ha podido cambiar.</strong>
                        </div>";
                    }
                }
                else{
                    echo "<div class='alert alert-danger'>
                        <strong>Cliente no encontrado.</strong>
                    </div>";
                }
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong>Algun campo está vacío.</strong>
                </div>";
            }
        }

        function numeroContenedores($conexion,$cliente){
            /* Vemos los contenedores que hay:
                Contamos todas las entregas de ese cliente
                y le quitamos todos los enlaces
            */

            $res = mysqli_query($conexion,"SELECT COUNT(NumAlbaran) FROM albaran WHERE tipo = 'Entrega' 
                and nomEmp = '".$cliente."'
                and NumAlbaran NOT IN (
                    SELECT NEntrega FROM enlace 
                ) ");

            if($res !== false && $res->num_rows > 0){
                if($a_res = mysqli_fetch_assoc($res)){
                    echo "<div class='NumeroContTotal'>";
                        echo "<h3>Número de contenedores: ";
                            echo $a_res['COUNT(NumAlbaran)'];
                        echo " </h3>";
                    echo "</div>";         
                }
       
            }

        }

        function modTelefono($conexion,$clnt,$tlf){
            if($clnt != null && $tlf != null){
                $res = mysqli_query($conexion,"SELECT * FROM cliente WHERE nomEmp = '".$clnt."' ");
                
                if($res!==false && $res->num_rows > 0){
                    //modifiamos las obras ascociadas
                    $res2 = mysqli_query($conexion,"UPDATE cliente SET telefono = '".$tlf."' WHERE nomEmp = '".$clnt."' ");
                    if($res2 !== false){
                        echo "<div class='alert alert-success'>
                            <strong> Se ha cambiado correctamente. </strong>
                        </div>";
                    }
                    else{
                        echo "<div class='alert alert-danger'>
                            <strong> No se ha podido cambiar.</strong>
                        </div>";
                    }
                }
            }
        }
    
    }
    
    $p_cln = new panel_Clientes($conexion);
    include "modules/conf_clientes.php";
    
    if(isset($_GET['id'])){
        $nomEmp = $_GET['id'];
        $p_cln->numeroContenedores($conexion,$nomEmp);
        $p_cln->buscarObraCliente($conexion,$nomEmp);
    
    }
?>
</div>
</body>
</html>