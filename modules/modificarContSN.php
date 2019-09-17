<head>
    <script src="../js/jquery.js"></script>
    <script src="../js/ModContSN.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <title>Modificar Contenedor</title>
    <meta charset="utf-8">
</head>
<?php 
    $capacidad=$_GET['Capacidad'];
    $emp=$_GET['emp'];
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
                <select class="form-control" id="eleccion" name="eleccion" onChange="mostrarContSN(this.value);">
                    <option value="iddle">Elige una opción</option>
                    <option value="Capacidad">Capacidad</option>
                    <option value="empresa">Empresa</option>
                    <option value="stock">Contenedores en stock</option>

                </select>
        </div>
        </form>
    <div class="formularios">
        <div id="Capacidad" style="display: none;">
            <form action="../contenedoressn.php" method="post">
                <div class="form-group">
                    <label for="Capacidad"><strong> Capacidad del contenedor </strong></label>
                    <input type="text" class="form-control" id="CapNueva" placeholder="Capacidad" name="CapNueva">
                    <input type="hidden" id="Cap" name="Cap" value=<?php echo $capacidad ?> >
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModCap" name="ModCap" value="Modificar" />
                </div>
            </form>
        </div>

        <div id="empresa" style="display: none;">
            <form action="../contenedoressn.php" method="post">
                <div class="form-group">
                    <label for="empresa"><strong> Empresa del contenedor </strong></label>
                    <input type="text" class="form-control" id="empNueva" placeholder="Empresa" name="empNueva">
                    <input type="hidden" id="Cap" name="Cap" value=<?php echo $capacidad ?> >                    
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModEmp" name="ModEmp" value="Modificar" />
                </div>
            </form>
        </div>

        <div id="stock" style="display: none;">
            <form action="../contenedoressn.php" method="post">
                <div class="form-group">
                    <label for="stock"><strong>Stock</strong></label>
                    <input type="text" class="form-control" id="stock" placeholder="Contenedores disponibles" name="stock">
                    <input type="hidden" id="Cap" name="Cap" value=<?php echo $capacidad ?> >
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModStock" name="ModStock" value="Modificar" />
                </div>
            </form>
        </div>

    </div>
</div>