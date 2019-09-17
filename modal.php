<html>
<head>
    <script src="js/jquery.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <title>Atención</title>
    <meta charset="utf-8">
</head>

<?php 
    $fecha=$_POST['fecha_ini'];
    $fecha_fin=$_POST['fecha_fin'] 
?>

  
    <div class="Recuadro" >
        <div id="Recuadrohead">
        <h3>Atención</h3>
        </div>
        <div id="Recuadrobody">
            <h4>¿Estás seguro de borrar?</h4>
        
            <form class="form-inline" action="imprimir.php" method="post">
                <input type="hidden" id="fecha_ini" name="fecha_ini" value=<?php echo $fecha ?> >
                <input type="hidden" id="fecha_fin" name="fecha_fin" value=<?php echo $fecha_fin ?> >

                <button type="submit" id="BorrarAlb" name="BorrarAlb" class="btn btn-danger">Aceptar</button>
                
                <button type="submit" class="btn btn-success">Cancelar</button>
              
            </form>

        </div>
    </div>


</html>