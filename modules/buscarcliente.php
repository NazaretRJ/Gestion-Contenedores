<?php 
include("conexion.php");
$q = $_GET['q']; //q es lo que está escribiendo

    $res = mysqli_query($conexion,"SELECT * FROM cliente WHERE UPPER(nomEmp) LIKE UPPER('%".$q."%')");
    if ($res->num_rows > 0) {
        while($a_res =  mysqli_fetch_assoc($res)) {
            echo "<option value='./clientes.php?id=".$a_res['nomEmp']."'>".$a_res['nomEmp']."</option>";
        }
    } 
    else {
        echo "<option value='0'> No se encontró. </option>";
}
?>