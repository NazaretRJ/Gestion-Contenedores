<script>

    function Imprimir(){
        window.print();
    }


</script>



<div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Imprimir albaranes enlazados</b></div>

<div class= "panel-body">

    <form class="form-inline" method="post" action="./modules/imprimirDatos.php"  target="_blank">
    <div class="form-group"style="margin-left: 15px; margin-top:5px;">
        <label for="fecha_ini" style="margin-right: 5px"><strong>Fecha Inicio</strong></label>
        <input type="date" class="form-control" id="fecha_ini"  style="margin-right: 5px" name="fecha_ini">

        <label for="fecha_fin" style="margin-right: 5px"><strong>Fecha Fin</strong></label>
        <input type="date" class="form-control" id="fecha_fin"  style="margin-right: 5px"  name="fecha_fin">

        <button type="submit"  style="margin-right: 5px" class="btn btn-primary" name="ImpMov">Mostrar Movimientos</button>
    </div>
    </form>

</div>


<div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Borrar albaranes enlazados</b></div>

<div class= "panel-body">
    <form class="form-inline" method="post" action="modal.php">
    <div class="form-group" style="margin-left: 15px; margin-top:5px;">
        <label for="fecha_ini" style="margin-right: 5px"><strong>Fecha Inicio</strong></label>
        <input type="date" class="form-control" id="fecha_ini" placeholder="Inicio" name="fecha_ini">

        <label for="fecha_fin" style="margin-right: 5px;margin-left: 10px;"><strong>Fecha Fin</strong></label>
        <input type="date" class="form-control" id="fecha_fin" placeholder="Fin" name="fecha_fin">

        <button type="submit"  class="btn btn-danger" name="DelMov" style="margin-left: 10px; ">Borrar Movimientos</button>
    </div>
    </form>

</div>

