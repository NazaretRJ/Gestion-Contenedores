function mostrarContSN(id) {
    if(id == "iddle"){
        $("#Capacidad").hide();
        $("#empresa").hide();
        $("#stock").hide();
    }
    if(id == "Capacidad"){
        $("#Capacidad").show();
        $("#empresa").hide();
        $("#stock").hide();
    }
    if(id == "empresa"){
        $("#Capacidad").hide();
        $("#empresa").show();
        $("#stock").hide();
    }
    if(id == "stock"){
        $("#Capacidad").hide();
        $("#empresa").hide();
        $("#stock").show();
    }

}