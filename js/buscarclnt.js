function buscarCliente(str){
    // Si no se escribe nada no se muestra nada de resultado.
    if (str == ""){
       document.getElementById("muestra_clientes").innerHTML = "";
       return;
    } 
    else{
       if (window.XMLHttpRequest){
           // code for IE7+, Firefox, Chrome, Opera, Safari
           xmlhttp = new XMLHttpRequest();
        } 
        else {
           // code for IE6, IE5
           xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){
               document.getElementById("muestra_clientes").innerHTML = this.responseText;
            }
        };
  
    
       // Se envia con GET la cadena a buscar en la BD
       xmlhttp.open("GET","modules/buscarcliente.php?q=" + str, true);
       // Se muestra
       xmlhttp.send();
    }
}