
<head>
    <script src="../js/jquery.js"></script>
    <script src="../js/ModMovForm.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <title>Modificar Movimiento</title>
    <meta charset="utf-8">
</head>
<?php 
    $num=$_GET['num'];
    $tipoEnt=$_GET['tipo'];
    include("conexion.php");
?>

<html>
<body>
<div class= "panel panel-primary">
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center"><b style="color: #ffffff"> Modificar Movimiento</b>
    </div>
    
    <div class= "panel-body" style="text-align:center">
        
        <h1 style="color:grey"><b>¿Qué quieres modificar?</b></h1>
        
        <form class="form-inline" action="./#" method="post">
        <div class="form-group" style="margin-left:35%;margin-top:15px;">
                <label for="eleccion" style="margin-right: 5px"><strong>Opciones</strong></label>
                <select class="form-control" id="eleccion" name="eleccion" onChange="mostrar(this.value);">
                    <option value="iddle">Elige una opción</option>
                    <option value="NumAlb">Número Albarán</option>
                    <option value="tipo">Tipo</option>
                    <option value="fecha">Fecha</option>
                    <option value="cliente">Cliente y obra</option>
                <!-- <option value="contenedor">Contenedor</option>-->
                    <!--If tipo == entrega habilitamos opcion de resi-->
                    <?php
                        if($tipoEnt != 0){
                            echo "<option value='residuo'>Residuo</option>";
                        }
                    ?>
                </select>
        </div>
        </form>
    <div class="formularios">
        <div id="NumAlb" style="display: none;">
            <form action="../index.php" method="post">
                <div class="form-group">
                    <label for="NAlb"><strong> Número Albaran </strong></label>
                    <input type="text" class="form-control" id="NAlb" placeholder="Número Albarán" name="NAlb">
                    <input type="hidden" id="numAlb" name="numAlb" value=<?php echo $num ?> >
                    <input type="hidden" id="tipo" name="tipo" value=<?php echo $tipoEnt ?> >
                    <input type="submit" id="ModNAlb" name="ModNAlb" value="Modificar" />
                </div>
            </form>
        </div>

        <div id="tipo_nuevo" style="display: none;">
            <form action="../index.php" method="post">
            <div class="form-group">
                <label for="tipo_nuevo" ><strong>Tipo</strong></label>
                    <select class="form-control" id="tipo_nuevo" name="tipo_nuevo">
                        <option>Entrega</option>
                        <option>Recogida</option>
                    </select>
                    <input type="hidden" id="numAlb" name="numAlb" value=<?php echo $num ?> >
                    <input type="submit" id="ModTipo" name="ModTipo" value="Modificar" />

            </div>
            </form>
        </div>

        <div id="fecha" style="display: none;">
            <form action="../index.php" method="post">
                <div class="form-group">
                    <label for="fecha"><strong>Fecha</strong></label>
                    <input type="date" class="form-control" id="fecha" placeholder="Fecha" name="fecha">
                    <input type="hidden" id="numAlb" name="numAlb" value=<?php echo $num ?> >
                    <input type="submit" id="ModFecha" name="ModFecha" value="Modificar" />

                </div>
            </form>
        </div>

        <div id="cliente" style="display: none;">
            <form action="../index.php" method="post">
            <div class="form-group">
                <label for="emp"><strong>Cliente</strong></label>
                    <select class="form-control" id="emp" name="emp">
                        <?php
                            //vamos a buscar las empresas
                            $res = mysqli_query($conexion,"SELECT nomEmp FROM cliente");
                            if($res->num_rows > 0){
                                while($a_res =  mysqli_fetch_assoc($res)){
                                    echo "<option>";
                                    echo $a_res['nomEmp'];
                                   echo "</option>";
                                }
                            }
                        ?>
                    </select>
                <label for="local"><strong>Obra</strong></label>
                    <select class="form-control" id="local" name="local">
                        <?php
                            //vamos a buscar las obras
                            $res = mysqli_query($conexion,"SELECT localizacion FROM obracivil");
                            if($res->num_rows > 0){
                                while($a_res =  mysqli_fetch_assoc($res)){
                                    echo "<option>";
                                    echo $a_res['localizacion'];
                                    echo "</option>";
                                }
                            }
                        ?>
                    </select>
                <input type="hidden" id="numAlb" name="numAlb" value=<?php echo $num ?> >
                <input type="submit" name="ModCli" id="ModCli" value="Modificar" />
            </div>
            </form>
        </div>

        <!--contenedor
        <div id="contenedor" style="display: none;">
            <form action="../index.php" method="post">
                <div class="form-group">
                            
                    <label for="tipoCont"><strong>Tipo de Contenedor</strong></label>
                    <select class="form-control" id="tipoCont" name="tipoCont">
                        <option value="SN">Sin Numero</option>
                        <option value="Enumerado">Enumerado</option>
                    </select>

                    <label for="cont"><strong> Contenedor </strong></label>
                    <input type="text" class="form-control" id="cont" placeholder="Contenedor" name="cont">
                
                    <label for="ContEmp"><strong>Empresa propietaria</strong></label>
                    <input type="text" class="form-control" id="ContEmp" placeholder="Propietaria del contenedor" name="ContEmp">
                
                    <input type="hidden" id="numAlb" name="numAlb" value=<?php echo $num ?> >
                    <input type="hidden" id="tipo" name="tipo" value=<?php echo $tipoEnt ?> >
                    <input type="submit" id="ModCont" name="ModCont" value="Modificar" />
                
                </div>
                    
            </form>
        </div>
        -->
        
     <!--Residuo-->
        <div id="residuo" style="display: none;">
            <form action="../index.php" method="post">
                <div class="form-group">
                    <label for="cont"><strong> Residuo</strong></label>
                    <input type="text" class="form-control" id="resi" placeholder="Residuo" name="resi">

                    <input type="hidden" id="numAlb" name="numAlb" value=<?php echo $num ?> >
                    <input type="submit" id="ModResi" name="ModResi" value="Modificar" />
                
                </div>
                    
            </form>
        </div>

       
        
    </div><!--fin div formularios-->

</div>
</body>
</html>
<!--TODO CAMBIAR EL ENviar POR DIFERENTES MOV-->