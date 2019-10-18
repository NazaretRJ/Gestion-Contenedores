<html>



<div class= "panel panel-primary">
    <div class= "panel-heading" style="background-color:#0f31f0;text-align: center;"><b style="color: #ffffff">Añadir Cliente</b>
</div>
    
    <div class= "panel-body">
        <div class= "form_cln">
        
        <form class="form-inline" method="post" action="clientes.php">
            <div class="form-group" style="margin-left: 15%;">
                <label for="aclnt" style="margin-left: 10px;"><strong>Nombre del cliente</strong></label>
                <input type="text" class="form-control" id="aclnt" placeholder="Nombre del cliente" name="aclnt" style="margin-left: 10px;">
                
                <label for="atlf" style="margin-left: 10px;"><strong>Teléfono</strong></label>
                <input type="number" class="form-control" id="atlf" placeholder="Telefono" name="atlf" style="margin-left: 10px;">
            
            </div>

            <button type="submit" class="btn btn-primary" name="anadircliente" style="margin-left: 10px">Añadir Cliente</button>
        
        </form>
        
        </div>
    </div>

</html>

<?php

    if(isset($_GET['id'])){
        //modificar
        echo "<h2 style='text-align:center;'> Cliente: ".$_GET['id']." </h2>";
        
        if(isset($_GET['tlf']) ){
            echo "<h3 style='text-align:center;'>".$_GET['tlf']."</h3>";
        }
        
        echo "<div class= 'panel panel-primary'>";

        echo "<div class='panel-heading' style='background-color:#a8192f;text-align: center;'><b style='color: #ffffff'>Modificar Cliente</b>
            </div>";
        
        echo "<div class= 'panel-body'>";
        echo "<div class= 'form_cln'>";

            echo "<form class='form-inline' method='post' action='clientes.php'>";
                echo "<div class='form-group' style='margin-left: 30%;' >";
                    echo "<label for='aclnt' style='margin-left: 10px;'><strong>Nombre del Cliente</strong></label>";
                    echo "<input type='text' class='form-control' id='nmCln' placeholder='Nombre del Cliente' name='nmCln' style='margin-left: 10px;'>";
                    
                    echo "<input type='hidden' name='idEmp' value='".$_GET['id']."'>";
                echo "</div>";

                echo "<button type='submit' class='btn btn-primary' name='ModClnt' style='margin-left: 10px'>Modificar Cliente</button>";
    
            echo "</form>";

            echo "<form class='form-inline' method='post' action='clientes.php'>";
                echo "<div class='form-group' style='margin-left: 30%;' >";
                    echo "<label for='tlf' style='margin-left: 10px;'><strong>Teléfono</strong></label>";
                    echo "<input type='number' class='form-control' id='tlf' placeholder='Teléfono' name='tlf' style='margin-left: 10px;'>";

                    echo "<input type='hidden' name='idEmp' value='".$_GET['id']."'>";
            echo "</div>";

            echo "<button type='submit' class='btn btn-primary' name='ModTlfClnt' style='margin-left: 10px'>Modificar teléfono</button>";

            echo "</form>";

    
        echo "</div>";
        echo "</div>";
        echo "</div>";

        //añadir obra
        echo "<div class= 'panel panel-primary'>";

        echo "<div class='panel-heading' style='background-color:#a8192f;text-align: center;'><b style='color: #ffffff'>Añadir Obra</b>
            </div>";
            
        echo "<div class= 'panel-body'>";
        echo "<div class= 'form_cln'>";

            echo "<form class='form-inline' method='post' action='clientes.php'>";
                echo "<div class='form-group' style='margin-left: 30%;' >";
                    echo "<label for='aclnt' style='margin-left: 10px;'><strong>Nombre de la Obra</strong></label>";
                    echo "<input type='text' class='form-control' id='nmObra' placeholder='Nombre de la obra' name='nmObra' style='margin-left: 10px;'>";
                    echo "<input type='hidden' name='idEmp' value='".$_GET['id']."'>";
                echo "</div>";

                echo "<button type='submit' class='btn btn-primary' name='anadirObra' style='margin-left: 10px'>Añadir Obra</button>";
        
            echo "</form>";
        
        echo "</div>";
        echo "</div>";
        echo "</div>";
     
    
    }
  

    if(isset($_POST['ModClnt']) && isset($_POST['idEmp'])){
        $p_cln->modCliente($conexion,$_POST['idEmp'],$_POST['nmCln']);
    }
    
    if(isset($_POST['ModTlfClnt']) && isset($_POST['idEmp'])){
        $p_cln->modTelefono($conexion,$_POST['idEmp'],$_POST['tlf']);
    }
    if(isset($_POST['anadirObra']) && isset($_POST['idEmp'])){
        $p_cln->addObraCivil($conexion,$_POST['idEmp'],$_POST['nmObra']);
    }

    if(isset($_POST['anadircliente'])){
        $p_cln->addCliente($conexion,$_POST['aclnt'],$_POST['atlf']);
    }


?>