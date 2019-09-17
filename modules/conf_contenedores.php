<html>

<div class= "panel panel-primary">
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Buscar Contenedor</b>
</div>
    
    <div class= "panel-body">
        <div class= "form_cont_B">
        <form class="form-inline" method="post" action="contenedores.php">
            <div class="form-group" style="margin-left: 15px">
                <label for="Bnum" style="margin-right: 5px"><strong> Número </strong></label>
                <input type="text" class="form-control" id="Bnum" placeholder="Número de contenedor" name="Bnum">
            </div>

            <div class="form-group" style="margin-left: 15px">
                <label for="Bemp" style="margin-right: 5px"><strong>Empresa propietaria</strong></label>
                <input type="text" class="form-control" id="Bemp" placeholder="Empresa propietaria" name="Bemp">
            </div>


            <button type="submit" class="btn btn-primary" name="BCont" style="margin-left: 10px">Buscar</button>
        </form>
        </div>
    </div>

<!--Nuevo contenedor-->
<div class= "panel panel-primary">
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Nuevo Contenedor</b>
</div>
    
    <div class= "panel-body">
        <div class= "form_cont">
        <form class="form-inline" method="post" action="contenedores.php">
            <div class="form-group" style="margin-left: 15px">
                <label for="num" style="margin-right: 5px"><strong> Número </strong></label>
                <input type="text" class="form-control" id="Nnum" placeholder="Número de contenedor" name="Nnum">
            </div>

            <div class="form-group" style="margin-left: 15px">
                <label for="emp" style="margin-right: 5px"><strong>Empresa propietaria</strong></label>
                <input type="text" class="form-control" id="Nemp" placeholder="Empresa propietaria" name="Nemp">
            </div>

            <div class="form-group" style="margin-left: 15px">
                <label for="tam" style="margin-right: 5px"><strong> Tamaño </strong></label>
                <input type="text" class="form-control" id="Ntam" placeholder="Tamaño del contenedor" name="Ntam">
            </div>

            <button type="submit" class="btn btn-primary" name="anadirCont" style="margin-left: 10px">Añadir Contenedor</button>
        </form>
        </div>
    </div>
 
</html>

