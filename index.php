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
        <li class="nav-item active">
                <a class="nav-link" href="./#">Movimientos</a>
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

        
        <li class="nav-item">
            <a class="nav-link" href="./imprimir.php">Imprimir</a>
        </li>

    </ul>
 </nav>

<div class= "container_mov">


<?php

class panel_Mov {
    private $conexion;
    public $MAX = 30;
    public $MAX2 = 15;
    private $pagi = 0;
    private $contar_pagi = 0;
    private $pagi_navegacion;
    private $numer_reg = 15;
    private $pagConfig;
    private $pag_anterior;
    private $pag_siguiente;
    private $separador;

    function _construct($conexion) {
        $this->conexion = $conexion;
        $this->MAX = 30;
        $this->MAX2 = 15;
        $this->pagi = 0;
        $this->contar_pagi = 0;
        $this->pagi_navegacion = "<a href='index.php?pagi=0>Pagina</a>";
        $this->numer_reg = 15;

    }

    function calcularpag($conexion){
        if(isset($_GET['pagi']))
            $this->pagi = $_GET['pagi'];
        else
            $this->pagi = 0;
        $this->contar_pagi = (strlen($this->pagi)); 
        // Contamos los registros totales
        $result0 = mysqli_query($conexion,"SELECT NumAlbaran FROM albaran"); 
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

    function CalcularIntervalo($fecha,$fecha_pagado){
        $daux2 = new DateTime('NOW');
        if(is_null($fecha_pagado)){
            $daux = DateTime::createFromFormat('Y-m-d',$fecha);
        }
        else{
            $daux = DateTime::createFromFormat('Y-m-d',$fecha_pagado);
        }
        $tiempo = date_diff($daux,$daux2);
        $intervalo = $tiempo->format('%R%a días');
    
        return $intervalo;
    
    }
    function CalcularIntervalo2($fecha,$fecha_pagado,$fecha2){
        $daux2 = DateTime::createFromFormat('Y-m-d',$fecha2);
        $daux = null;

        if(is_null($fecha_pagado)){
            $daux = DateTime::createFromFormat('Y-m-d',$fecha);
        }
        else{
            $daux = DateTime::createFromFormat('Y-m-d',$fecha_pagado);
        }
        $tiempo = date_diff($daux,$daux2);
        $intervalo = $tiempo->format('%R%a días');
    
        return $intervalo;
    
    }
    
    /*
        Con los contenedores sin número no se puede controlar que 
        primero haya una entrega antes que una recogida.
        Por eso, deberemos de intentar enlazar aunque sea una recogida.
        (Sobretodo, a la hora de modificar y borrar nos ahorramos problemas)

        Función: Dada una recogida, enlaza su entrega.
        Si no encontramos el enlace, con un enumerado -> cambiamos el estado
    */
    
    function Enlazar($conexion,$NumAlbaran,$local,$Emp,$IdCont,$cont,$empCont,$tipoContSN,$fecha2,$resi,$tipo){
        $conseguido = false;
        
        //buscamos su entrega
        $ocup = 'ocupado';
        $ent = 'Entrega';

        /*
            Los albaranes con la capacidad y la localización se les quita las entregas con enlaces
            comprobamos que la empresa donde lo recoge es la misma
            Cogemos los albaranes más viejos que coincidan.
        */

        $res = false;
        if($tipoContSN == 0){
            

            if(strcasecmp($tipo,'Recogida') == 0){
               
                $res = mysqli_query($conexion," SELECT albaran.NumAlbaran,albaran.fecha,solicita.fecha_pagado FROM albaran INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran
                WHERE albaran.NumAlbaran NOT IN
                (
                    SELECT NEntrega FROM enlace INNER JOIN solicita
                    ON enlace.NEntrega = solicita.NumAlbaran
                )
                and solicita.IdCont = '".$IdCont."' and solicita.EmpresaCont = '".$empCont."'
                and albaran.localizacion = '".$local."' and albaran.nomEmp = '".$Emp."'
                and albaran.tipo = 'Entrega'  
                and albaran.fecha <= '".$fecha2."'
                ORDER BY fecha ASC LIMIT 1
                ");
            }
            else{
                
                //La recogida tiene que ser después o el mismo día de la entrega
                $res = mysqli_query($conexion," SELECT albaran.NumAlbaran,albaran.fecha,solicita.fecha_pagado FROM albaran INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran
                WHERE albaran.NumAlbaran NOT IN
                (
                    SELECT NRecogida FROM enlace INNER JOIN solicita
                    ON enlace.NRecogida = solicita.NumAlbaran
                )
                and solicita.IdCont = '".$IdCont."' and solicita.EmpresaCont = '".$empCont."'
                and albaran.localizacion = '".$local."' and albaran.nomEmp = '".$Emp."'
                and albaran.tipo = 'Recogida'  
                and '".$fecha2."' <= albaran.fecha
                ORDER BY fecha ASC LIMIT 1
                ");
            }
            
        }
        else{
            
            $res = mysqli_query($conexion,"SELECT albaran.NumAlbaran,albaran.fecha,solicita.fecha_pagado FROM albaran,contenedor,solicita   
            WHERE contenedor.estado = '".$ocup."'  
            and solicita.IdCont = contenedor.Id and solicita.EmpresaCont = '".$empCont."' 
            and solicita.EmpresaCont = contenedor.Empresa and contenedor.numCont = '".$cont."'
            and albaran.tipo = '".$ent."' and solicita.NumAlbaran = albaran.NumAlbaran
            and albaran.nomEmp = '".$Emp."' and albaran.localizacion = '".$local."' 
            and albaran.NumAlbaran NOT IN (SELECT NEntrega FROM enlace) ");
        }

        if($res !== false){
            if($res->num_rows > 0){
               
                //cogemos uno
                $a_res =  mysqli_fetch_assoc($res);
                //vinculamos 
                $aux = $a_res['NumAlbaran'];
                
                //¿Residuo nulo?
                if($resi != null){
                    if(strcasecmp($tipo,'Recogida') == 0){
                        //Busca una entrega -> fecha entrega, fecha pagado, fecha de la recogida
                        $intervalo = $this->CalcularIntervalo2($a_res['fecha'],$a_res['fecha_pagado'],$fecha2);
                        $res2 = mysqli_query($conexion,"INSERT INTO enlace (NEntrega,NRecogida,dias,residuo) VALUES ('$aux','$NumAlbaran','$intervalo','$resi') ");

                    }
                    else{
                        $intervalo = $this->CalcularIntervalo2($fecha2,$a_res['fecha_pagado'],$a_res['fecha']);
                        $res2 = mysqli_query($conexion,"INSERT INTO enlace (NEntrega,NRecogida,dias,residuo) VALUES ('$NumAlbaran','$aux','$intervalo','$resi') ");

                    }
                    
                    if($res2 != false){
                        
                        $conseguido = true;
                    }
                    
                }
                else{
                    if(strcasecmp($tipo,'Recogida') == 0){
                        $intervalo = $this->CalcularIntervalo2($a_res['fecha'],$a_res['fecha_pagado'],$fecha2);
                        $res2 = mysqli_query($conexion,"INSERT INTO enlace (NEntrega,NRecogida,dias) VALUES ('$aux','$NumAlbaran','$intervalo') ");

                    }
                    else{
                        $intervalo = $this->CalcularIntervalo2($fecha2,$a_res['fecha_pagado'],$a_res['fecha']);
                        $res2 = mysqli_query($conexion,"INSERT INTO enlace (NEntrega,NRecogida,dias) VALUES ('$NumAlbaran','$aux','$intervalo') ");

                    }
                    
                    if($res2 != false){
                        
                        $conseguido = true;
                    }

                }

            }
            
        }

        if($conseguido == false && $tipoContSN != 0){
            // Cambiamos el estado del contenedor
            $estado = 'ocupado';    
            $res2 = mysqli_query($conexion,"UPDATE contenedor SET estado = '".$estado."' WHERE numCont ='".$cont."' and Empresa = '".$empCont."' and Id = '".$IdCont."' ");
        }

        return $conseguido;
    }

    function MostrarMovimientos($conexion){

        $this->calcularpag($conexion);
        $l_sup = $this->numer_reg;
        $l_inf = 0; // Si NO recibimos un valor por la variable $page
        if ($this->contar_pagi > 0) { 
            // Si recibimos un valor por la variable $page ejecutamos esta consulta
            $l_inf = $this->pagi;  
        }

        $enlaceRec = false;
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
                echo "<th scope='col'>Ver</th>";
                

            echo "</tr>";
        echo "</thead>";

        echo "<tbody>";
            $res = mysqli_query($conexion, "SELECT * FROM albaran 
            INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran ORDER BY albaran.fecha DESC LIMIT $l_inf,$l_sup");
    
            if($res !== false){
                if($res->num_rows > 0){
                    while($a_res =  mysqli_fetch_assoc($res)){
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
                            
                            $res2 = false;
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
                                while($a_res2 =  mysqli_fetch_assoc($res2)){
                                    $fecha_pagado = $a_res2['fecha_pagado'];

                                    if($boolSN == 0){
                                        echo "<td>";
                                        echo $a_res2['Capacidad'];
                                        echo "  ";
                                        echo $a_res2['Empresa'];
                                        echo "</td>";
                                    }
                                    else{
                                        echo "<td>";
                                        echo $a_res2['numCont'];
                                        echo "  ";
                                        echo $a_res2['Empresa'];
                                        echo "</td>";
                                    }
                                }
                            }
                            else{
                                echo "<tr><td><div class='alert alert-danger'>
                                    <strong> Error en la Base de Datos </strong>
                                </div></td></tr>";
                                //die('Error SQL: ' . mysqli_error($conexion)); 
                            }

                            // FASE ENLACE
                            
                            $tipo_entrega = strcasecmp($a_res['tipo'],"ENTREGA");
                            $res3 = false;
                            if($tipo_entrega == 0){
                                $res3 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NEntrega = '".$a_res['NumAlbaran']."' ");
                            }
                            else{
                                $res3 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NRecogida = '".$a_res['NumAlbaran']."' ");
                            }
                            $enlace_alb = null;
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
                        
                                        echo "<td>";
                                        if($tipo_entrega == 0){
                                            $enlace_alb = $a_res3['NRecogida'];
                                            echo $a_res3['NRecogida'];
                                        }
                                            
                                        else{
                                            $enlace_alb = $a_res3['NEntrega'];
                                            echo $a_res3['NEntrega'];
                                        }
                                            
                        
                                        echo "</td>";
                                    }
                                }
                                else{  
                                    //No enlazado
                                    
                                    echo "<td>";
                                    echo "-";
                                    echo "</td>";
                                
                                    echo "<td>";
                
                                        $intervalo = $this->CalcularIntervalo($a_res['fecha'],$fecha_pagado);
                    
                                        if(!is_null($fecha_pagado)){
                                            echo "pagado" ;
                                        
                                        }
                                        else{
                                            if($intervalo >= $this->MAX2 && $intervalo < $this->MAX){
                                                echo "<p style='color:#e1471e'>";
                                                echo $intervalo;
                                                echo "</p>";
                                            }
                                            else{
                                                if($intervalo >= $this->MAX){
                                                    echo "<p style='color:red'><strong>";
                                                    echo $intervalo;
                                                    echo "</strong></p>";
                                                }
                                                else{
                                                    echo $intervalo;
                                                }
                                            }
                                        }

                                        echo "</td>";
                        
                                        echo "<td>";
                                        echo "No enlazado";
                                        echo "</td>";
                                                                                
                
                                }
                            }
                            else{
                                echo "<tr><td><div class='alert alert-danger'>
                                <strong> Error en la Base de Datos </strong>
                                </div></td></tr>";
                                die('Error SQL: ' . mysqli_error($conexion));
                            }

                            echo "<td>";
                            /*echo "<a href= 'modules/modificarMovNuevo.php?num=".$NumAlbaran."&tipo=".$tipo." '><i style='width:5px;'class='fas fa-pencil-alt'></i></a>";
                            //text-decoration:none quitar color azul de hipervinculo
                            echo "<a style= 'margin-left: 30px;text-decoration:none;color:red;' href='./modules/modal.php?numBorrar=".$NumAlbaran." '><i style='width:5px;'class='fas fa-eraser'></i></a>";
                            */
                            if($enlace_alb != NULL)
                                echo "<a href= 'modules/muestra_movimiento_especifico.php?NAlb=".$a_res['NumAlbaran']."&tipo=".$a_res['tipo']."&enlazado=".$enlace_alb."&ContenedorSN=".$boolSN." '> <i style='width:5px;'class='fas fa-eye'></i></a>";
                            else
                                echo "<a href= 'modules/muestra_movimiento_especifico.php?NAlb=".$a_res['NumAlbaran']."&tipo=".$a_res['tipo']."&ContenedorSN=".$boolSN."'> <i style='width:5px;'class='fas fa-eye'></i></a>";
                            
                            
                            echo "</td>";

                            echo "</tr>";
                        }
                    }
                    else{
                        echo "<tr><td><div class='alert alert-danger'>
                                <strong> No líneas </strong>
                        </div></td></tr>";
                    
                    }
                }
                else{
                    echo "<tr><td><div class='alert alert-danger'>
                        <strong> Error en la Base de Datos </strong>
                    </div></td></tr>";
                    die('Error SQL: ' . mysqli_error($conexion));
                }

        echo "</tbody>";
        echo "</table>";
        echo "<p align='center'>";
        echo $this->pagi_navegacion;
        echo "</p>";  

        

    }

    function ChangeSolicitaSN($conexion,$NAlb,$IdCont,$varEmp,$Capacidad,$tipoMov){

        $conseguido = false;        
        
        $res2 = false;
        if(strcasecmp($tipoMov,"Entrega") == 0){
            // tipo entrega, quitamos uno de casa
            $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa-1 WHERE Capacidad ='".$Capacidad."' and Empresa = '".$varEmp."' and Id = '".$IdCont."' ");    
        }
        else{
            //actualizamos 
            $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa+1 WHERE Capacidad ='".$Capacidad."' and Empresa = '".$varEmp."' and Id = '".$IdCont."' ");
        }
        if($res2 != false){
            $conseguido = true;
        }
        else{
            // Borramos la fila insertada del solicita y damos error
            $res3 = mysqli_query($conexion,"DELETE FROM solicita WHERE '".$NAlb."' = NumAlbaran ");
            
            echo "<div class='alert alert-danger'>
                <strong>No se insertó correctamente.</strong>
            </div>";

            if($res3 == false){
                echo "<div class='alert alert-warning'>
                    <strong>Incongruencias en Base de datos</strong>
                </div>";
            }
        }
        
        
        return $conseguido;
    }

    function ChangeSolicitaConNum($conexion,$NAlb,$IdCont,$varEmp,$NumCont,$tipoMov){

        $conseguido = false;

        $res2 = false;
        if(strcasecmp($tipoMov,"Entrega") == 0){
            // tipo entrega, pasa a estar ocupado
            $res2 = mysqli_query($conexion,"UPDATE contenedor SET estado = 'ocupado' WHERE numCont = '".$NumCont."' and Empresa ='".$varEmp."' ");
        }
        else{
            //enlazar y actualizar

            $res2 = mysqli_query($conexion,"UPDATE contenedor SET estado = 'libre' WHERE numCont = '".$NumCont."' and Empresa ='".$varEmp."' ");
        
        }
        if($res2 != false){
            $conseguido = true;
        }
        else{
            // Borramos la fila insertada del solicita y damos error
            $res3 = mysqli_query($conexion,"DELETE FROM solicita WHERE '".$NAlb."' = NumAlbaran ");
            
            echo "<div class='alert alert-danger'>
                <strong>No se insertó correctamente.</strong>
            </div>";
            if($res3 == false){
                echo "<div class='alert alert-warning'>
                    <strong>Incongruencias en Base de datos</strong>
                </div>";
            }
        }
        

        return $conseguido;
    }

    function AddMovimiento($conexion,$NAlb,$fecha,$tipo,$local,$Emp,$tipoCont,$cont,$ContEmp,$resi){
        
        if($NAlb!=null && $fecha!=null && $tipo!=null && $Emp!=null &&
            $local!=null && $cont!=null && $tipoCont!=null && $ContEmp != null){
                
            /*si todos los campos están rellenos
            buscamos el contenedor, el nombre de empresa es más fácil que se equivoquen, pondré Like
            con UPPER se pasa a mayuscula todo*/

            $tipoContSN = strcasecmp($tipoCont,"SN");
            $comp = false;
            if($tipoContSN == 0){
                $comp = mysqli_query($conexion,"SELECT * FROM contenedorsn WHERE UPPER(Empresa) LIKE UPPER('%".$ContEmp."%') and Capacidad = '".$cont."' ");
            }
            else{
                
                $comp = mysqli_query($conexion,"SELECT * FROM contenedor WHERE UPPER(Empresa) LIKE UPPER('%".$ContEmp."%')  and numCont = '".$cont."' ");
            }
            
            //vemos si el cliente tiene esa obra

            $comp2 = mysqli_query($conexion,"SELECT * FROM obracivil WHERE nomEmp='".$Emp."' and localizacion='".$local."' ");
            
            if($comp != false && $comp2 != false){

                if($comp2->num_rows > 0 && $comp->num_rows > 0 ){
                    //se encontró
                    //vemos si el estado del contenedor es válido

                    while($a_comp =  mysqli_fetch_assoc($comp)){
                        
                        $varEmp = $a_comp['Empresa'];
                        $lib = 'libre';
                        $ocup = 'ocupado';
                        $ent = 'Entrega';
                        
                        $BBaja = strcasecmp('baja',$a_comp['estado']);

                        if($tipoContSN !=0 && $BBaja == 0){
                            //es de los numerados que tienen estado
                            //está de baja damos error
                            echo "<div class='alert alert-warning'>
                                <strong> El contenedor está de baja</strong>
                            </div>";
                        }
                        else{
                            $res = false;
                            if( $tipoContSN != 0 ){
                                $Blibre = strcasecmp($lib,$a_comp['estado']);
                                $Bocup = strcasecmp($ocup,$a_comp['estado']);
                                if (( $Blibre == 0 && (strcasecmp($tipo,$ent) == 0) ) || (( $Bocup == 0) && (strcasecmp($tipo,$ent) != 0  ) )) {
                                    //si entrega y libre o recogida y ocupado
                                    
                                    $res = mysqli_query($conexion,"INSERT INTO albaran (NumAlbaran,nomEmp,localizacion,fecha,tipo) VALUES ('$NAlb','$Emp','$local','$fecha','$tipo')");
                                }
                                else{
                                    //el contenedor está ocupado y vas a entregarlo o el contenedor vacio y vas a recogerlo
                                        echo "<div class='alert alert-danger'>
                                            <strong>Estado del contenedor incoherente.</strong>
                                        </div>";
                                }
                            }
                            else{
                                $res = mysqli_query($conexion,"INSERT INTO albaran (NumAlbaran,nomEmp,localizacion,fecha,tipo) VALUES ('$NAlb','$Emp','$local','$fecha','$tipo')");
                            }
                            
                            if($res !== false){
                                $id = $a_comp['Id'];
                                //$conseguido = false;
                                $res2 = false;
                                if($tipoContSN == 0){
                                    //es Sin Número
                                    
                                    $res2 = mysqli_query($conexion,"INSERT INTO solicita (NumAlbaran,IdCont,EmpresaCont,tipoCont) VALUES ('$NAlb','$id','$varEmp','SN')");
                                   
                                }
                                else{ 
                                    $res2 = mysqli_query($conexion,"INSERT INTO solicita (NumAlbaran,IdCont,EmpresaCont,tipoCont) VALUES ('$NAlb','$id','$varEmp','Enumerado')");
                                }
                                if($res2 !== false){
                                    $conseguido2 = false;
                                    if($tipoContSN == 0){
                                        $id = $a_comp['Id'];
                                        
                                        $conseguido2 = $this->Enlazar($conexion,$NAlb,$local,$Emp,$id,$cont,$varEmp,$tipoContSN,$fecha,$resi,$tipo);
    
                                    }
                                    else{
                                        if(strcasecmp($tipo,'Recogida') == 0){
                                            //tipo recogida, buscamos su entrega y actualizamos
                                            $id = $a_comp['Id'];
                                            
                                            $conseguido2 = $this->Enlazar($conexion,$NAlb,$local,$Emp,$id,$cont,$varEmp,$tipoContSN,$fecha,$resi,$tipo);
                                            
                                        }
                                    }
                                    
                                    if( ($conseguido2 == false && $tipoContSN == 0) || ($tipoContSN !=0 && (strcasecmp($tipo,'Recogida') == 0) && $conseguido2 == false) ){
                                        //TODO Ver en que casos hay que borrar
                                        echo "<div class='alert alert-warning'>
                                        <strong> No se pudo vincular </strong>
                                        </div>";
                                    }
                                    
                                    $conseguido = false;
                                    if($tipoContSN == 0){
                                        //es Sin Número
                                        $conseguido = $this->ChangeSolicitaSN($conexion,$NAlb,$id,$varEmp,$cont,$tipo);
                                    }
                                    else{ 
                                        
                                        $conseguido =  $this->ChangeSolicitaConNum($conexion,$NAlb,$id,$varEmp,$cont,$tipo);
                                    }
    
                                    if($conseguido == false){
                                        //borramos todo
                                        $borrar = mysqli_query($conexion,"DELETE FROM enlace WHERE '".$NAlb."' = NumEntrega OR '".$NAlb."' = NumRecogida ");
                                        $borrar2 = mysqli_query($conexion,"DELETE FROM albaran WHERE '".$NAlb."' = NumAlbaran ");
                                        
                                        if($borrar == false || $borrar2 == false ){
                                            echo "<div class='alert alert-warning'>
                                            <strong> Incongruencias en la base de datos </strong>
                                            </div>";
                                        }
                                    }
                                    else{
                                        echo "<div class='alert alert-success'>
                                        <strong> Añadido correctamente </strong>
                                        </div>";
                                    }
    
                                }
                                else{
                                    //borrar albaran
                                    $res3 = mysqli_query($conexion,"DELETE FROM albaran WHERE '".$NAlb."' = NumAlbaran ");
    
                                    if($res3 === false){
                                        echo "<div class='alert alert-warning'>
                                        <strong> Estado de la base de datos incoherente</strong>
                                        </div>";
                                    }
                                    else{
                                        echo "<div class='alert alert-warning'>
                                        <strong> No se pudo realizar esta operación</strong>
                                        </div>";
                                    }
    
                                }
                            }
                            else{
                                echo "<div class='alert alert-warning'>
                                <strong> No se pudo realizar esta operación</strong>
                                </div>";
                            }
                        }
                    }
                }
                else{
                    echo "<div class='alert alert-warning'>
                    <strong> No se ha encontrado el contenedor o la obra</strong>
                    </div>";
                }   
            }
            else{
                echo "<div class='alert alert-warning'>
                <strong> No se pudo realizar esta operación</strong>
                </div>";
            }            
        } 
        else{
            //ERROR
            echo "<div class='alert alert-danger'>
                <strong>Algún campo está vacio</strong>
            </div>";
        }
        
        unset($_POST['anadirMov']);
    }

    function PrincipioTablaNormal(){
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
            echo "<th scope='col'>Ver</th>";

            echo "</tr>";
        echo "</thead>";

        echo "<tbody>";
    }
    
    function calcularpagFecha($conexion,$fecha){
        $conseguido = false;

        if(isset($_GET['pagi']))
            $this->pagi = $_GET['pagi'];
        else
            $this->pagi = 0;
        $this->contar_pagi = (strlen($this->pagi)); 
        // Contamos los registros totales
        $res = mysqli_query($conexion,"SELECT COUNT(NumAlbaran) as registros FROM albaran WHERE '".$fecha."' = albaran.fecha");

        if($res !== false){
            $conseguido = true;
            $data=mysqli_fetch_assoc($res);
            $numero_registros0 = $data['registros']; 
        
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
        else
            $this->pagi_navegacion = null;

        return $conseguido;

    }

    function BuscarFecha($conexion,$fecha){
        unset($_POST['BuscarFecha']);
        unset($_POST['Bfecha']);

        if(!is_null($fecha)){
           /* $conseguido = $this->calcularpagFecha($conexion,$fecha);
            if($conseguido){
                $l_sup = $this->numer_reg;
                $l_inf = 0; // Si NO recibimos un valor por la variable $page
                if ($this->contar_pagi > 0) { 
                    // Si recibimos un valor por la variable $page ejecutamos esta consulta
                    $l_inf = $this->pagi;  
                }
                $res = mysqli_query($conexion,"SELECT * FROM albaran 
                INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$fecha."' = albaran.fecha LIMIT $l_inf,$l_sup");    
            }
            else{
                $res = mysqli_query($conexion,"SELECT * FROM albaran 
                INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$fecha."' = albaran.fecha");
            }*/
            $res = mysqli_query($conexion,"SELECT * FROM albaran 
            INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$fecha."' = albaran.fecha");

            $this->PrincipioTablaNormal();
            //buscar el contenedor por el id
            
            if($res!== false && $res->num_rows > 0) {

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
                            $fecha_pagado = $a_res2['fecha_pagado'];

                            if($boolSN == 0){
                                $contenedor = $a_res2['Capacidad'];
                                $EmpCont = $a_res2['Empresa'];
                            }
                            else{
                                $contenedor = $a_res2['numCont'];
                                $EmpCont = $a_res2['Empresa'];
                            }
                            
                            $this->MostrarAux($conexion,$a_res['NumAlbaran'],$a_res['tipo'],$a_res['fecha'],$a_res['nomEmp'],$a_res['localizacion'],$contenedor,$EmpCont,$a_res['fecha_pagado'],$boolSN);
                            
                        }
                    }

                }
                echo "</tbody>";
                echo "</table>";
                /*echo "<p align='center'>";
                echo $this->pagi_navegacion;
                echo "</p>";  
                */
                
            }
            else{
                echo "<tr><td><div class='alert alert-danger'>
                        <strong> No hay lineas </strong>
                    </div></td></tr>";
            }


        }
        else{
            echo "<div class='alert alert-warning'>
            <strong>Se necesita una fecha válida</strong>
            </div>";
        }

    }

    function BuscarAlbaran($conexion,$num){

        unset($_POST['Bnum']);
        unset($_POST['BuscarNum']);

        if(!is_null($num)){
            $res = mysqli_query($conexion,"SELECT * FROM albaran 
            INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$num."' = albaran.NumAlbaran ");

            $this->PrincipioTablaNormal();
            //buscar el contenedor por el id
            
            if($res!== false && $res->num_rows > 0) {
                //solo habrá 1
                if($a_res =  mysqli_fetch_assoc($res)){
                    
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
                            $fecha_pagado = $a_res2['fecha_pagado'];

                            if($boolSN == 0){
                                $contenedor = $a_res2['Capacidad'];
                                $EmpCont = $a_res2['Empresa'];
                            }
                            else{
                                $contenedor = $a_res2['numCont'];
                                $EmpCont = $a_res2['Empresa'];
                            }
                            
                            $this->MostrarAux($conexion,$a_res['NumAlbaran'],$a_res['tipo'],$a_res['fecha'],$a_res['nomEmp'],$a_res['localizacion'],$contenedor,$EmpCont,$a_res['fecha_pagado'],$boolSN);
                            
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

        }
        else{
            echo "<div class='alert alert-warning'>
            <strong>Se necesita un número válido</strong>
            </div>";
        }

    } 

    function calcularpagCliente($conexion,$cli){
        $conseguido = false;

        if(isset($_GET['pagi']))
            $this->pagi = $_GET['pagi'];
        else
            $this->pagi = 0;
        $this->contar_pagi = (strlen($this->pagi)); 
        // Contamos los registros totales
        $res = mysqli_query($conexion,"SELECT COUNT(NumAlbaran) as registros FROM albaran WHERE '".$cli."' = nomEmp");

        if($res !== false){
            $conseguido = true;
            $data=mysqli_fetch_assoc($res);
            $numero_registros0 = $data['registros'];
        
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
        else
            $this->pagi_navegacion = null;

        return $conseguido;

    }

    function BuscarCliente($conexion,$cli){

        unset($_POST['BCln']);
        unset($_POST['BuscarClnt']);

        if(!is_null($cli)){
            /*$conseguido = $this->calcularpagCliente($conexion,$cli);
            if($conseguido){
                $l_sup = $this->numer_reg;
                $l_inf = 0; // Si NO recibimos un valor por la variable $page
                if ($this->contar_pagi > 0) { 
                    // Si recibimos un valor por la variable $page ejecutamos esta consulta
                    $l_inf = $this->pagi;  
                }
                $res = mysqli_query($conexion,"SELECT * FROM albaran 
                INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$cli."' = albaran.nomEmp LIMIT $l_inf,$l_sup");    
            }
            else{
                $res = mysqli_query($conexion,"SELECT * FROM albaran 
                INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$cli."' = albaran.nomEmp ");
            }*/

            $res = mysqli_query($conexion,"SELECT * FROM albaran 
            INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran WHERE '".$cli."' = albaran.nomEmp ");

            $this->PrincipioTablaNormal();
            //buscar el contenedor por el id
            
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
                            $fecha_pagado = $a_res2['fecha_pagado'];

                            if($boolSN == 0){
                                $contenedor = $a_res2['Capacidad'];
                                $EmpCont = $a_res2['Empresa'];
                            }
                            else{
                                $contenedor = $a_res2['numCont'];
                                $EmpCont = $a_res2['Empresa'];
                            }
                            
                            $this->MostrarAux($conexion,$a_res['NumAlbaran'],$a_res['tipo'],$a_res['fecha'],$a_res['nomEmp'],$a_res['localizacion'],$contenedor,$EmpCont,$a_res['fecha_pagado'],$boolSN);
                            
                        }
                    }

                }
                echo "</tbody>";
                echo "</table>";
                //echo "<p align='center'>$this->pagi_navegacion</p>";
            }
            else{
                echo "<tr><td><div class='alert alert-danger'>
                        <strong> No hay lineas </strong>
                    </div></td></tr>";
            }


        }
        else{
            echo "<div class='alert alert-warning'>
            <strong>Se necesita un número válido</strong>
            </div>";
        }

    } 

    function BuscarLimiteSuperado($conexion,$lim){

        unset($_POST['BuscarLim']);
        unset($_POST['Dias']);


        $res = mysqli_query($conexion,"SELECT albaran.NumAlbaran,albaran.nomEmp,
            albaran.localizacion,albaran.nomEmp,albaran.fecha,solicita.fecha_pagado,albaran.tipo,solicita.tipoCont FROM albaran 
            INNER JOIN solicita ON albaran.NumAlbaran = solicita.NumAlbaran ");
        
    
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
            echo "<th scope='col'>Ver</th>";
            echo "<th scope='col'>Pagar</th>";

            echo "</tr>";
        echo "</thead>";

        echo "<tbody>";

        if($res!== false && $res->num_rows > 0){
            
            while($a_res =  mysqli_fetch_assoc($res) ){
                $dias = 0;
                /*
                    miramos a ver:
                    - ¿Enlace? sí -> días
                    - No tiene enlace -> intervalo

                */

                $dias = $this->CalcularIntervalo($a_res['fecha'],$a_res['fecha_pagado']);

                $tipo_entrega = strcasecmp($a_res['tipo'],"ENTREGA");
                $res2 = false;
                if($tipo_entrega == 0){
                    $res2 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NEntrega = '".$a_res['NumAlbaran']."' ");
                }
                else{
                    $res2 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NRecogida = '".$a_res['NumAlbaran']."' ");
                }
                $enlace_alb = null;
                $residuo = null;
                if($res2 !== false && $res2->num_rows > 0){
                    //algo enlazado
                    if($a_res2 =  mysqli_fetch_assoc($res2)){
                        $dias = $a_res2['dias'];
                        
                        if(! is_null($a_res2['residuo']))
                            $residuo = $a_res2['residuo'];
                        else
                            $residuo = null;
                        
                        if($tipo_entrega == 0){
                            $enlace_alb = $a_res2['NRecogida'];
                            
                        }
                        else{
                            $enlace_alb = $a_res2['NEntrega'];
                            
                        }  
                    }
                }
                else{
                    $dias = $this->CalcularIntervalo($a_res['fecha'],$a_res['fecha_pagado']);
                }

                $dias = intval($dias);
                if($dias >= $lim){
                    //mostramos
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
                    
                    $res3 = false;
                    
                    $boolSN = strcasecmp ($a_res['tipoCont'],'SN');
                    if($boolSN == 0){
                        //Sin Número
                        //Lo hacemos con los específicos para luego sacar el contenedor
                        $res3 = mysqli_query($conexion, "SELECT * FROM solicita INNER JOIN contenedorsn ON solicita.IdCont = contenedorsn.Id and solicita.EmpresaCont = contenedorsn.Empresa and solicita.NumAlbaran = '".$a_res['NumAlbaran']."' ");
                    }
                    else{
                        $res3 = mysqli_query($conexion,"SELECT * FROM solicita INNER JOIN contenedor ON solicita.IdCont = contenedor.Id and solicita.EmpresaCont = contenedor.Empresa and solicita.NumAlbaran = '".$a_res['NumAlbaran']."' ");
                    }
                    $fecha_pagado = null;

                    if($res3 !== false){
                        if($a_res3 =  mysqli_fetch_assoc($res3)){

                            if($boolSN == 0){
                                echo "<td>";
                                echo $a_res3['Capacidad'];
                                echo "  ";
                                echo $a_res3['Empresa'];
                                echo "</td>";
                            }
                            else{
                                echo "<td>";
                                echo $a_res3['numCont'];
                                echo "  ";
                                echo $a_res3['Empresa'];
                                echo "</td>";
                            }
                        }
                    }
                    else{
                        echo "<tr><td><div class='alert alert-danger'>
                            <strong> Error en la Base de Datos </strong>
                        </div></td></tr>";
                        die('Error SQL: ' . mysqli_error($conexion)); 
                    }

                    //Enlace
                    if(!is_null($enlace_alb)){
                        echo "<td>";
                            if(is_null($residuo))
                                echo "-";
                            else
                                echo $residuo;
                        echo "</td>";

                        echo "<td>";
                            echo "<p style='color:red'><strong>";
                                echo $dias;
                            echo "</strong></p>";
                        echo "</td>";

                        echo "<td>";
                            echo $enlace_alb;
                        echo "</td>";
                    }
                    else{
                        echo "<td>";
                            echo "-";
                        echo "</td>";

                        echo "<td>";
                            echo "<p style='color:red'><strong>";
                                echo $dias;
                            echo "</strong></p>";
                        echo "</td>";

                        echo "<td>";
                            echo "No enlazado";
                        echo "</td>";
                    }

                    echo "<td>";
                    if($enlace_alb != NULL)
                        echo "<a href= 'modules/muestra_movimiento_especifico.php?NAlb=".$a_res['NumAlbaran']."&tipo=".$a_res['tipo']."&enlazado=".$enlace_alb."&ContenedorSN=".$boolSN." '> <i style='width:5px;'class='fas fa-eye'></i></a>";
                    else
                        echo "<a href= 'modules/muestra_movimiento_especifico.php?NAlb=".$a_res['NumAlbaran']."&tipo=".$a_res['tipo']."&ContenedorSN=".$boolSN."'> <i style='width:5px;'class='fas fa-eye'></i></a>";
                    
                    echo "</td>";
                    
                    echo "<td>";
                        echo "<form method='post' action='index.php'>";
                            echo "<input type=hidden name='NumPay' id='NumPay' value='".$a_res['NumAlbaran']."'>"; 
                            echo "<button type='submit' class='btn btn-primary btn-sm' name=Pay id=Pay>Pagar</button>";
                        echo "</form>";
                    echo "</td>";
                    echo "</tr>";

                }

            }
            echo "</tbody>";
            echo "</table>";

        }
        

    }

    function MostrarAux($conexion,$NumAlbaran,$tipo,$fecha,$nomEmp,$localizacion,$cont,$empresa,$fecha_pagado,$boolSN){
        
        echo "<tr>";
            echo "<td>";
                echo $NumAlbaran;
            echo "</td>";
            echo "<td>";
                echo $tipo;
            echo "</td>";
            echo "<td>";
                $daux = DateTime::createFromFormat('Y-m-d',$fecha);
                
                echo $daux->format('d-m-Y');
            echo "</td>";
            echo "<td>";
                echo $nomEmp;
            echo "</td>";

            echo "<td>";
                echo $localizacion;
            echo "</td>";

            echo "<td>";
                echo $cont;
                echo " ";
                echo $empresa;
            echo "</td>";
            
                
            $tipo_entrega = strcasecmp($tipo,"ENTREGA");
            $res3 = false;
            if($tipo_entrega == 0){
                $res3 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NEntrega = '".$NumAlbaran."' ");
            }
            else{
                $res3 = mysqli_query($conexion, "SELECT * FROM enlace WHERE NRecogida = '".$NumAlbaran."' ");
            }
            $enlace_alb = null;
            if($res3 !== false){
                if($res3->num_rows > 0){
                    //algo enlazado
                    if($a_res3 =  mysqli_fetch_assoc($res3)){
                        echo "<td>";
                        if( is_null($a_res3['residuo']))
                            echo "-";
                        else
                            echo $a_res3['residuo'];
                        echo "</td>";
                
                        echo "<td>";
                        echo $a_res3['dias'];
                        echo "</td>";
        
                        echo "<td>";
                        if($tipo_entrega == 0){
                            $enlace_alb = $a_res3['NRecogida'];
                            echo $a_res3['NRecogida'];
                        }
                        else{
                            $enlace_alb = $a_res3['NEntrega'];
                            echo $a_res3['NEntrega'];
                        }
                            
        
                        echo "</td>";
                    }
                }
                else{  
                    //No enlazado

                    echo "<td>";
                    echo "-";
                    echo "</td>";
                
                    echo "<td>";

                        $intervalo = $this->CalcularIntervalo($fecha,$fecha_pagado);
    
                        if(!is_null($fecha_pagado))
                            echo "pagado ";
                        else{
                            if($intervalo >= $this->MAX2 && $intervalo < $this->MAX){
                                echo "<p style='color:#e1471e'>";
                                echo $intervalo;
                                echo "</p>";
                            }
                            else{
                                if($intervalo >= $this->MAX){
                                    echo "<p style='color:red'><strong>";
                                    echo $intervalo;
                                    echo "</strong></p>";
                                }
                                else{
                                    echo $intervalo;
                                }
                            }
                        }

                        echo "</td>";
        
                        echo "<td>";
                        echo "No enlazado";
                        echo "</td>";   

                }
            }
            else{
                echo "<tr><td><div class='alert alert-danger'>
                    <strong> Error en la Base de Datos </strong>
                </div></td></tr>";
                die('Error SQL: ' . mysqli_error($conexion)); 
            }
                        
            echo "<td>";
                if($enlace_alb != NULL)
                    echo "<a href= 'modules/muestra_movimiento_especifico.php?NAlb=".$NumAlbaran."&tipo=".$tipo."&enlazado=".$enlace_alb."&ContenedorSN=".$boolSN." '> <i style='width:5px;'class='fas fa-eye'></i></a>";
                else
                    echo "<a href= 'modules/muestra_movimiento_especifico.php?NAlb=".$NumAlbaran."&tipo=".$tipo."&ContenedorSN=".$boolSN."'> <i style='width:5px;'class='fas fa-eye'></i></a>";
            echo "</td>";

        echo "</tr>";
        
    }

    function Pagar($conexion,$num){
        $res = mysqli_query($conexion,"SELECT * FROM solicita WHERE NumAlbaran = '".$num."' ");
        if($res!== false && $res->num_rows > 0){
            if($a_res = mysqli_fetch_assoc($res)){
                $fecha_anterior = $a_res['fecha_pagado'];
            
                $daux = new DateTime('NOW');
                $daux2 = $daux->format('Y-m-d');
                $res2 = mysqli_query($conexion,"UPDATE solicita SET fecha_pagado = '".$daux2."' WHERE NumAlbaran = '".$num."' ");
                if($res2 === false){
                    echo "<tr><td><div class='alert alert-danger'>
                        <strong> No se ha podido pagar. </strong>
                    </div></td></tr>";
                }
                else{
                    $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$num."' OR NRecogida = '".$num."' ");
                    //pagamos uno que ya hemos recogido y se pasó de dias
                    if($comp !== false && $comp->num_rows > 0){
                        $comp2 = mysqli_query($conexion,"UPDATE enlace SET enlace.dias = 0 WHERE NEntrega = '".$num."' OR NRecogida = '".$num."' ");
                        if($comp2 !== false){
                                echo "<tr><td><div class='alert alert-success'>
                                    <strong> Pagado </strong>
                                </div></td></tr>";
                        }
                        else{
                            //TODO Deshacer el solicita
                                $res3 = mysqli_query($conexion,"UPDATE solicita SET fecha_pagado ='".$fecha_anterior."' WHERE NumAlbaran ='".$num."' "); 
                                if($res3 !== false){
                                    echo "<tr><td><div class='alert alert-danger'>
                                        <strong> No se ha podido pagar. </strong>
                                    </div></td></tr>";
                                }
                                else{
                                    echo "<tr><td><div class='alert alert-danger'>
                                        <strong> No se ha podido pagar bien. </strong> Incongruencias en base de datos.
                                    </div></td></tr>";
                                }
                        }
                    }
                    else{
                        //no tenia enlace pero ha pagado
                        echo "<tr><td><div class='alert alert-success'>
                                <strong> Pagado </strong>
                        </div></td></tr>";
                    }

                }
                
            }
            
        }
        else{
            echo "<tr><td><div class='alert alert-danger'>
                <strong> No se ha podido pagar. </strong>
            </div></td></tr>";
        }
        unset($_POST['Pay']);
        unset($_POST['NumPay']);
    }

    function BuscarSinEnlace($conexion){

        $res = mysqli_query($conexion," SELECT * FROM albaran NATURAL JOIN solicita WHERE albaran.NumAlbaran 
            NOT IN (SELECT NEntrega FROM enlace ) and albaran.NumAlbaran NOT IN (SELECT NRecogida FROM enlace ) ");
        
        $this->PrincipioTablaNormal();
        
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
                        $fecha_pagado = $a_res2['fecha_pagado'];

                        if($boolSN == 0){
                            $contenedor = $a_res2['Capacidad'];
                            $EmpCont = $a_res2['Empresa'];
                        }
                        else{
                            $contenedor = $a_res2['numCont'];
                            $EmpCont = $a_res2['Empresa'];
                        }
                    
                        $this->MostrarAux($conexion,$a_res['NumAlbaran'],$a_res['tipo'],$a_res['fecha'],$a_res['nomEmp'],$a_res['localizacion'],$contenedor,$EmpCont,$a_res['fecha_pagado'],$boolSN);
                    
                    }
                }

            }
            echo "</table>";
            echo "<p align='center'>$this->pagi_navegacion</p>";
        }
        else{
            echo "<tr><td><div class='alert alert-danger'>
                <strong> No hay lineas </strong>
            </div></td></tr>";
        }
    
        echo "</tbody>";
        echo "</table>";
    }

    function ActFecha($conexion,$fecha,$fechaAnt,$tipo,$Numalb){
        $fallo = false;

        $res = mysqli_query($conexion, "UPDATE albaran SET fecha = '".$fecha."' WHERE NumAlbaran = '".$Numalb."' ");
        if($res !== false){

            //actualizar enlace
            //cambiar la fecha, si tenía enlace, entonces tiene que cambiar los dias que estuvo en el enlace
            if((strcasecmp($tipo,'Entrega') == 0)){
                
                //buscamos el enlace
                $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$Numalb."'  ");
                if($comp!==false && $comp->num_rows > 0){
                    
                    //tiene enlace
                    $a_comp = mysqli_fetch_assoc($comp);
                    $comp2 = mysqli_query($conexion,"SELECT * FROM albaran NATURAL JOIN solicita WHERE NumAlbaran = '".$a_comp['NRecogida']."' ");
                    if($comp2!==false && $comp2->num_rows>0){
                        $a_comp2 =  mysqli_fetch_assoc($comp2);
                        $fecha2 = $a_comp2['fecha'];
                        $dias=$this->CalcularIntervalo2($fecha,$a_comp2['fecha_pagado'],$fecha2);
                        $upd = mysqli_query($conexion,"UPDATE enlace SET dias = '".$dias."' WHERE NEntrega = '".$Numalb."' ");
                        if(!$upd){
                            $fallo = true;
                            //deshacer
                            $res2 = mysqli_query($conexion, "UPDATE albaran SET fecha = '".$fechaAnt."' WHERE NumAlbaran = '".$Numalb."' ");
                        }
                    }
            
                }
            }
            else{
                $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NRecogida = '".$Numalb."' ");
                if($comp->num_rows > 0){
                    //tiene enlace
                    $a_comp =  mysqli_fetch_assoc($comp);
                    $comp2 = mysqli_query($conexion,"SELECT * FROM albaran NATURAL JOIN solicita WHERE NumAlbaran = '".$a_comp['NEntrega']."' ");
                    if($comp2!==false && $comp2->num_rows>0){
                        $a_comp2 =  mysqli_fetch_assoc($comp2);
                        $fecha2 = $a_comp2['fecha']; 
                        $dias=$this->CalcularIntervalo2($fecha2,$a_comp2['fecha_pagado'],$fecha);
                        $upd = mysqli_query($conexion,"UPDATE enlace SET dias = '".$dias."' WHERE NRecogida = '".$Numalb."' ");
                        if(!$upd){
                            $fallo = true;
                            $res2 = mysqli_query($conexion, "UPDATE albaran SET fecha = '".$fechaAnt."' WHERE NumAlbaran = '".$Numalb."' ");
                        }
                    }
                }
            }

        }

    }
    
    function ModificarNumAlbaran($conexion,$antiguo,$nuevo,$tipo){
        unset($_POST['ModNAlb']);
        unset($_POST['NAlb']); 
        unset($_POST['tipo']);

        $falloEn = false;
        $falloAlb = false;
        $enlace = false;

        if($nuevo != null){
            $comp_ini = mysqli_query($conexion,"SELECT * FROM albaran WHERE NumAlbaran = '".$nuevo."' ");
            if(!($comp_ini->num_rows > 0)){
                $res = mysqli_query($conexion, "UPDATE solicita SET NumAlbaran = '".$nuevo."' WHERE NumAlbaran = '".$antiguo."' ");
                if($res){
                    //actualizamos enlace
                    if((strcasecmp($tipo,'Entrega') == 0)){
                        //tipo entrega
                        $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$antiguo."' ");
                        if($comp!==false && $comp ->num_rows > 0){
                            //tiene enlace y era entrega
                            $enlace = true;
                            $comp_up = mysqli_query($conexion,"UPDATE enlace SET NEntrega = '".$nuevo."' WHERE NEntrega = '".$antiguo."' ");
                            if(!$comp_up){ //fallo
                                $falloEn = true;
                            }
                        }
                    }
                    else{
                        $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NRecogida = '".$antiguo."' ");
                        if($comp!==false && $comp ->num_rows > 0){
                            //tiene enlace y era recogida
                            $enlace = true;
                            $comp_up = mysqli_query($conexion,"UPDATE enlace SET NRecogida = '".$nuevo."' WHERE NRecogida = '".$antiguo."' ");
                            if(!$comp_up){ //fallo
                                $falloEn = true;
                            }
                        }
        
                    }
                    //se pudo actualiar solicita -> actualizamos albaran
                    if($falloEn == false){
                        //si no hubo fallo en enlace, actualizamos
                        $res4 = mysqli_query($conexion, "UPDATE albaran SET NumAlbaran = '".$nuevo."' WHERE NumAlbaran = '".$antiguo."' ");
                        if(!$res4){ //fallo
                            $falloAlb = true;
                        }
                    }
    
                    //se cambió el solicita, miremos si hubo algún fallo
                    if($falloEn == true){
                        //hubo fallo en enlace
                        $fallo_des = $this->DeshacerUpEnlace($conexion,$tipo,$antiguo,$nuevo);
                        if($fallo_des == true){
                            //cambiamos solicita
                            $des = mysqli_query($conexion, "UPDATE solicita SET NumAlbaran = '".$antiguo."' WHERE NumAlbaran = '".$nuevo."' ");
                            if(!$des){
                                //no se cambió
                                echo "<div class='alert alert-danger'>
                                    <strong>Error al actualizar</strong> Habrá incongruencias.
                                </div>";
                                //no cambiamos albaran porque se supone que no debería entrar.
                            }
                        }
                        else{
                            echo "<div class='alert alert-danger'>
                                <strong>Error al actualizar</strong> Todo volvió a la versión anterior.
                            </div>";
                        }
                    }
                    else{
                        if($falloAlb == true){
                            //fallo al cambiar albaran
                            if($enlace == true){
                                //se cambió enlace pero al dar error albarán debemos quitar todo.
                                $fallo_des = $this->DeshacerUpEnlace($conexion,$tipo,$antiguo,$nuevo);
                                if($fallo_des == true){
                                    //cambiamos solicita
                                    $des = mysqli_query($conexion, "UPDATE solicita SET NumAlbaran = '".$antiguo."' WHERE NumAlbaran = '".$nuevo."' ");
                                    if(!$des){
                                        //no se cambió
                                        echo "<div class='alert alert-danger'>
                                            <strong>Error al actualizar</strong> Habrá incongruencias.
                                        </div>";
                                        
                                    }
                                    else{
                                        $des2 = mysqli_query($conexion, "UPDATE albaran SET NumAlbaran = '".$antiguo."' WHERE NumAlbaran = '".$nuevo."' ");
                                        if(!$des2){
                                            echo "<div class='alert alert-danger'>
                                                <strong>Error al actualizar</strong> Habrá incongruencias.
                                            </div>";
                                        }
                                    }
                                }
                                else{
                                    echo "<div class='alert alert-danger'>
                                        <strong>Error al actualizar</strong> Todo volvió a la versión anterior.
                                    </div>";
                                }
                            }
                        }
                    }
    
    
                }
                else{
                    //no se pudo actualizar, no ha cambiado nada
                    echo "<div class='alert alert-danger'>
                        <strong>Error al actualizar.</strong>
                    </div>";
                }
            }
            else{
                echo "<div class='alert alert-danger'>
                    <strong>Ya hay un albaran con ese número.</strong>
                </div>";
            }
        }
        else{
            echo "<div class='alert alert-danger'>
                <strong>Parámetro vacío.</strong>
            </div>";
        }
    }
    
    //Ante un update de número de albarán fallido, se procede a estar en el anterior momento
    function DeshacerUpEnlace($conexion,$tipo,$antiguo,$nuevo){
        $fallo = false;
        if((strcasecmp($tipo,'Entrega') == 0)){
            //tipo entrega
            $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$nuevo."' ");
            if($comp!==false && $comp ->num_rows > 0){
                //tiene enlace y era entrega
                $comp_up = mysqli_query($conexion,"UPDATE enlace SET NEntrega = '".$antiguo."' WHERE NEntrega = '".$nuevo."' ");
                if(!$comp_up){ //fallo
                    $fallo = true;
                }
            }
        }
        else{
            $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NRecogida = '".$nuevo."' ");
            if($comp!==false && $comp->num_rows > 0){
                //tiene enlace y era recogida
                $comp_up = mysqli_query($conexion,"UPDATE enlace SET NRecogida = '".$antiguo."' WHERE NRecogida = '".$nuevo."' ");
                if(!$comp_up){ //fallo
                    $fallo = true;
                }
            }

        }
        return $fallo;
    }


    //TODO al recargar la página, borrar la info de post

    function ProcesoEnlazar($conexion,$NumAlb,$nuevoTipo){
    
        $comp = mysqli_query($conexion,"SELECT * FROM albaran NATURAL JOIN solicita WHERE NumAlbaran = '".$NumAlb."' ");
        if($comp!==false && $comp->num_rows > 0){
            $a_comp = mysqli_fetch_assoc($comp);
            
            $comp2 = false;
            $boolSN = strcasecmp('SN',$a_comp['tipoCont']);
            if( $boolSN == 0){
               
                $comp2 = mysqli_query($conexion,"SELECT Id,Capacidad,Empresa FROM contenedorsn INNER JOIN solicita 
                    ON contenedorsn.Id = solicita.IdCont WHERE solicita.IdCont = '".$a_comp['IdCont']."' and NumAlbaran = '".$NumAlb."' ");
            }
            else{
                
                $comp2 = mysqli_query($conexion,"SELECT Id,numCont,Empresa FROM contenedor INNER JOIN solicita 
                    ON contenedor.Id = solicita.IdCont WHERE solicita.IdCont = '".$a_comp['IdCont']."' and NumAlbaran = '".$NumAlb."' ");
            }
            if($comp2 !== false && $comp2->num_rows > 0){
                
                $a_comp2 = mysqli_fetch_assoc($comp2);
                if($boolSN == 0){
                    $cont = $a_comp2['Capacidad'];
                }
                else{
                    $cont = $a_comp2['numCont'];
                }

                
                $conseguido = $this->Enlazar($conexion,$NumAlb,$a_comp['localizacion'],$a_comp['nomEmp'],$a_comp2['Id'],$cont,$a_comp2['Empresa'],$boolSN,$a_comp['fecha'],null,$nuevoTipo);
            
                
                if($conseguido == true){
                    echo "<div class='alert alert-success'>
                    <strong> Vinculado </strong>
                    </div>";
                }
                else{
                    echo "<div class='alert alert-warning'>
                    <strong> No se pudo vincular </strong>
                    </div>";
                }

            }
        }

    }

    function ModificarTipo($conexion,$NumAlb,$nuevoTipo){ 
        unset($_POST['ModTipo']);
        unset($_POST['numAlb']);
        unset($_POST['tipo_nuevo']);
        $res = mysqli_query($conexion,"SELECT * FROM albaran NATURAL JOIN solicita WHERE NumAlbaran = '".$NumAlb."' ");
        if($res!==false && $res->num_rows > 0){
            $a_res = mysqli_fetch_assoc($res);
            if(strcasecmp($a_res['tipoCont'],'SN') == 0){
                if(strcasecmp($nuevoTipo,$a_res['tipo']) != 0){
                    //no es el mismo tipo
                    //Lo primero que debemos hacer es ver si tiene enlace, si hay -> borrramos. Luego buscamos nuevos enlaces
                    $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$NumAlb."' OR NRecogida = '".$NumAlb."' ");
                    if($comp!==false && $comp->num_rows > 0){
                        //borramos
                        $a_comp = mysqli_fetch_assoc($comp);
                        
                        $comp_del = mysqli_query($conexion,"DELETE FROM enlace WHERE NEntrega = '".$a_comp['NEntrega']."' ");
                            
                            if(!$comp_del){
                                echo "<tr><td><div class='alert alert-danger'>
                                    <strong> Error al intentar modificar los enlaces </strong> 
                                </div></td></tr>";
                            }
                            else{
                                //cambiamos
                                $res = mysqli_query($conexion,"UPDATE albaran SET tipo = '".$nuevoTipo."' WHERE NumAlbaran = '".$NumAlb."' ");
                                if($res){
                                    $this->ProcesoEnlazar($conexion,$NumAlb,$nuevoTipo);
                                }
                                else{
                                    $NEntrega = $a_comp['NEntrega'] ; $NRecogida = $a_comp['NRecogida'] ; $dias = $a_comp['dias']; $resi = $a_comp['residuo'];
                                    $err = mysqli_query($conexion,"INSERT INTO enlace (NEntrega,NRecogida,dias,residuo) VALUES ('$NEntrega','$NRecogida','$dias','$resi') ");
                                    if(!$err){
                                        echo "<tr><td><div class='alert alert-danger'>
                                            <strong> Error al intentar actualizar </strong><p>Puede haber incongruencias</p>
                                        </div></td></tr>";
                                    }
                                }
                            }
                    }
                    else{
                        //no tiene enlace
                        //cambiamos y vemos si casa
                        $res = mysqli_query($conexion,"UPDATE albaran SET tipo = '".$nuevoTipo."' WHERE NumAlbaran = '".$NumAlb."' ");
                        if($res){
                            
                            $this->ProcesoEnlazar($conexion,$NumAlb,$nuevoTipo);
                        }
                        else{
                            echo "<tr><td><div class='alert alert-danger'>
                                <strong> Error al intentar actualizar </strong><p>Puede haber incongruencias</p>
                            </div></td></tr>";
                        }
                            
                    }
    
                }
                else{
                    //no ha cambiado el tipo
                    echo "<tr><td><div class='alert alert-warning'>
                        <p>El tipo introducido es igual al que estaba</p>
                    </div></td></tr>";
                }
            }
            else{
                echo "<tr><td><div class='alert alert-warning'>
                    <p>No se puede cambiar el tipo a los que tienen contenedor enumerado</p>
                </div></td></tr>";
            }
            
        }
    }

    function ModificarFecha($conexion,$NumAlb,$fecha){
        unset($_POST['ModFecha']);
        unset($_POST['numAlb']);
        unset($_POST['fecha']);

        $fallo = false;
        $res = mysqli_query($conexion,"SELECT * FROM albaran WHERE NumAlbaran ='".$NumAlb."' ");
        if($res!==false && $res->num_rows > 0){
            $a_res =  mysqli_fetch_assoc($res);
            $fallo = $this->ActFecha($conexion,$fecha,$a_res['fecha'],$a_res['tipo'],$NumAlb);
        }
        if($fallo == true){
            echo "<tr><td><div class='alert alert-danger'>
                <strong> Error al intentar actualizar </strong> 
            </div></td></tr>";
        }
        else{
            echo "<tr><td><div class='alert alert-success'>
                <strong> Se actualizó correctamente </strong> 
            </div></td></tr>";
        }

    }
    
    function ModificarCliente($conexion,$NumAlb,$emp,$localizacion){
        unset($_POST['ModCli']);
        unset($_POST['numAlb']);
        unset($_POST['emp']);
        unset($_POST['local']);

        //ver si enlace -> romper enlace
        //modificar albaran
        $res = mysqli_query($conexion,"SELECT * FROM albaran WHERE NumAlbaran = '".$NumAlb."' ");
        if($res !== false && $res->num_rows > 0 ){
            $a_res = mysqli_fetch_assoc($res);
            $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$NumAlb."' OR NRecogida = '".$NumAlb."' ");
            if($comp !== false && $comp->num_rows > 0){

                //borramos
                $a_comp = mysqli_fetch_assoc($comp);
                $resi = $a_comp['residuo'];
                    
                $comp_del = mysqli_query($conexion,"DELETE FROM enlace WHERE NEntrega = '".$a_comp['NEntrega']."' ");
                    
                if(!$comp_del){
                    echo "<tr><td><div class='alert alert-danger'>
                        <strong> Error al intentar modificar los enlaces </strong> 
                    </div></td></tr>";
                }
                else{
                    //no error
                    //modificar albaran
                    $comp2 = mysqli_query($conexion,"SELECT * FROM obracivil WHERE nomEmp = '".$emp."' and localizacion = '".$localizacion."' ");
                    if($comp2->num_rows > 0){
   
                        $res_up = mysqli_query($conexion,"UPDATE albaran SET nomEmp = '".$emp."' , localizacion = '".$localizacion."' WHERE NumAlbaran = '".$NumAlb."' ");
                        if($res_up){
                            
                            $this->ProcesoEnlazar($conexion,$NumAlb,$a_res['tipo']);
                            
                        }
                        else{
                            echo "<tr><td><div class='alert alert-danger'>
                                <strong> Error al intentar modificar el cliente </strong> 
                            </div></td></tr>";
                        }
                    }
                    else{
                        echo "<tr><td><div class='alert alert-warning'>
                            <strong> Error: la localización no pertenece al cliente </strong> 
                        </div></td></tr>";
                    }

                }
                
            }
            else{
                //no hay enlace
                $comp2 = mysqli_query($conexion,"SELECT * FROM obracivil WHERE nomEmp = '".$emp."' and localizacion = '".$localizacion."' ");
                    if($comp2!==false && $comp2->num_rows > 0){
 
                        $res_up = mysqli_query($conexion,"UPDATE albaran SET nomEmp = '".$emp."', localizacion = '".$localizacion."' WHERE NumAlbaran = '".$NumAlb."' ");
                        if($res_up){
                            //¿se podrá enlazar?
                            $this->ProcesoEnlazar($conexion,$NumAlb,$a_res['tipo']);
                        }
                    }
                    else{
                        echo "<tr><td><div class='alert alert-warning'>
                            <strong> Error: la localización no pertenece al cliente </strong> 
                        </div></td></tr>"; 
                    }
            }
        }

    }
    
    /*
    // Actualiza el estado de un contenedor enumerado dependiendo de si alguien lo utiliza o no
    function ActualizarEstadoContenedorEnum($conexion,$NumAlb,$IdCont,$empCont,$fecha,$Entrega){
        //¿Tiene enlace? Bucamos Entregas posteriores a la mía sin enlace
        $conseguido =  false;

        $res=mysqli_query($conexion, "SELECT * FROM solicita NATURAL JOIN albaran
                WHERE solicita.NumAlbaran NOT IN
                (
                    SELECT NEntrega FROM enlace INNER JOIN solicita
                    ON enlace.NEntrega = solicita.NumAlbaran
                )
                and solicita.IdCont = '".$IdCont."' and solicita.EmpresaCont = '".$empCont."'  
                and albaran.tipo = 'Entrega' and albaran.NumAlbaran != '".$NumAlb."'
                and '".$fecha."' <= albaran.fecha
                ORDER BY fecha ASC LIMIT 1 
                ");
        
        if($res!==false){
            if($res->num_rows == 0){
                echo "ACN no hay";
                // No hay,cambiamos estado
                if($Entrega == 0){
                    $nuevoEstado = 'libre';
                }
                else{
                    // hay una entrega que se queda sin su recogida
                    $nuevoEstado = 'ocupado';
                }

                $res2 = mysqli_query($conexion,"UPDATE contenedor SET estado = '".$nuevoEstado."'  WHERE Id = '".$IdCont."' and Empresa = '".$empCont."' ");
                if($res2 !== false){
                    $conseguido = true;
                }
            }
        }
        return $conseguido;
    }*/

    //TODO: ModificarContenedor

   
    //Verá el contenedor que tiene actualmente, y quitará
    /*function ModificarContenedor($conexion,$NumAlb,$tipoCont,$cont,$contEmp,$tipoEnt){
        
        if($cont!=null && $contEmp != null){
            $comp = mysqli_query($conexion,"SELECT * FROM solicita NATURAL JOIN albaran WHERE albaran.NumAlbaran = '".$NumAlb."' ");
        
            if($comp !==  false && $comp->num_rows > 0){
                $a_comp = mysqli_fetch_assoc($comp);
                $boolSNAnt = strcmp ($a_comp['tipoCont'],'SN');
                $boolIguales = strcmp($a_comp['tipoCont'],$tipoCont);
                $boolSNActual = strcmp($tipoCont,'SN');
    
                $seguir = true;
                
                // Ver el contenedor 
                if($boolSNActual == 0)
                    $comp2 = mysqli_query($conexion,"SELECT Id,Empresa FROM contenedorsn WHERE '".$cont."' = Capacidad and UPPER(Empresa) LIKE UPPER('%".$contEmp."%') ");
                else
                    $comp2 = mysqli_query($conexion,"SELECT Id,Empresa FROM contenedor WHERE '".$cont."' = numCont and UPPER(Empresa) LIKE UPPER('%".$contEmp."%') ");

                if($comp2 !== false && $comp2->num_rows > 0 )
                    $a_comp2 = mysqli_fetch_assoc($comp2);

                if($boolIguales == 0){
                    //son iguales, vemos si es el mismo

                    if($a_comp['IdCont'] == $a_comp2['Id'] && $a_comp['EmpresaCont'] == $a_comp2['Empresa']){
                        $seguir = false;
                        //es el mismo
                        echo "<tr><td><div class='alert alert-warning'>
                            <strong>Es el mismo contenedor</strong> 
                        </div></td></tr>";
                    }
                    
                }
    
                if($boolSNAnt == 0 && $seguir != false ){
                    //era Sin Número
                    
                    if($tipoEnt == 0)
                        $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa+1 WHERE Empresa = '".$a_comp['EmpresaCont']."' and Id = '".$a_comp['IdCont']."' ");
                    else
                        $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa-1 WHERE Empresa = '".$a_comp['EmpresaCont']."' and Id = '".$a_comp['IdCont']."' ");

                    if($res2 !== false){
                        //seguimos 
                        $this->ModificarContenedorSN($conexion,$NumAlb,$a_comp['IdCont'],$a_comp['EmpresaCont'],$tipoEnt);
                          
                    }
                    else{
                        echo "<tr><td><div class='alert alert-danger'>
                            <strong>No se ha podido actualizar el contenedor</strong> 
                        </div></td></tr>";
                    }
                }
                else{
                    if($seguir != false && $boolSNAnt!=0){
                        
                        //era enumerado
                          //            Miramos ese contenedor.
                           // Si lo tiene una entrega sin enlace -> debe quedarse como ocupado
                            //Si los que lo tienen ya tiene enlace -> debe quedarse como libre 
                        
                        $conseguido = $this->ActualizarEstadoContenedorEnum($conexion,$NumAlb,$a_comp['IdCont'],$a_comp['EmpresaCont'],$a_comp['fecha'],$tipoEnt);
    
                        if($conseguido == true){
                            // Se la pasamos a la correspondiente
                            $this->ModificarContenedorEnum($conexion,$NumAlb,$a_comp['IdCont'],$a_comp['EmpresaCont'],$tipoEnt);
                        }
                        else{
                            echo "<tr><td><div class='alert alert-danger'>
                                <strong>No se ha podido actualizar el contenedor</strong> 
                                Puede que alguien esté usandolo.
                            </div></td></tr>";
                        }
                    }
    
                }
                //no se actualiza con los numerado a ocupado o libre
                if($comp2 !== false && $comp2->num_rows > 0){
                    // actualizamos

                    $res2 = mysqli_query($conexion,"UPDATE solicita SET IdCont='".$a_comp2['Id']."' ,EmpresaCont = '".$a_comp2['Empresa']."' ,tipoCont='".$tipoCont."' WHERE NumAlbaran = '".$NumAlb."' ");
                    
                    if($res2 !== false){
                        $conseguido2 = false;
                        if($tipoEnt == 0)
                            $tipo = 'Entrega';
                        else
                            $tipo = 'Recogida';


                        if($boolSNActual == 0){
                            if($tipoEnt == 0)
                                $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa-1 WHERE Empresa = '".$a_comp2['Empresa']."' and Id = '".$a_comp2['Id']."' ");
                            else
                                $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa+1 WHERE Empresa = '".$a_comp2['Empresa']."' and Id = '".$a_comp2['Id']."' ");
    
                            // Enlazar
                            $this->Enlazar($conexion,$NumAlb,$a_comp['localizacion'],$a_comp['nomEmp'],$a_comp2['Id'],$cont,$a_comp2['Empresa'],$boolSNActual,$a_comp['fecha'],null,$tipo);
                            
    
                        }
                        else{
                            if(strcasecmp($tipo,'Recogida') == 0){
                                //tipo recogida, buscamos su entrega y actualizamos
                                //Enlazar
                                $this->Enlazar($conexion,$NumAlb,$a_comp['localizacion'],$a_comp['nomEmp'],$a_comp2['Id'],$cont,$a_comp2['Empresa'],$boolSNActual,$a_comp['fecha'],null,$tipo);                                
                            }
                            else{
                                //tipo entrega actualizamos el conteendor
                                echo "cambiamos estado contenedor ";
                                echo $cont;
                                $ocup = 'ocupado';
                                $res2 = mysqli_query($conexion,"UPDATE contenedor SET estado='".$ocup."' WHERE numCont = '".$cont."' and Empresa = '".$a_comp2['Empresa']."' ");
                                if($res2 === false){
                                    echo "<div class='alert alert-warning'>
                                    <strong> El contenedor está en un estado inapropiado </strong>
                                    </div>";
                                }
                            }
                        }
                        
                        if( ($conseguido2 == false && $boolSNActual == 0) || ($boolSNActual !=0 && (strcasecmp($tipo,'Recogida') == 0) && $conseguido2 == false) ){
                            //TODO Ver en que casos hay que borrar
                            echo "<div class='alert alert-warning'>
                            <strong> No se pudo vincular </strong>
                            </div>";
                        }
        
                    }
                }


            }
        }

    
    }*/

   /* function ModificarContenedorEnum($conexion,$NumAlb,$Idcont,$ContEmp,$tipoEnt){
        //tenemos que ver que la empresa y el contenedor casen
        unset($_POST['ModCont']);
        echo "Modificar Enum ";
        echo $Idcont ;
        echo " "; 
        echo $ContEmp;
        $comp = mysqli_query($conexion,"SELECT * FROM contenedor WHERE Id = '".$Idcont."' and Empresa = '".$ContEmp."' ");
        if($comp !== false && $comp->num_rows > 0){
            //ver si enlace -> romper enlace
            $res = null;
            if($tipoEnt == 0){
                $res = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$NumAlb."' ");      
            }
            else{
                $res = mysqli_query($conexion,"SELECT * FROM enlace WHERE NRecogida = '".$NumAlb."' ");      
            }

            if($res !== false){
                if($res->num_rows > 0){
                    $a_res = mysqli_fetch_assoc($res);
                    //Quitar enlace
                    $recogida = $a_res['NRecogida'];
                    $res2 = mysqli_query($conexion,"DELETE FROM enlace WHERE NEntrega = '".$a_res['NEntrega']."' ");
                    if(!$res2){
                        echo "<tr><td><div class='alert alert-danger'>
                            <strong> Error al intentar modificar los enlaces </strong> 
                        </div></td></tr>";
                    }
                    else{
                        if($tipoEnt == 0){
                            //se queda una recogida suelta -> Eliminar y avisar
                            echo "<tr><td><div class='alert alert-warning'>
                                <strong> Se procede a eliminar la recogida enlazada </strong> ";
                            echo $recogida;
                            echo "</div></td></tr>";

                            $res3 = mysqli_query($conexion,"DELETE FROM solicita WHERE NumAlbaran = '".$recogida."' ");
                            $res4 = mysqli_query($conexion,"DELETE FROM albaran WHERE NumAlbaran = '".$recogida."' ");

                            if(!$res3 || ! $res4){
                                echo "<tr><td><div class='alert alert-danger'>
                                    <strong> Error al intentar borrar la recogida asociada </strong> 
                                </div></td></tr>";  
                            }

                            //No actualizamos porque ya lo hemos hecho antes

                        }
                    }
                    
                }
                else{
                
                    $a_comp = mysqli_fetch_assoc($comp);
    
                    $res_up = mysqli_query($conexion,"UPDATE solicita SET IdCont = '".$a_comp['Id']."' , EmpresaCont = '".$a_comp['Empresa']."' WHERE NumAlbaran = '".$NumAlb."' ");
                    
                    if($res_up){
                        if($tipoEnt == 0)
                            $tipo = 'Entrega';
                        else
                            $tipo = 'Recogida';
    
                        $this->ProcesoEnlazar($conexion,$NumAlb,$tipo);
                        
                        
                    }
                    else{
                        echo "<tr><td><div class='alert alert-danger'>
                            <strong> Error al intentar modificar el contenedor </strong> 
                        </div></td></tr>";
                        //reenlazar
                    }
    
                }
            }
            
            
        }
        else{
            echo"<tr><td><div class='alert alert-danger'>
                <strong> Error, no se encuentra el contenedor </strong> 
            </div></td></tr>";
        }
    
    }

    function ModificarContenedorSN($conexion,$NumAlb,$Idcont,$ContEmp,$tipoEnt){
        unset($_POST['ModCont']);
        $comp = mysqli_query($conexion,"SELECT * FROM contenedorsn WHERE Id = '".$Idcont."' and Empresa = '".$ContEmp."' ");
        if($comp !== false && $comp->num_rows > 0){
            echo " MODIFICAR SN ";
            //ver si enlace -> romper enlace
            $res = null;
            if($tipoEnt == 0){
                $res = mysqli_query($conexion,"SELECT * FROM enlace WHERE NEntrega = '".$NumAlb."' ");      
            }
            else{
                $res = mysqli_query($conexion,"SELECT * FROM enlace WHERE NRecogida = '".$NumAlb."' ");      
            }
            if($res !== false && $res->num_rows > 0){
                $a_res = mysqli_fetch_assoc($res);
                //Quitar enlace

                $res2 = mysqli_query($conexion,"DELETE FROM enlace WHERE NEntrega = '".$a_res['NEntrega']."' ");

                if(!$res2){
                    echo "<tr><td><div class='alert alert-danger'>
                        <strong> Error al intentar modificar los enlaces </strong> 
                    </div></td></tr>";
                }
                else{
                    echo " delete enlace ";
                    //$a_comp = mysqli_fetch_assoc($comp);

                    //$res_up = mysqli_query($conexion,"UPDATE solicita SET IdCont = '".$a_comp['Id']."' , EmpresaCont = '".$a_comp['Empresa']."' WHERE NumAlbaran = '".$NumAlb."' ");

                    //if($res_up){

                        if($tipoEnt == 0)
                            $tipo = 'Entrega';
                        else
                            $tipo = 'Recogida';
                        echo $tipo;
                        $this->ProcesoEnlazar($conexion,$NumAlb,$tipo);

                    //}
                    /*else{
                        echo "<tr><td><div class='alert alert-danger'>
                                <strong> Error al intentar modificar el cliente </strong> 
                        </div></td></tr>";
                        //reenlazar
                    // aqui va fin de comentarios }



                }
            }
                


        }
        else{
            echo"<tr><td><div class='alert alert-danger'>
                <strong> Error, no se encuentra el contenedor </strong> 
            </div></td></tr>";
        }
    }*/


    function ModResi($conexion,$NumAlb,$resi){
        //buscamos enlace y cambiamos residuo
        unset($_POST['ModResi']);
        unset($_POST['numAlb']);
        unset($_POST['resi']);
        $res = mysqli_query($conexion,"SELECT * FROM enlace WHERE '".$NumAlb."' = NRecogida");
        if($res->num_rows > 0){
            $res_up = mysqli_query($conexion,"UPDATE enlace SET residuo = '".$resi."' WHERE '".$NumAlb."' = NRecogida ");
            if($res_up){
                echo "<tr><td><div class='alert alert-success'>
                    <strong>Residuo modificado con éxito</strong> 
                </div></td></tr>";
            }
            else{
                echo "<tr><td><div class='alert alert-danger'>
                    <strong>Hubo un error al intentar modificarlo</strong> 
                </div></td></tr>";
            }
        }
        else{
            echo "<tr><td><div class='alert alert-danger'>
                <strong> Enlace de recogida no encontrado</strong> 
            </div></td></tr>";
        }
    }

    function ActualizarEstadoContenedor($conexion,$NumAlb,$enlace){
        
        if(is_null($enlace)){
            
            //Como está suelto el contenedor no se ha restablecido
            $comp = mysqli_query($conexion,"SELECT IdCont,EmpresaCont,tipoCont,tipo FROM solicita NATURAL JOIN albaran WHERE '".$NumAlb."' = NumAlbaran ");
            if($comp !== false && $comp->num_rows > 0){
                $a_comp = mysqli_fetch_assoc($comp);
                $boolEn = strcmp($a_comp['tipo'],'Entrega');
                $boolSN = strcmp($a_comp['tipoCont'],'SN');
                if($boolEn == 0){
                   
                    //es una entrega suelta
                    if($boolSN == 0){
                        $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa+1 WHERE Empresa = '".$a_comp['EmpresaCont']."' and Id = '".$a_comp['IdCont']."' ");
                    }
                    else{
                        
                        echo $a_comp['EmpresaCont'];
                        echo $a_comp['IdCont'];
                        $res2 = mysqli_query($conexion,"UPDATE contenedor SET estado='libre' WHERE Empresa = '".$a_comp['EmpresaCont']."' and Id = '".$a_comp['IdCont']."' ");
                    }
                    if($res2 === false){
                        echo "<tr><td><div class='alert alert-warning'>
                            <strong>No se ha podido cambiar el estado del contenedor</strong> 
                        </div></td></tr>";
                    }
                }
                else{
                    //es una recogida suelta
                    if($boolSN == 0){
                        $res2 = mysqli_query($conexion,"UPDATE contenedorsn SET EnCasa = EnCasa-1 WHERE Empresa = '".$a_comp['EmpresaCont']."' and Id = '".$a_comp['IdCont']."' ");
                    }
                    //con los otros contenedores no debería darse el caso de que haya una recogida suelta
                    if($res2 === false){
                        echo "<tr><td><div class='alert alert-warning'>
                            <strong>No se ha podido cambiar el estado del contenedor</strong> 
                        </div></td></tr>";
                    }
                }

            }
        }


    }

    function Borrar($conexion,$NumAlb){
        unset($_POST['BorrarMov']);
        $enlace = null;
        $comp = mysqli_query($conexion,"SELECT * FROM enlace WHERE '".$NumAlb."' = NEntrega OR '".$NumAlb."' = NRecogida ");
        if($comp!==false && $comp->num_rows > 0){
            //tiene enlace
            $a_comp = mysqli_fetch_assoc($comp);
            if($a_comp['NEntrega'] == $NumAlb){
                $enlace = $a_comp['NRecogida'];
            }
            else{
                $enlace = $a_comp['NEntrega'];
            }

        }

        $error = false;
        //Primero ponemos el contenedor bien
        $this->ActualizarEstadoContenedor($conexion,$NumAlb,$enlace);

        if(!is_null($enlace)){
            //borramos el enlace
            $res = mysqli_query($conexion,"DELETE FROM enlace WHERE '".$NumAlb."' = NEntrega OR '".$NumAlb."' = NRecogida ");
            if($res !== false){
                $res2 = mysqli_query($conexion,"DELETE FROM solicita WHERE '".$NumAlb."' = NumAlbaran ");
                $res3 = mysqli_query($conexion,"DELETE FROM solicita WHERE '".$enlace."' = NumAlbaran ");

                if(!$res2 || !$res3){
                    //error
                    $error = true;
                }
                else{
                    $res4 = mysqli_query($conexion,"DELETE FROM albaran WHERE '".$NumAlb."' = NumAlbaran ");
                    $res5 = mysqli_query($conexion,"DELETE FROM albaran WHERE '".$enlace."' = NumAlbaran ");
                }

                if(!$res4 || !$res5){
                    //error
                    $error = true;
                }

            }
            else{
                //ERROR
                $error = true;
            }
        }
        else{
            $res = mysqli_query($conexion,"DELETE FROM solicita WHERE '".$NumAlb."' = NumAlbaran ");
            if(!$res){
                //error
                $error = true;
            }
            else{
                $res2 = mysqli_query($conexion,"DELETE FROM albaran WHERE '".$NumAlb."' = NumAlbaran ");
                if(!$res2){
                    $error = true;
                }
            }
        }

        if($error == true){
            echo "<tr><td><div class='alert alert-danger'>
                <strong>Hubo un error al intentar borrarlo</strong> 
            </div></td></tr>";
        }
        else{
            echo "<tr><td><div class='alert alert-success'>
                <strong>Borrado con éxito</strong> 
            </div></td></tr>";
        }
    }
    


}

    $p_mov = new panel_Mov($conexion);
    include "modules/conf_movimientos.php";


    
    if(isset($_POST['BorrarMov'])){
        //borrar

        $p_mov->Borrar($conexion,$_POST['numAlb']);

    }
    else{
            //modificar
            if(isset($_POST['ModNAlb'])){
                $p_mov->ModificarNumAlbaran($conexion,$_POST['numAlb'],$_POST['NAlb'],$_POST['tipo']);
    
            }
            else{
                if(isset($_POST['ModTipo'])){
                    $p_mov->ModificarTipo($conexion,$_POST['numAlb'],$_POST['tipo_nuevo']);
                }
                else{
                    if(isset($_POST['ModFecha'])){
                            $p_mov->ModificarFecha($conexion,$_POST['numAlb'],$_POST['fecha']);
                    }
                    else{
                        if(isset($_POST['ModCli'])){
                            $p_mov->ModificarCliente($conexion,$_POST['numAlb'],$_POST['emp'],$_POST['local']);
                        }
                        else{
                            
                            if(isset($_POST['ModResi'])){
                                $p_mov->ModResi($conexion,$_POST['numAlb'],$_POST['resi']);
                            }
                            
                        }
                    }
                }
            }

    }

    if(isset($_POST['Pay']) && isset($_POST['NumPay'])){
        $p_mov->Pagar($conexion,$_POST['NumPay']);
    }




    //para ver
    if(isset($_POST['BuscarFecha']) && isset($_POST['Bfecha']) ){
        $p_mov->BuscarFecha($conexion,$_POST['Bfecha']);
    }
    else{
        if(isset($_POST['BuscarNum']) && isset($_POST['Bnum']) ){
            $p_mov->BuscarAlbaran($conexion,$_POST['Bnum']);
        }
        else{
            if( isset($_POST['BuscarClnt']) && isset($_POST['BCln']) ){
                $p_mov->BuscarCliente($conexion,$_POST['BCln']);
            }
            else{
                if(isset($_POST['BuscarLim']) && isset($_POST['Dias'])){
                    $p_mov->BuscarLimiteSuperado($conexion,$_POST['Dias']);
                }
                else{
                    if(isset($_POST['BuscarEN'])){
                        $p_mov->BuscarSinEnlace($conexion);
                    }
                    else{
                        $p_mov->MostrarMovimientos($conexion);
                    }
                    
                }
                
            }
            
        }
        
    }



?>
</div>
</body>
</html>
</body>

