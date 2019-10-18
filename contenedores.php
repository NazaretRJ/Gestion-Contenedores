<?php
    include "modules/conexion.php";
    $titulo_pag = "Gestion Contenedores";
    include "modules/head.php";


?>
<html>
<head>
    <script src="js/ModCont.js"></script>
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
                
            <li class="nav-item active">
                <a class="nav-link" href="./contenedores.php">Contenedores Con Número</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./contenedoressn.php">Contenedores Sin Número</a>
            </li> 

            <li class="nav-item">
                <a class="nav-link" href="./clientes.php">Clientes y obras</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./imprimir.php">Imprimir</a>
            </li>


        </ul>
    </nav>

    
   

<div class= "container_cont">
<?php
class panel_Contenedores{
    private $conexion;
    private $pagi = 0;
    private $contar_pagi = 0;
    private $pagi_navegacion;
    private $numer_reg = 15;

    function _construct($conexion) {
        $this->conexion = $conexion;
        $this->pagi = 0;
        $this->contar_pagi = 0;
        $this->pagi_navegacion = "<a href='contenedores.php?pagi=0>Pagina</a>";
        $this->numer_reg = 15;
    }

    function calcularpag($conexion){
        if(isset($_GET['pagi']))
            $this->pagi = $_GET['pagi'];
        else
            $this->pagi = 0;
        $this->contar_pagi = (strlen($this->pagi)); 
        // Contamos los registros totales
        $result0 = mysqli_query($conexion,"SELECT * FROM albaran"); 
        $numero_registros0 = $result0->num_rows; 
        // ----------------------------- Pagina anterior
        $prim_reg_ant = abs($this->numer_reg - $this->pagi);

        //inicializacion
        $pag_anterior = "";
        $pag_siguiente = "";
        $separador = "";

        if ($this->pagi <> 0){ 
            $pag_anterior = "<a href='index.php?pagi=$prim_reg_ant'>Pagina anterior</a>";
        }
        // ----------------------------- Pagina siguiente
        $prim_reg_sig = $this->numer_reg + $this->pagi;

        if ($this->pagi < ($numero_registros0 - ($this->numer_reg - 1))){ 
            $pag_siguiente = "<a href='index.php?pagi=$prim_reg_sig'>Pagina siguiente</a>";
        }
        // ----------------------------- Separador
        if ($this->pagi <> 0 and $this->pagi < $numero_registros0 - ($this->numer_reg - 1)) { 
            $separador = "|";
        }
        // Creamos la barra de navegacion

        $this->pagi_navegacion = "$pag_anterior $separador $pag_siguiente";

    } 


    function addContenedor($conexion,$num,$emp,$tam){
        $res = false;
        if($num != null and $tam != null){
            if($emp != null)
                $res = mysqli_query($conexion,"INSERT INTO contenedorgeneral(Empresa) VALUES ('$emp') ");
            else
                $res = mysqli_query($conexion,"INSERT INTO contenedorgeneral(Empresa) VALUES (DEFAULT) ");
            
            if($res){
                $comp = mysqli_query($conexion,"SELECT MAX(Id) as Id FROM contenedorgeneral");
                if($comp !== false){
                    if($a_comp =  mysqli_fetch_assoc($comp)){
                        
                        $ID = $a_comp['Id'];
                        
                        $res2 = false;
                        if($emp != null)
                            $res2 = mysqli_query($conexion,"INSERT INTO contenedor (numCont,Empresa,Id,tamaño) VALUES ('$num','$emp','$ID','$tam') ");
                        else
                            $res2 = mysqli_query($conexion,"INSERT INTO contenedor (numCont,Id,tamaño) VALUES ('$num','$ID','$tam') ");
                    
                        if($res2 !== false){
                            echo "<div class='alert alert-success'>
                                <strong> Se ha insertado correctamente.</strong>
                            </div>";
                        }
                        else{
                            echo "<div class='alert alert-danger'>
                                <strong> No se ha podido insertar.</strong>
                            </div>";
                        }
                    }

                }
                else{
                    echo "<div class='alert alert-danger'>
                            <strong> No se ha podido insertar.</strong>
                        </div>";
                }
    
            }
            else{
                echo "<div class='alert alert-danger'>
                        <strong> No se ha insertar .</strong>
                    </div>";
            }
                    
        }
        else{
            echo "<div class='alert alert-danger'>
                    <strong> Algún campo está vacío.</strong> Obligatorios: Número y tamaño.
                </div>";
        }
        
    }

    function MostrarContenedores($conexion){
        $this->calcularpag($conexion);
        $l_sup = $this->numer_reg;
        $l_inf = 0; // Si NO recibimos un valor por la variable $page
        if ($this->contar_pagi > 0) { 
            // Si recibimos un valor por la variable $page ejecutamos esta consulta
            $l_inf = $this->pagi;  
        } 
        $res = mysqli_query($conexion, "SELECT * FROM contenedor ORDER BY numCont DESC LIMIT $l_inf,$l_sup");
        //p-3 mb-2 bg-primary text-white
        echo "<table class='table table-sm table-hover' style='text-align: center;'>";

        echo "<thead class='p-3 mb-2 bg-primary text-white'>";
            echo "<tr>";
                echo "<th scope='col'>Número de contenedor</th>";
                echo "<th scope='col'>Empresa propietaria</th>";
                echo "<th scope='col'>Tamaño</th>";
                echo "<th scope='col'>Estado</th>";
                echo "<th scope='col'>Modificar</th>";
            echo "</tr>";
        echo "</thead>";

        echo "<tbody style='text-align: center;'>";
        if($res->num_rows > 0) {
            while($a_res =  mysqli_fetch_assoc($res)){
                if($a_res['estado'] != 'borrado'){
                    echo "<tr>";
                        echo "<td>";
                            echo $a_res['numCont'];
                        echo "</td>";
                        echo "<td>";
                            echo $a_res['Empresa'];
                        echo "</td>";
                        echo "<td>";
                            echo $a_res['tamaño'];
                        echo "</td>";
                        echo "<td>";
                            echo $a_res['estado'];
                        echo "</td>";
                        echo "<td>";
                            echo "<a href= 'modules/modificarCont.php?numCont=".$a_res['numCont']."&emp=".$a_res['Empresa']." '><i style='width:5px;'class='fas fa-pencil-alt'></i></a>";
                        echo "</td>";
                    echo "</tr>";
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
    }
    
    function DeshacerUpSolicita($conexion,$ID,$emp,$empAnt){
        //prerrequistio: ninguno es nulo
        $fallo = true;
        $comp = mysqli_query($conexion,"SELECT * FROM solicita WHERE IdCont = '".$ID."' and EmpresaCont = '".$emp."' ");
        if($comp->num_rows > 0){ //alguien lo usa
            $res = mysqli_query($conexion,"UPDATE solicita SET EmpresaCont ='".$empAnt."' WHERE IdCont = '".$ID."' and EmpresaCont = '".$emp."' "); 
            if($res !== false)
                $fallo = false;
        }
        
        return $fallo;
    }
    
    function UpdateSolicita($conexion,$ID,$emp,$empAnt){
        //prerrequistio: ninguno es nulo
        $fallo = true;

        $comp = mysqli_query($conexion,"SELECT * FROM solicita WHERE IdCont = '".$ID."' and EmpresaCont = '".$empAnt."' ");
        if($comp !== false){
            
            if($comp->num_rows > 0){ // lo usan
                $res = mysqli_query($conexion,"UPDATE solicita SET EmpresaCont ='".$emp."' WHERE IdCont = '".$ID."' and EmpresaCont = '".$empAnt."' "); 
                if($res !== false){
                    $fallo = false;
                }
            }
            else{ //no lo usan
                $fallo = false;
            }
        }
        return $fallo;
    }

    function modificarNum($conexion,$Num,$emp,$Nuevo){
        unset($_POST['ModNumCont']);
        unset($_POST['numCont']);
        unset($_POST['emp']);
        unset($_POST['Numero']);

        if($Nuevo != null && $emp != null && $Num !=null){
            $emp = $this->buscarEmp($conexion,$emp,$Num);
            $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE numCont ='".$Num."' and Empresa = '".$emp."'");
            if($res !== false && $res->num_rows > 0){
                //primero coger el id
                if($res !== false){
                    $res = mysqli_query($conexion,"UPDATE contenedor SET numCont ='".$Nuevo."' WHERE numCont ='".$Num."' and Empresa = '".$emp."' ");
                    if($res !== false){
                        echo "<div class='alert alert-success'>
                            <strong> Se ha insertado correctamente. </strong>
                        </div>";
                    }
                    else{
                        echo "<div class='alert alert-danger'>
                            <strong> No se ha podido cambiar .</strong>
                        </div>";
                    }
                }
                
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong> No se ha encontrado .</strong>
                </div>";
            }
        }
        else{
            echo "<div class='alert alert-danger'>
                <strong> Algún campo vacío.</strong>
            </div>";
        }
    
    }

    function modificarEmp($conexion,$Num,$emp,$Nuevo){
        unset($_POST['ModNumCont']);
        unset($_POST['numCont']);
        unset($_POST['emp']);
        unset($_POST['empNueva']);

        if($Nuevo != null && $emp != null && $Num !=null){
            $emp = $this->buscarEmp($conexion,$emp,$Num);
            $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE numCont ='".$Num."' and Empresa = '".$emp."'");
            if($res->num_rows > 0 && $a_res = mysqli_fetch_assoc($res)){
                
                $fallo = $this->UpdateSolicita($conexion,$a_res['Id'],$Nuevo,$emp);
                if($fallo == false){
                    
                    $res2 = mysqli_query($conexion,"UPDATE contenedor SET Empresa ='".$Nuevo."' WHERE numCont ='".$Num."' and Empresa = '".$emp."' ");
                    if($res2){
                        echo "<div class='alert alert-success'>
                            <strong> Se ha insertado correctamente. </strong>
                        </div>";
                    }
                    else{
                        
                        $fallo2 = $this-> DeshacerUpSolicita($conexion,$a_res['Id'],$Nuevo,$emp);
                        if($fallo2){
                            echo "<div class='alert alert-warning'>
                                <strong> Puede haber inconsistencias en las solicitudes de contenedores </strong>
                            </div>";
                        }
                        echo "<div class='alert alert-danger'>
                            <strong> No se ha podido cambiar .</strong>
                        </div>";
                    }
                }
                
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong> No se ha encontrado .</strong>
                </div>";
            }
        }
        else{
            echo "<div class='alert alert-danger'>
                <strong> Algún campo vacío.</strong>
            </div>";
        }

    }

    function modificarTam($conexion,$Num,$emp,$Nuevo){
        unset($_POST['ModNumCont']);
        unset($_POST['numCont']);
        unset($_POST['emp']);
        unset($_POST['tam']);

        if($Nuevo != null && $emp != null && $Num !=null){
            $emp = $this->buscarEmp($conexion,$emp,$Num);
            $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE numCont ='".$Num."' and Empresa = '".$emp."'");
            if($res->num_rows > 0){
                $res = mysqli_query($conexion,"UPDATE contenedor SET tamaño = '".$Nuevo."' WHERE numCont ='".$Num."' and Empresa = '".$emp."' ");
                if($res){
                    echo "<div class='alert alert-success'>
                        <strong> Se ha modificado correctamente. </strong>
                    </div>";
                }
                else{
                    echo "<div class='alert alert-danger'>
                        <strong> No se ha podido cambiar .</strong>
                    </div>";
                }
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong> No se ha encontrado .</strong>
                </div>";
            }
        }
        else{
            echo "<div class='alert alert-danger'>
                <strong> Algun campo está vacío.</strong>
            </div>";
        }

    }

    function modificarEstado($conexion,$Num,$emp,$estado){
        unset($_POST['ModNumCont']);
        unset($_POST['numCont']);
        unset($_POST['emp']);
        unset($_POST['estado']);
        $emp = $this->buscarEmp($conexion,$emp,$Num);
        $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE numCont='".$Num."' and Empresa='".$emp."' ");
        if($res->num_rows > 0){
            $a_res = mysqli_fetch_assoc($res);
            if((strcasecmp($a_res['estado'],$estado) != 0)){
                $up = mysqli_query($conexion,"UPDATE contenedor SET estado = '".$estado."' WHERE numCont='".$Num."' and Empresa='".$emp."' ");
                if($up){
                    echo "<tr><td><div class='alert alert-success'>
                        <strong> Modificado con éxito </strong>
                        <p>esto puede afectar a futuros movimientos</p>
                    </div></td></tr>";
                }
                else{
                    echo "<tr><td><div class='alert alert-danger'>
                        <strong> Hubo un error </strong>
                    </div></td></tr>";
                }
            }
            else{
                echo "<tr><td><div class='alert alert-warning'>
                    <strong> el estado es el mismo </strong>
                </div></td></tr>";
            }

        }
    }
    
    function buscarCont($conexion,$num,$empresa){ //mostrar con busqueda
        unset($_POST['BCont']);
        if($conexion != null && $num != null ){
            $res = "";
            if($empresa != null){
                $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE UPPER(Empresa) LIKE UPPER('%".$empresa."%') and numCont = '".$num."' "); 
            }
            else{
                $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE numCont = '".$num."' "); 
            }

            if(!$res){
                echo "<tr><td><div class='alert alert-danger'>
                    <strong> No hay lineas </strong>
                </div></td></tr>";
            }
            else{
                echo "<table class='table table-sm table-hover' style='text-align: center;'>";

                echo "<thead class='p-3 mb-2 bg-primary text-white'>";
                    echo "<tr>";
                        echo "<th scope='col'>Número de contenedor</th>";
                        echo "<th scope='col'>Empresa propietaria</th>";
                        echo "<th scope='col'>Tamaño</th>";
                        echo "<th scope='col'>Estado</th>";
                        echo "<th>Modificar</th>";
                    echo "</tr>";
                echo "</thead>";
        
                echo "<tbody style='text-align: center;'>";
                if($res->num_rows > 0) {
                    while($a_res =  mysqli_fetch_assoc($res)){
                        if($a_res['estado'] != 'borrado'){
                            echo "<tr>";
                                echo "<td>";
                                    echo $a_res['numCont'];
                                echo "</td>";
                                echo "<td>";
                                    echo $a_res['Empresa'];
                                echo "</td>";
                                echo "<td>";
                                    echo $a_res['tamaño'];
                                echo "</td>";
                                echo "<td>";
                                    echo $a_res['estado'];
                                echo "</td>";
        
                                echo "<td>";
                                    echo "<a href= 'modules/modificarCont.php?numCont=".$a_res['numCont']."&emp=".$a_res['Empresa']." '><i style='width:5px;'class='fas fa-pencil-alt'></i></a>";
                                echo "</td>";
                                
                            echo "</tr>";
                        }
                    }
                }
            }
        }
    }

    function buscarEmp($conexion,$emp,$num){
        $empresa = $emp;
        $res = mysqli_query($conexion,"SELECT * FROM contenedor WHERE numCont='".$num."' and UPPER(Empresa) LIKE UPPER('%".$emp."%') ");
        if($res->num_rows>0){
            $a_res = mysqli_fetch_assoc($res);
            $empresa = $a_res['Empresa'];
        }
        return $empresa;
    }

    function ContarContenedores($conexion){
        $res_libre = mysqli_query($conexion,"SELECT COUNT(numCont) as Clibres  FROM contenedor WHERE estado = 'libre' ");
        $res_ocupado = mysqli_query($conexion,"SELECT COUNT(numCont) as Cocupados  FROM contenedor WHERE estado = 'ocupado' ");

        if($res_libre !== false && $res_ocupado !== false){
            echo "<table class='table table-sm table-hover' style='text-align: center;'>";

            echo "<thead class='p-3 mb-2 bg-primary text-white'>";
                echo "<tr>";
                echo "<th scope='col'>Contenedores Libres</th>";
                echo "<th scope='col'>Contenedores Ocupados</th>";
                echo "</tr>";
            echo "</thead>";

            echo "<tbody style='text-align: center;'>";
                $a_res_lib =  mysqli_fetch_assoc($res_libre);
                $a_res_ocup =  mysqli_fetch_assoc($res_ocupado);
                
                echo "<tr>";
                    echo "<td>";
                        echo $a_res_lib['Clibres'];
                    echo "</td>";

                    echo "<td>";
                        echo $a_res_ocup['Cocupados'];
                    echo "</td>";

                echo "</tr>";

        }
    }
}

$p_cont = new panel_Contenedores($conexion);
include "modules/conf_contenedores.php";

if(isset($_POST['ModNumCont'])){
    $p_cont->modificarNum($conexion,$_POST['numCont'],$_POST['emp'],$_POST['Numero']);
}
else{
    if(isset($_POST['ModEmp'])){
        $p_cont->modificarEmp($conexion,$_POST['numCont'],$_POST['emp'],$_POST['empNueva']);
    }
    else{
        if(isset($_POST['ModTam'])){
            $p_cont->modificarTam($conexion,$_POST['numCont'],$_POST['emp'],$_POST['tam']);
        }
        else{
            if(isset($_POST['ModEst'])){
                $p_cont->modificarEstado($conexion,$_POST['numCont'],$_POST['emp'],$_POST['estado']);
            }
        }
    }
}
//todo unset
if(isset($_POST['anadirCont'])){
    $p_cont->addContenedor($conexion,$_POST['Nnum'],$_POST['Nemp'],$_POST['Ntam']);
    
}

if(isset($_POST['BCont'])){
    $p_cont->buscarCont($conexion,$_POST['Bnum'],$_POST['Bemp']);
}
else{
    $p_cont->ContarContenedores($conexion);
    $p_cont->MostrarContenedores($conexion);
}





?>
</div>
</body>

</html>