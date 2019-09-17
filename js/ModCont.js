function mostrarCont(id) {
    if(id == "iddle"){
        $("#Numero").hide();
        $("#empresa").hide();
        $("#tamaño").hide();
        $("#estado").hide();
    }
    if(id == "Numero"){
        $("#Numero").show();
        $("#empresa").hide();
        $("#tamaño").hide();
        $("#estado").hide();
    }
    if(id == "empresa"){
        $("#Numero").hide();
        $("#empresa").show();
        $("#tamaño").hide();
        $("#estado").hide();
    }
    if(id == "tamaño"){
        $("#Numero").hide();
        $("#empresa").hide();
        $("#tamaño").show();
        $("#estado").hide();
    }
    if(id == "estado"){
        $("#Numero").hide();
        $("#empresa").hide();
        $("#tamaño").hide();
        $("#estado").show();
    }
}