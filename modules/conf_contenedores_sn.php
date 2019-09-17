<html>

<!--Nuevo contenedor-->
<div class= "panel panel-primary">
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Nuevo Contenedor</b>
</div>
    
    <div class= "panel-body">
        <div class= "form_cont">
        <form class="form-inline" method="post" action="contenedoressn.php">
            <div class="form-group" style="margin-left: 15px">
                <label for="num" style="margin-right: 5px"><strong> Capacidad </strong></label>
                <input type="text" class="form-control" id="Cap" placeholder="Capacidad del contenedor" name="Cap">
            </div>

            <div class="form-group" style="margin-left: 15px">
                <label for="emp" style="margin-right: 5px"><strong>Empresa propietaria</strong></label>
                <input type="text" class="form-control" id="Nemp" placeholder="Empresa propietaria" name="Nemp">
            </div>

            <div class="form-group" style="margin-left: 15px">
                <label for="tam" style="margin-right: 5px"><strong>En Stock</strong></label>
                <input type="text" class="form-control" id="NCasa" placeholder="Numero de contenedores" name="NCasa">
            </div>

            <button type="submit" class="btn btn-primary" name="anadirContSN" id="anadirContSN" style="margin-left: 10px">Añadir Contenedor</button>
        </form>
        </div>
    </div>
 
</html>