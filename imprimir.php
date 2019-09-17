<html>
<?php
    include "modules/conexion.php";
    $titulo_pag = "Gestion Contenedores";
    include "modules/head.php";
?>

<head>
    <?php
        include "modules/header.php";
    ?>
</head>



<body>

 <nav class="navbar navbar-expand-sm bg-danger navbar-dark" style="margin-top: 200px;">
    <ul class="navbar-nav">
        <li class="nav-item">
                <a class="nav-link" href="./index.php">Movimientos</a>
        </li>
                
        <li class="nav-item">
            <a class="nav-link" href="./contenedores.php">Contenedores Con Número</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="./contenedoressn.php">Contenedores Sin Número</a>
        </li> 

        <li class="nav-item">
            <a class="nav-link" href="./clientes.php">Clientes y obras</a>
        </li>

        <li class="nav-item active">
            <a class="nav-link" href="./imprimir.php">Imprimir</a>
        </li>

    </ul>
 </nav>

<div class= "container_mov">


<?php

class panel_Imp {

    private $conexion;
    private $MAX = 30;
    private $MAX2 = 15;

    function _construct($conexion) {
        $this->conexion = $conexion;
    }

    //Borra moviemientos y sus enlaces
    function BorrarMovimientos($conexion,$fecha,$fecha_fin){
        unset($_POST['DelMov']);
        unset($_POST['fecha_ini']);
        unset($_POST['fecha_fin']);

        if(!is_null($fecha) && !is_null($fecha_fin)){
            //tenemos los enlaces
            $res = mysqli_query($conexion,"SELECT NEntrega, NRecogida FROM enlace INNER JOIN albaran ON NEntrega = NumAlbaran NATURAL JOIN solicita
            WHERE albaran.fecha BETWEEN '".$fecha."' and '".$fecha_fin."'
            ORDER BY albaran.fecha ASC ");

            $conseguido = true;
            if($res !== false and $res->num_rows > 0){
                while($a_res =  mysqli_fetch_assoc($res)){
                    //Borramos los enlaces
                    $del_en = mysqli_query($conexion,"DELETE FROM enlace WHERE NEntrega = '".$a_res['NEntrega']."' ");

                    if($del_en === false){
                        $conseguido = false;
                    }
                    else{
                        //Borramos de solicita
                        $del_sol =  mysqli_query($conexion,"DELETE FROM solicita WHERE NumAlbaran = '".$a_res['NEntrega']."' ");
                        $del_sol2 =  mysqli_query($conexion,"DELETE FROM solicita WHERE NumAlbaran = '".$a_res['NRecogida']."' ");

                        if($del_sol === false || $del_sol2 === false){
                            $conseguido = false;
                        }
                        else{
                            //Borramos el albaran

                            $del_alb =  mysqli_query($conexion,"DELETE FROM albaran WHERE NumAlbaran = '".$a_res['NEntrega']."' ");
                            $del_alb2 =  mysqli_query($conexion,"DELETE FROM albaran WHERE NumAlbaran = '".$a_res['NRecogida']."' ");

                            if($del_alb === false || $del_alb2 === false){
                                $conseguido = false;
                            }
                        }
                    }

                }
            }

            if($conseguido == true){
                echo "<tr><td><div class='alert alert-success'>
                <strong> Borrados correctamente </strong>
                </div></td></tr>";
            }
            else{
                echo "<tr><td><div class='alert alert-warning'>
                <strong> Hubo un error borrando algún albarán </strong>
                </div></td></tr>";
            }
        
        }
        else{
            echo "<tr><td><div class='alert alert-warning'>
                <strong> Fechas en blanco </strong>
                Introduzca fechas válidas
            </div></td></tr>";
        }
    }
}
    $p_imp = new panel_Imp($conexion);
    
    include './modules/conf_imprimir.php';

    if(isset($_POST['BorrarAlb'])){
        //borrar
        $p_imp->BorrarMovimientos($conexion,$_POST['fecha_ini'],$_POST['fecha_fin']);
    }
    

?>



</div>
</body>
</html>
</body>