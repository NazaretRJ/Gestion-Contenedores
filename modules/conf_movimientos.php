<html>
<div class= "panel panel-primary" >
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Buscar Movimiento</b></div>
    
    <div class="panel-body">
        <div class= "form_mov">
        <form class="form-inline" method="post" action="./#">
        <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="opcion" style="margin-right: 5px"><strong>Buscar por</strong></label>
                <select class="form-control" id="opcion" name="opcion" onChange="MostrarBuscar(this.value);">
                    <option value="iddle">Elija una opción</option>
                    <option value="FC">Fecha completa</option>
                    <option value="NA">Número de Albarán</option>
                    <option value="Cli">Cliente</option>
                    <option value="LS">Limite superado</option>
                    <option value="NE">No enlazado</option>
                </select>
        </div>

        <!--Aquí van todos los formularios -->
        <div class="BuscarOcultos">
        <div id="Fecha" name="Fecha"style="display: none;">
            <form class='form-inline' method='post' action='index.php'>
                <div class='form-group'>
                    <label for='Bfecha'><strong>Fecha</strong></label>
                    <input type='date' class='form-control' id='Bfecha' placeholder='Fecha' name='Bfecha'>
                
                    <button type='submit' class='btn btn-primary' name='BuscarFecha'>Buscar</button>
                </div>
            </form>
        </div>

        <div id="NumAlb" style="display: none;">
            <form class='form-inline' method='post' action='index.php'>
                <div class='form-group'>
                    <label for='Bnum' ><strong>Número de Albarán</strong></label>
                    <input type='text' class='form-control' id='Bnum' placeholder='Número de Albarán' name='Bnum'>
                    <button type='submit' class='btn btn-primary' name='BuscarNum'>Buscar</button>
                </div>
            </form>
        </div>

        <div id="Cliente" style="display: none;">
            <form class='form-inline' method='post' action='index.php'>
                <div class='form-group'>
                    <label for='BCln' ><strong>Cliente</strong></label>
                    <select class="form-control" id="BCln" name="BCln">
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
                    <button type='submit' class='btn btn-primary' name='BuscarClnt'>Buscar</button>
                </div>
            </form>
        </div>

        <div id="Limite" name="Limite" style="display: none;">
            <form class='form-inline' method='post' action='index.php'>
                <div class='form-group' >
                    <label for='Dias'><strong>Días</strong></label>
                        <select class='form-control' id='Dias' name='Dias' style="margin-left:10px;">
                            <option>15</option>
                            <option>30</option>
                        </select>
                    <button type='submit' class='btn btn-primary' name='BuscarLim'>Buscar</button>
                </div>
            </form>
        </div>

        <div id="Enlace" name="Enlace" style="display: none;">
            <form class='form-inline' method='post' action='index.php'>
                <button type='submit' class='btn btn-primary' name='BuscarEN' id='BuscarEN' style='display: none;'>Buscar</button>
            </form>
        </form>
    </div>
    </div>

</div>

<div class= "panel panel-primary">
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Añadir Movimiento</b></div>
    
    <div class= "panel-body">
        <div class= "form_mov">
        <form class="form-inline" method="post" action="index.php">
            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="NAlb" style="margin-right: 5px"><strong> Número Albaran </strong></label>
                <input type="text" class="form-control" id="NAlb" placeholder="Número Albarán" name="NAlb">
            </div>

            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="fecha" style="margin-right: 5px"><strong>Fecha</strong></label>
                <input type="date" class="form-control" id="fecha" placeholder="Fecha" name="fecha">
            </div>

            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="tipo" style="margin-right: 5px"><strong>Tipo</strong></label>
                <select class="form-control" id="tipo" name="tipo" onChange="MostrarResiduo(this.value);">
                    <option value="Entrega">Entrega</option>
                    <option value="Recogida">Recogida</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-left: 15px; margin-top:5px;display:none;" name="CasillaResiduo" id="CasillaResiduo">
                <label for="Resi" style="margin-right: 5px"><strong> Residuo </strong></label>
                <input type="text" class="form-control" id="Resi" placeholder="Residuo" name="Resi" style="width:150px;">
            </div>

            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="emp" style="margin-right: 5px"><strong>Cliente</strong></label>
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
            </div>
      
            <div class="form-group" style="margin-left: 15px; margin-top:5px;" name="CasillaObra" id="CasillaObra">
                <label for="local" style="margin-right: 5px"><strong>Obra</strong></label>
                <select class="form-control" id="local" name="local">
            <?php
                //vamos a buscar las obras
                //TODO: hacer con las obras solo del cliente ya seleccionado
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
            </div>
            
            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="tipoCont" style="margin-right: 5px"><strong>Tipo de Contenedor</strong></label>
                <select class="form-control" id="tipoCont" name="tipoCont">
                    <option value="SN">Sin Numero</option>
                    <option value="Enumerado">Enumerado</option>
                </select>
            </div>

            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="cont" style="margin-right: 5px"><strong> Contenedor </strong></label>
                <input type="text" class="form-control" id="cont" placeholder="Número Contenedor" name="cont">
            </div>

            <div class="form-group" style="margin-left: 15px; margin-top:5px;">
                <label for="ContEmp" style="margin-right: 5px"><strong>Empresa propietaria</strong></label>
                <input type="text" class="form-control" id="ContEmp" placeholder="Propietaria del contenedor" name="ContEmp">
            </div>

            <button type="submit"  class="btn btn-primary" name="anadirMov" style="margin-left: 10px; margin-top:10px; margin-inline:auto; ">Añadir Movimiento</button>
        </form>
        </div>
    </div>

</div>
<?php

    if(isset($_POST['anadirMov'])){
        $p_mov-> AddMovimiento($conexion,$_POST['NAlb'],$_POST['fecha'],$_POST['tipo'],$_POST['local'],$_POST['emp'],$_POST['tipoCont'],$_POST['cont'],$_POST['ContEmp'],$_POST['Resi']);
    }


?>

</html>