function mostrar(id) {
    if(id == "iddle"){
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#fecha").hide();
        $("#cliente").hide();
        $("#obra").hide();
        $("#contenedor").hide();
        $("#residuo").hide();
    }
    if(id == "NumAlb") {
        $("#NumAlb").show();
        $("#tipo_nuevo").hide();
        $("#fecha").hide();
        $("#cliente").hide();
        $("#obra").hide();
        $("#contenedor").hide();
        $("#residuo").hide();
    }

    if(id == "tipo") {
        $("#NumAlb").hide();
        $("#tipo_nuevo").show();
        $("#fecha").hide();
        $("#cliente").hide();
        $("#obra").hide();
        $("#contenedor").hide();
        $("#residuo").hide();
    }
    if(id == "fecha"){
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#fecha").show();
        $("#cliente").hide();
        $("#obra").hide();
        $("#contenedor").hide();
        $("#residuo").hide();
    }
    if(id == "cliente"){
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#fecha").hide();
        $("#cliente").show();
        $("#obra").hide();
        $("#contenedor").hide();
        $("#residuo").hide();
    }
    if(id == "obra"){
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#fecha").hide();
        $("#cliente").hide();
        $("#obra").show();
        $("#contenedor").hide();
        $("#residuo").hide();
    }
    if(id == "contenedor"){
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#fecha").hide();
        $("#cliente").hide();
        $("#obra").hide();
        $("#contenedor").show();
        $("#residuo").hide();
    }
    if(id == "residuo"){
        $("#NumAlb").hide();
        $("#tipo_nuevo").hide();
        $("#fecha").hide();
        $("#cliente").hide();
        $("#obra").hide();
        $("#contenedor").hide();
        $("#residuo").show();
    }

}