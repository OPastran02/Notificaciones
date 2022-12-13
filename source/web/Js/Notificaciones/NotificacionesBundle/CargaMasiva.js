var CargaMasiva = function () {

    var obtenerDatosPedido = function()
    {
        var pedido = $('#pedido').val();
        var plazo1 = $('#plazo1').val();
        var plazo2 = $('#plazo2').val();
        var citacion = $('#citacion').val();
        var nocturnidad = $('#nocturnidad').val();
        var tipo = $('#tipo').val();
        var fojas = $('#fojas').val();
        var vencer = $('#vencer').val();
        var modelo = $('#modelo').val();

        return [pedido, plazo1, plazo2, citacion, nocturnidad, tipo, fojas, vencer, modelo];
    }


    var obtenerDatosEstablecimiento = function(n)
    {
        var calleR = $('#calleR' + n).val();
        var alturaR = $('#alturaR' + n).val();
        var pisoR = $('#pisoR' + n).val();
        var dptoR = $('#dptoR' + n).val();
        var localR = $('#localR' + n).val();
        var enviarR = $('#enviarR' + n).val();
        var destinatario = $('#destinatario' + n).val();
        var calleL = $('#calleL' + n).val();
        var alturaL = $('#alturaL' + n).val();
        var pisoL = $('#pisoL' + n).val();
        var dptoL = $('#dptoL' + n).val();
        var localL = $('#localL' + n).val();
        var enviarL = $('#enviarL' + n).val();

        return [calleR, alturaR, pisoR, dptoR, localR, enviarR, destinatario, calleL, alturaL, pisoL, dptoL, localL, enviarL];
    }


    var obtenerDatosEstablecimientoModal = function()
    {
        var calleR = $('#modalcalleR').val();
        var alturaR = $('#modalalturaR').val();
        var pisoR = $('#modalpisoR').val();
        var dptoR = $('#modaldptoR').val();
        var localR = $('#modallocalR').val();
        var enviarR = $('#modalenviarR').val();
        var destinatario = $('#modaldestinatario').val();
        var calleL = $('#modalcalleL').val();
        var alturaL = $('#modalalturaL').val();
        var pisoL = $('#modalpisoL').val();
        var dptoL = $('#modaldptoL').val();
        var localL = $('#modallocalL').val();
        var enviarL = $('#modalenviarL').val();

        return [calleR, alturaR, pisoR, dptoR, localR, enviarR, destinatario, calleL, alturaL, pisoL, dptoL, localL, enviarL];
    }


    var mostrarModalDecision = function(establecimientos, numero)
    {
        var values = {};
        
        var url = routes.results.decidirEstablecimiento;
        
        var str = '';
        for (var i = 0; i < establecimientos.length; i++) {
            str += establecimientos[i].id + '||||';
            str += establecimientos[i].razonSocial + '||||';
            str += establecimientos[i].Direccion + '||||||||';
        }

        url = routes.results.decidirEstablecimiento.replace("0", str);
        //var gif = routes.results.gif;

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                $('.modal-body').html(response);
                $('.modal-title').html("Seleccionar establecimiento");
                $('#modalCM').modal('show');
                $('#numero').val(numero); //SIEMPRE EL NÚMERO VA A SER EL MISMO. ACÁ NO DEBERÍA HACERLO (?)
                var datos = obtenerDatosEstablecimiento(numero);
                //VOLCAR LOS DATOS EN EL MODAL
                var calleR = $('#modalcalleR').val(datos[0]);
                var alturaR = $('#modalalturaR').val(datos[1]);
                var pisoR = $('#modalpisoR').val(datos[2]);
                var dptoR = $('#modaldptoR').val(datos[3]);
                var localR = $('#modallocalR').val(datos[4]);
                var enviarR = $('#modalenviarR').val(datos[5]);
                var destinatario = $('#modaldestinatario').val(datos[6]);
                var calleL = $('#modalcalleL').val(datos[7]);
                var alturaL = $('#modalalturaL').val(datos[8]);
                var pisoL = $('#modalpisoL').val(datos[9]);
                var dptoL = $('#modaldptoL').val(datos[10]);
                var localL = $('#modallocalL').val(datos[11]);
                var enviarL = $('#modalenviarL').val(datos[12]);
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    var elegir = function (idEstablecimiento)
    {
        var values = {};

        var url = routes.results.elegir;

        url = url.replace("0", idEstablecimiento); //idEstablecimiento

        var datosPedido = obtenerDatosPedido();

        url = url.replace("-1", datosPedido[0]); //idPedido
        
        if(datosPedido[1] == '')
            url = url.replace("-2", '0');
        else
            url = url.replace("-2", datosPedido[1]); //plazo1

        if(datosPedido[2] == '')
            url = url.replace("-3", '0');
        else
            url = url.replace("-3", datosPedido[2]); //plazo2

        if(datosPedido[3] == '')
            url = url.replace("-4", '0');
        else
            url = url.replace("-4", datosPedido[3]); //citacion

        if(datosPedido[4] == '')
            url = url.replace("-5", '0');
        else
            url = url.replace("-5", datosPedido[4]); //nocturnidad

        if(datosPedido[5] == '')
            url = url.replace("-6", '0');
        else
            url = url.replace("-6", datosPedido[5]); //idTipo

        if(datosPedido[6] == '')
            url = url.replace("-7", '0');
        else
            url = url.replace("-7", datosPedido[6]); //fojas
        
        if(datosPedido[7] == '')
            url = url.replace("-8", '0');
        else
            url = url.replace("-8", datosPedido[7]); //vencer

        if(datosPedido[8] == '')
            url = url.replace("-9", '0');
        else
            url = url.replace("-9", datosPedido[8]); //idModelo

        var datosEstablecimiento = obtenerDatosEstablecimientoModal();

        if(datosEstablecimiento[0] == '')
            url = url.replace("-10", '0');
        else
            url = url.replace("-10", datosEstablecimiento[0]); //calleR

        if(datosEstablecimiento[1] == '')
            url = url.replace("-11", '0');
        else
            url = url.replace("-11", datosEstablecimiento[1]); //alturaR

        if(datosEstablecimiento[2] == '')
            url = url.replace("-12", '0');
        else
            url = url.replace("-12", datosEstablecimiento[2]); //pisoR

        if(datosEstablecimiento[3] == '')
            url = url.replace("-13", '0');
        else
            url = url.replace("-13", datosEstablecimiento[3]); //dptoR

        if(datosEstablecimiento[4] == '')
            url = url.replace("-14", '0');
        else
            url = url.replace("-14", datosEstablecimiento[4]); //localR

        if(datosEstablecimiento[5] == '')
            url = url.replace("-15", '0');
        else
            url = url.replace("-15", datosEstablecimiento[5]); //enviarR

        if(datosEstablecimiento[6] == '')
            url = url.replace("-16", '0');
        else
            url = url.replace("-16", datosEstablecimiento[6]); //destinatario

        if(datosEstablecimiento[7] == '')
            url = url.replace("-17", '0');
        else
            url = url.replace("-17", datosEstablecimiento[7]); //calleL

        if(datosEstablecimiento[8] == '')
            url = url.replace("-18", '0');
        else
            url = url.replace("-18", datosEstablecimiento[8]); //alturaL

        if(datosEstablecimiento[9] == '')
            url = url.replace("-19", '0');
        else
            url = url.replace("-19", datosEstablecimiento[9]); //pisoL

        if(datosEstablecimiento[10] == '')
            url = url.replace("-20", '0');
        else
            url = url.replace("-20", datosEstablecimiento[10]); //dptoL

        if(datosEstablecimiento[11] == '')
            url = url.replace("-21", '0');
        else
            url = url.replace("-21", datosEstablecimiento[11]); //localL

        if(datosEstablecimiento[12] == '')
            url = url.replace("-22", '0');
        else
            url = url.replace("-22", datosEstablecimiento[12]); //enviarL

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                $('#modalCM').modal('hide');
                var numero = $('#numero').val();

                var fila = '#warning' + numero;
                $(fila).removeClass('warning');
                $(fila).addClass('active');

                var celda = '#labelWarning' + numero;
                $(celda).html('Cédulas creadas');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log('error');
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }



    return {
        init: function () {},

        mostrarModalDecision: function(establecimientos, numero) {
            mostrarModalDecision(establecimientos, numero);
        },

        elegir: function(idEstablecimiento) {
            elegir(idEstablecimiento);
        }
    };

}();


jQuery(document).ready(function() {
    CargaMasiva.init();
});