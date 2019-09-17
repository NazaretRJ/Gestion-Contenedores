function MostrarResiduo(id){
    if(id == 'Recogida')
        //$("#CasillaResiduo").show();
        document.getElementById('CasillaResiduo').style.display='flex';
    else
        document.getElementById('CasillaResiduo').style.display='none';
        //$("#CasillaResiduo").hide();
}

function MostrarObras(nombre){
    document.getElementById('CasillaObra').style.display='flex';
}

function MostrarBuscar(id){

    if(id == "iddle"){
        $("#Fecha").hidde();
        $("#NumAlb").hide();
        $("#Cliente").hide();
        $("#Limite").hide();
        $("#Enlace").hide();
    }

    if(id == "FC"){
        $("#Fecha").show();
        $("#NumAlb").hide();
        $("#Cliente").hide();
        $("#Limite").hide();
        $("#Enlace").hide();
    }
    if(id == "NA"){
        $("#Fecha").hide();
        $("#NumAlb").show();
        $("#Cliente").hide();
        $("#Limite").hide();
        $("#Enlace").hide();
    }
    if(id == "Cli"){
        $("#Fecha").hide();
        $("#NumAlb").hide();
        $("#Cliente").show();
        $("#Limite").hide();
        $("#Enlace").hide();
    }
    if(id == "LS"){
        $("#Fecha").hide();
        $("#NumAlb").hide();
        $("#Cliente").hide();
        $("#Limite").show();
        $("#Enlace").hide();
    }
    if(id == "NE"){
        $("#Fecha").hide();
        $("#NumAlb").hide();
        $("#Cliente").hide();
        $("#Limite").hide();
        $("#Enlace").show();
        $("#BuscarEN").click();
    }
}