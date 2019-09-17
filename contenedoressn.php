<?php
    include "modules/conexion.php";
    $titulo_pag = "Gestion Contenedores Sin Número";
    include "modules/head.php";
?>

<html>
<head>
    <script src="js/modContSN.js"></script>
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

        <li class="nav-item active">
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
class panel_Contenedores_SN{
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


    function addContenedor($conexion,$num,$emp,$numCasa){
        if($num != null){
            if($emp != null)
                $res = mysqli_query($conexion,"INSERT INTO contenedorgeneral(Empresa) VALUES ('$emp') ");
            else
                $res = mysqli_query($conexion,"INSERT INTO contenedorgeneral(Id,Empresa) VALUES (DEFAULT,DEFAULT) ");
            
            if($res !== false){
                $comp = mysqli_query($conexion,"SELECT MAX(Id) as Id FROM contenedorgeneral");
                if($comp->num_rows > 0){
                    if($a_comp =  mysqli_fetch_assoc($comp)){
                        $ID = $a_comp['Id'];
                        $res2 = false;
                        // No pondremos que sea positivo por si tiene fuera
                        if(is_null($numCasa) || !is_numeric($numCasa)){
                            $numCasa = 0;
                        }
                        if($emp != null)
                            $res2 = mysqli_query($conexion,"INSERT INTO contenedorsn (Capacidad,Empresa,Id,EnCasa) VALUES ('$num','$emp','$ID','$numCasa') ");
                        else
                            $res2 = mysqli_query($conexion,"INSERT INTO contenedorsn (Capacidad,Id,EnCasa) VALUES ('$num','$ID','$numCasa') ");
                    
                        if($res2 !== false){
                            echo "<div class='alert alert-success'>
                                <strong> Se ha insertado correctamente.</strong>
                            </div>";
                        }
                        else{
                            $res2 = mysqli_query($conexion,"DELETE FROM contenedorgeneral WHERE '".$ID."' = Id ");
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
                    <strong> No ha introducido la capacidad </strong>
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
        $res = mysqli_query($conexion, "SELECT * FROM contenedorsn ORDER BY Capacidad DESC");
        //p-3 mb-2 bg-primary text-white
        echo "<table class='table table-sm table-hover' style='text-align: center;'>";

        echo "<thead class='p-3 mb-2 bg-primary text-white'>";
            echo "<tr>";
                echo "<th scope='col'>Capacidad</th>";
                echo "<th scope='col'>Empresa propietaria</th>";
                echo "<th scope='col'>En Stock</th>";
                echo "<th scope='col'>Modificar</th>";
            echo "</tr>";
        echo "</thead>";

        echo "<tbody style='text-align: center;'>";
        if($res->num_rows > 0) {
            while($a_res =  mysqli_fetch_assoc($res)){ 
            
                echo "<tr>";
                    echo "<td>";
                        echo $a_res['Capacidad'];
                    echo "</td>";
                    echo "<td>";
                        echo $a_res['Empresa'];
                    echo "</td>";
                    echo "<td>";
                        echo $a_res['EnCasa'];
                    echo "</td>";

                    echo "<td>";
                        echo "<a href= 'modules/modificarContSN.php?Capacidad=".$a_res['Capacidad']."&emp=".$a_res['Empresa']." '><i style='width:5px;'class='fas fa-pencil-alt'></i></a>";
                    echo "</td>";
                echo "</tr>";
                

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
    
    //TODO
    function DeshacerUpSolicita($conexion,$Id,$emp,$empAnt){
        //prerrequistio: ninguno es nulo
        $fallo = true;
        $comp = mysqli_query($conexion,"SELECT * FROM solicita WHERE IdCont = '".$Id."' and EmpresaCont = '".$emp."' ");
        if($comp->num_rows > 0){ //alguien lo usa
            $a_comp =  mysqli_fetch_assoc($comp);
            $numAlb = $a_comp['NumAlbaran'];
            $res = mysqli_query($conexion,"UPDATE solicita SET Empresa ='".$empAnt."' WHERE IdCont = '".$Id."' and EmpresaCont = '".$emp."'  "); 
            if($res)
                $fallo = false;
        }
        
        return $fallo;
    }
    
    //TODO
    function UpdateSolicita($conexion,$Id,$emp,$empAnt){
        //prerrequistio: ninguno es nulo
        $fallo = true;
        $comp = mysqli_query($conexion,"SELECT * FROM solicita WHERE IdCont = '".$Id."' and EmpresaCont = '".$empAnt."' ");
        if($comp !== false && $comp->num_rows > 0){ //alguien lo usa
            $res = mysqli_query($conexion,"UPDATE solicita SET EmpresaCont ='".$emp."' WHERE IdCont = '".$Id."' and EmpresaCont = '".$empAnt."' "); 
            if($res){
                $fallo = false;
            }

        }

        return $fallo;
    }

    function modificarCapacidad($conexion,$Capacidad,$emp,$CapNueva){
        unset($_POST['ModNumCont']);
        unset($_POST['Cap']);
        unset($_POST['emp']);
        unset($_POST['CapNueva']);

        if($Capacidad != null && $emp != null && $CapNueva !=null){
            $emp = $this->buscarEmp($conexion,$emp,$Capacidad);
            $res = mysqli_query($conexion,"SELECT * FROM contenedorsn WHERE Capacidad='".$Capacidad."' and Empresa = '".$emp."'");
            
            if($res!==false && $res->num_rows > 0){

                $res = mysqli_query($conexion,"UPDATE contenedorsn SET Capacidad = '".$CapNueva."' WHERE Capacidad='".$Capacidad."' and Empresa = '".$emp."' ");
                if($res !== false){
                    echo "<div class='alert alert-success'>
                        <strong> Se ha insertado correctamente. </strong>
                    </div>";
                }
                else{
                    echo "<div class='alert alert-danger'>
                        <strong> No se ha podido cambiar. </strong>
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
                <strong> Algún campo vacío.</strong>
            </div>";
        }
    
    }

    function modificarEmp($conexion,$Capacidad,$emp,$Nuevo){
        unset($_POST['ModNumCont']);
        unset($_POST['Cap']);
        unset($_POST['emp']);
        unset($_POST['CapNueva']);
        
        if($Nuevo != null && $emp != null && $Capacidad !=null){
            
            $emp = $this->buscarEmp($conexion,$emp,$Capacidad);
            $res = mysqli_query($conexion,"SELECT * FROM contenedorsn WHERE Capacidad ='".$Capacidad."' and Empresa = '".$emp."'");
            if($res!==false && $res->num_rows > 0){
                $a_res = mysqli_fetch_assoc($res);
                $fallo = $this->UpdateSolicita($conexion,$a_res['Id'],$Nuevo,$emp);
                if($fallo == false){
                   
                    $res = mysqli_query($conexion,"UPDATE contenedorsn SET Empresa ='".$Nuevo."' WHERE Capacidad ='".$Capacidad."' and Empresa = '".$emp."' ");
                    if($res){
                        echo "<div class='alert alert-success'>
                            <strong> Se ha insertado correctamente. </strong>
                        </div>";
                    }
                    else{
                        
                        $fallo2 = $this-> DeshacerUpSolicita($conexion,$a_res['Id'],$emp,$Nuevo);
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

    function modificarStock($conexion,$Capacidad,$emp,$Nuevo){
        unset($_POST['ModNumCont']);
        unset($_POST['Cap']);
        unset($_POST['emp']);
        unset($_POST['CapNueva']);

        if($Nuevo != null && $emp != null && $Capacidad !=null){
            $emp = $this->buscarEmp($conexion,$emp,$Capacidad);
            $res = mysqli_query($conexion,"SELECT * FROM contenedorsn WHERE Capacidad ='".$Capacidad."' and Empresa = '".$emp."'");
            if($res->num_rows > 0){
                $res = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = '".$Nuevo."' WHERE Capacidad ='".$Capacidad."' and Empresa = '".$emp."' ");
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
    

    function buscarEmp($conexion,$emp,$Capacidad){
        $empresa = $emp;
        $res = mysqli_query($conexion,"SELECT * FROM contenedorsn WHERE Capacidad='".$Capacidad."' and UPPER(Empresa) LIKE UPPER('%".$emp."%') ");
        if($res->num_rows>0){
            $a_res = mysqli_fetch_assoc($res);
            $empresa = $a_res['Empresa'];
        }
        return $empresa;
    }
}

$p_cont_sn = new panel_Contenedores_SN($conexion);
include "modules/conf_contenedores_sn.php";

if(isset($_POST['ModCap'])){
    $p_cont_sn->modificarCapacidad($conexion,$_POST['Cap'],$_POST['emp'],$_POST['CapNueva']);
}
else{
    if(isset($_POST['ModEmp'])){
        $p_cont_sn->modificarEmp($conexion,$_POST['Cap'],$_POST['emp'],$_POST['empNueva']);
    }
    else{
        if(isset($_POST['ModStock'])){
            $p_cont_sn->modificarStock($conexion,$_POST['Cap'],$_POST['emp'],$_POST['stock']);
        }
    }
}

//todo unset
if(isset($_POST['anadirContSN'])){
    $p_cont_sn->addContenedor($conexion,$_POST['Cap'],$_POST['Nemp'],$_POST['NCasa']);
    
}
$p_cont_sn->MostrarContenedores($conexion);







?>
</div>
</body>

</html>