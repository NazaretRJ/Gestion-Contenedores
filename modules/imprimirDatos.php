<script> 
    function Imprimir(){

        window.print();
    }

</script>
<head>
    <title>Imprimir albaranes</title>
    <?php
        include "head.php";
        include "conexion.php";
    ?>
</head>

<?php 
class panel_Imp {

private $conexion;
private $MAX = 30;
private $MAX2 = 15;

function _construct($conexion) {
    $this->conexion = $conexion;
}

//abrir una página con los datos
//PRIMERO ENSEÑAR Y LUEGO BOTÓN PARA BORRAR
function MuestraEnlaces($conexion,$enlace,$NEntrega,$dias,$residuo,$EmpCont,$contenedor){

    $res = mysqli_query($conexion,"SELECT * FROM albaran
        WHERE albaran.NumAlbaran = '".$enlace."' ");

        if($res!== false && $res->num_rows > 0){
            while($a_res =  mysqli_fetch_assoc($res)){

                //Contenido tabla
                    
                echo "<tr>";
                    echo "<td>";
                        echo $a_res['NumAlbaran'];
                    echo "</td>";
                    echo "<td>";
                        echo $a_res['tipo'];
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

                    echo "<td>";
                        echo $contenedor;
                        echo " ";
                        echo $EmpCont;
                    echo "</td>";
                    
                    echo "<td>";
                    if( is_null($residuo))
                        echo "-";
                    else
                        echo $residuo;
                    echo "</td>";
            
                    echo "<td>";
                    echo $dias;
                    echo "</td>";
    
                    echo "<td>";
                        echo $NEntrega;
                    echo "</td>";

            }
        }
        else{
            echo "<tr><td><div class='alert alert-danger'>
                <strong> Error en la Base de Datos </strong>
            </div></td></tr>";
        
        }
        echo "</tr>";

}

function MostrarMovimientos($conexion,$fecha,$fecha_fin){
    unset($_POST['ImpMov']);
    unset($_POST['fecha_ini']);
    unset($_POST['fecha_fin']);

    if(!is_null($fecha) && !is_null($fecha_fin)){
    
        $res = mysqli_query($conexion,"SELECT * FROM enlace INNER JOIN albaran ON NEntrega = NumAlbaran NATURAL JOIN solicita
            WHERE albaran.fecha BETWEEN '".$fecha."' and '".$fecha_fin."'
            ORDER BY albaran.fecha ASC 
        ");

        /* TODAS LAS ENTREGAS QUE CUMPLEN LA FECHA
            
            SELECT * FROM enlace INNER JOIN albaran ON NEntrega = NumAlbaran NATURAL JOIN solicita
            WHERE albaran.fecha BETWEEN '2019-01-01' and '2019-08-30'
                ORDER BY albaran.fecha ASC 
        */
        // tabla 
        

    echo "<div class='tabla_movimientos_enlace'>";
            echo "<button type='button'  id='BtImprimir' onclick='Imprimir()' class='btn btn-primary'>Imprimir</button>";
        echo "<table class='table table-sm table-hover'>";

        echo "<thead class='p-3 mb-2 bg-primary text-white'>";
        echo "<tr>";
        echo "<th scope='col'>Número de Albaran</th>";
        echo "<th scope='col'>Tipo</th>";
        echo "<th scope='col'>Fecha</th>";
        echo "<th scope='col'>Empresa</th>";
        echo "<th scope='col'>Localización</th>";
        echo "<th scope='col'>Contenedor</th>";
        echo "<th scope='col'>Residuo</th>";
        echo "<th scope='col'>Dias fuera</th>";
        echo "<th scope='col'>Enlazado</th>";
        echo "</tr>";
        
        echo "</thead>";

        echo "<tbody>";

    
        if($res!== false && $res->num_rows > 0){
            while($a_res =  mysqli_fetch_assoc($res)){
            
                $boolSN = strcasecmp ($a_res['tipoCont'],'SN');
                   
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
                    if($a_res2 =  mysqli_fetch_assoc($res2) ){
                        
                        if($boolSN == 0){
                            $contenedor = $a_res2['Capacidad'];
                            $EmpCont = $a_res2['Empresa'];
                        }
                        else{
                            $contenedor = $a_res2['numCont'];
                            $EmpCont = $a_res2['Empresa'];
                        }
                
                        //Contenido tabla
                    
                        echo "<tr>";
                            echo "<td>";
                                echo $a_res['NumAlbaran'];
                            echo "</td>";
                            echo "<td>";
                                echo $a_res['tipo'];
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

                            echo "<td>";
                                echo $contenedor;
                                echo " ";
                                echo $EmpCont;
                            echo "</td>";


                            
                            $res3 = false;
                            
                            $enlace_alb = $a_res['NEntrega'];
                            

                            echo "<td>";
                            if( is_null($a_res['residuo']))
                                echo "-";
                            else
                                echo $a_res['residuo'];
                            echo "</td>";
            
                            echo "<td>";
                            echo $a_res['dias'];
                            echo "</td>";
    
                            echo "<td>";
                            
                                $enlace_alb = $a_res['NRecogida'];
                                echo $a_res['NRecogida'];

    
                            echo "</td>";

                        echo "</tr>";

                        $this->MuestraEnlaces($conexion,$enlace_alb,$a_res['NEntrega'],$a_res['dias'], $a_res['residuo'],$EmpCont,$contenedor);
                    
                    }
                }

            }

        
        }
        else{
            echo "<tr><td><div class='alert alert-danger'>
                <strong> No hay lineas </strong>
            </div></td></tr>";
        }

        echo "</tbody>";
        echo "</table>";

        echo "</div>";

    }
    else{
        echo "<div class='alert alert-warning'>
            <strong> Fechas en blanco </strong>
            Introduzca fechas válidas
        </div>";
    }


}


}
$p_imp = new panel_Imp($conexion);

    if(isset($_POST['ImpMov']))
        $p_imp->MostrarMovimientos($conexion,$_POST['fecha_ini'],$_POST['fecha_fin']);


?>