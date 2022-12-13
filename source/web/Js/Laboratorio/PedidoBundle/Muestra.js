var Muestra = function () {

    var excel = function (path) {
        var encontro = true;
        var arraybusqueda = new Array();
        var values;
        var c = document.getElementById("head").children.length;

        for ( i=0; i <= c-1; i++ )
        {
            arraybusqueda[i] = new Array();

            if (document.getElementById("order_" + i) != null)
            {
                if (document.getElementById("order_" + i + "_2") != null)
                {
                    arraybusqueda[i].push(document.getElementById("order_" + i).value);
                    arraybusqueda[i].push(document.getElementById("order_" + i + "_2").value);
                }
                else
                    arraybusqueda[i].push(document.getElementById("order_" + i).value);
            }
        }

        var values = {
            arraybusqueda : arraybusqueda,
        };
        var url = path;
        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend: function () {
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response) {
                window.open(response,"_self");
                //alert(response);
            },
            complete: function(response) {
                $.unblockUI();
            }
        });
    }

    var cambiarArea = function (idMuestra, idArea) {
        var values = {};
        var url = rutaCambiarArea.replace("0", idMuestra);
        url = url.replace("-1", idArea);
        window.location.href = url;
    }

    var supervisar = function (idMuestra) {
        var values = {};
        var url = rutaSupervisar.replace("0", idMuestra);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                alert(response);
                location.reload();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var desSupervisar = function (idMuestra) {
        var values = {};
        var url = rutaDesSupervisar.replace("0", idMuestra);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                alert(response);
                location.reload();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var autorizar = function (idMuestra) {
        var values = {};
        var url = rutaAutorizar.replace("0", idMuestra);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                alert(response);
                location.reload();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var desAutorizar = function (idMuestra) {
        var values = {};
        var url = rutaDesAutorizar.replace("0", idMuestra);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                alert(response);
                location.reload();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var finalizar = function (idMuestra, idArea) {
        var values = {};

        var url = rutaFinalizar.replace("0", "'"+idMuestra+"'");
        url = url.replace("-1", "'"+idArea+"'");

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                alert(response);
                location.reload();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var desFinalizar = function (idMuestra,idArea) {
        var values = {};
        var url = rutaDesFinalizar.replace("0", idMuestra);
        url = url.replace("-1", idArea);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                alert(response);
                location.reload();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var bloquear = function (idResultado, bloquear) {
        var values = {};
        var url = rutaBloquear.replace("0", idResultado);
        url = url.replace("-1", bloquear);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                resp =  response;

                if(resp == "OK")
                {
                    if(bloquear == 0)
                        $(".page-content").append('<input class="successFlashMessage" value="DESBLOQUEADO" hidden>');
                    else
                        $(".page-content").append('<input class="successFlashMessage" value="BLOQUEADO" hidden>');
                }
                else
                    $(".page-content").append('<input class="errorFlashMessage" value="' + resp + '" hidden>');

                UIToastr.init();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var traerMuestras = function (anio)
    {
        var url = rutaTraerMuestras.replace("0", anio);
        window.location.href = url;
    }

    var irAMuestra = function (idMuestra, idArea)
    {
        var url = rutaMuestra.replace("0", idMuestra);
        url = url.replace("-1", idArea);
        window.open(url, '_blank');
    }

    var autorizarPendiente = function (idMuestra, numeroMuestra)
    {
        var url = rutaAutorizar.replace("0", idMuestra);

        $.ajax({
            type: "POST",
            url: url,
            data: {},
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                if(response == 'Muestra autorizada')
                {
                    $(".page-content").append('<input class="successFlashMessage" value="' + response + '" hidden>');

                    // Marcar muestra autorizada

                    var idPendienteParaVer = 'VER_' + numeroMuestra;
                    var pendienteParaVer = document.getElementById(idPendienteParaVer);
                    var idPendienteParaAutorizar = 'AUTORIZAR_' + numeroMuestra;
                    var pendienteParaAutorizar = document.getElementById(idPendienteParaAutorizar);

                    pendienteParaVer.setAttribute('class', 'btn btn-circle green-jungle btn-sm');
                    pendienteParaVer.setAttribute('style', 'opacity: 50%;');
                    pendienteParaAutorizar.setAttribute('title', 'Muestra autorizada');
                    pendienteParaAutorizar.setAttribute('class', 'btn btn-circle default btn-sm');
                    pendienteParaAutorizar.setAttribute('disabled', 'true');
                    pendienteParaAutorizar.innerHTML = 'Autorizada';
                }
                else
                    $(".page-content").append('<input class="errorFlashMessage" value="' + response + '" hidden>');

                UIToastr.init();
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var autorizarTodasLasMuestrasPendientes = function (muestrasPendientes)
    {
        muestrasPendientes = muestrasPendientes.slice(0,2);

        if (window.confirm('Está seguro de que desea autorizar todas las muestras?'))
        {
            $.ajax({
                type: "POST",
                url: rutaAutorizarTodasLasPendientes,
                data: {
                    muestrasPendientes: muestrasPendientes
                },
                beforeSend : function (){
                    $.blockUI({
                        message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                        css: { border: 'none'}
                    });
                },
                success: function(response){
                    console.log(response);

                    if (response == 'Todas las muestras fueron autorizadas')
                        $(".page-content").append('<input class="successFlashMessage" value="' + response + '" hidden>');
                    else
                    {
                        if (response == 'No se pudieron autorizar todas las muestras')
                            $(".page-content").append('<input class="alertFlashMessage" value="' + response + '" hidden>');
                        else
                            $(".page-content").append('<input class="errorFlashMessage" value="' + response + '" hidden>');
                    }

                    UIToastr.init();
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
    }

    return {
        //main function to initiate the module
        init: function () { },

        excel: function (path) {
            excel(path);
        },

        traerMuestras: function (anio) {
            traerMuestras(anio);
        },

        irAMuestra: function (idMuestra, idArea) {
            irAMuestra(idMuestra, idArea);
        },

        cambiarArea: function (idMuestra, idArea) {
            cambiarArea(idMuestra, idArea);
        },

        supervisar: function (idMuestra) {
            supervisar(idMuestra);
        },

        desSupervisar: function (idMuestra) {
            desSupervisar(idMuestra);
        },

        autorizar: function (idMuestra) {
            autorizar(idMuestra);
        },

        autorizarPendiente: function (idMuestra, numeroMuestra) {
            autorizarPendiente(idMuestra, numeroMuestra);
        },

        autorizarTodasLasMuestrasPendientes: function (muestrasPendientes) {
            autorizarTodasLasMuestrasPendientes(muestrasPendientes);
        },

        desAutorizar: function (idMuestra) {
            desAutorizar(idMuestra);
        },

        finalizar: function (idMuestra,idArea) {
            finalizar(idMuestra,idArea);
        },

        desFinalizar: function (idMuestra,idArea) {
            desFinalizar(idMuestra,idArea);
        },

        bloquear: function (idResultado, idArea) {
            bloquear(idResultado, idArea);
        },
    };

}();