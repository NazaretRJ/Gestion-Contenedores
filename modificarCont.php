
<head>
    <script src="../js/jquery.js"></script>
    <script src="../js/ModCont.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <title>Modificar Contenedor</title>
    <meta charset="utf-8">
</head>
<?php 
    $num=$_GET['numCont'];
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
                <select class="form-control" id="eleccion" name="eleccion" onChange="mostrarCont(this.value);">
                    <option value="iddle">Elige una opción</option>
                    <option value="Numero">Número</option>
                    <option value="empresa">Empresa</option>
                    <option value="tamaño">Tamaño</option>
                    <option value="estado">Estado</option>

                </select>
        </div>
        </form>
    <div class="formularios">
        <div id="Numero" style="display: none;">
            <form action="../contenedores.php" method="post">
                <div class="form-group">
                    <label for="Numero"><strong> Número del contenedor </strong></label>
                    <input type="text" class="form-control" id="Numero" placeholder="Número" name="Numero">
                    <input type="hidden" id="numCont" name="numCont" value=<?php echo $num ?> >
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModNumCont" name="ModNumCont" value="Modificar" />
                </div>
            </form>
        </div>

        <div id="empresa" style="display: none;">
            <form action="../contenedores.php" method="post">
                <div class="form-group">
                    <label for="empresa"><strong> Empresa del contenedor </strong></label>
                    <input type="text" class="form-control" id="empNueva" placeholder="Empresa" name="empNueva">
                    <input type="hidden" id="numCont" name="numCont" value=<?php echo $num ?> >
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModEmp" name="ModEmp" value="Modificar" />
                </div>
            </form>
        </div>

        <div id="tamaño" style="display: none;">
            <form action="../contenedores.php" method="post">
                <div class="form-group">
                    <label for="tamaño"><strong>Tamaño</strong></label>
                    <input type="text" class="form-control" id="tam" placeholder="Tamaño" name="tam">
                    <input type="hidden" id="numCont" name="numCont" value=<?php echo $num ?> >
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModTam" name="ModTam" value="Modificar" />
                </div>
            </form>
        </div>


        <div id="estado" style="display: none;">
            <form action="../contenedores.php" method="post">
            <div class="form-group">
                <label for="estado" ><strong>Estado</strong></label>
                    <select class="form-control" id="estado" name="estado">
                        <option>libre</option>
                        <option>ocupado</option>
                        <option>baja</option>
                    </select>
                    <input type="hidden" id="numCont" name="numCont" value=<?php echo $num ?> >
                    <input type="hidden" id="emp" name="emp" value=<?php echo $emp ?> >
                    <input type="submit" id="ModEst" name="ModEst" value="Modificar" />

            </div>
            </form>
        </div>
    </div>
</div>