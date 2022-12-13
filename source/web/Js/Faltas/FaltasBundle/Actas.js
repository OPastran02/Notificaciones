String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

var Actas = function () {

    var buscarRemitos = function () {
        var gif = routesActa.myroutes.gif;
        var values = $("#remitoForm").serializeArray();
        var resp;
        var fechaInicial = '';
        var fechaFinal = '';

        var fi = $("#faltas_faltasbundle_remitoacta_fechaInicial").val();
        var ff = $("#faltas_faltasbundle_remitoacta_fechaFinal").val();

        if(fi != '')
            fechaInicial = formatearFecha(fi);

        if(ff != '')
            fechaFinal = formatearFecha(ff);

        if(fi == fechaInicial && ff == fechaFinal)
        {
            $.ajax({
                type: $("#remitoForm").attr('method'),
                url: $("#remitoForm").attr('action'),
                data: values,
                beforeSend : function (){
                    $.blockUI({
                        message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                        css: { border: 'none'}
                    });
                },
                success: function(response){
                    resp =  response;
                    $('#dragon').html(resp);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            alert('Fecha/s incorrecta/s');
    }

    var formatearFecha = function(s)
    {
        if(s.split('-') == s)
        {
            return -1;
        }
        else
        {
            var fia = s.split('-');
            var fi = new Date(fia[2],(fia[1]-1),fia[0]);

            var d = fi.getDate();
            var m = fi.getMonth()+1;
            var a = fi.getFullYear();

            if(fia[0].length == 2 && d < 10)
                d = '0' + d;

            if(fia[1].length == 2 && m < 10)
                m = '0' + m;
            else
                if(fia[1] == '01')
                    m = '0' + m;

            return d + '-' + m + '-' + a;
        }
    }

    var excel = function (path) {

        var gif = actas.myroutes.gif;

         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head").children.length;
        

         for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            if (document.getElementById("order_"+i) != null){
                values=document.getElementById("order_"+i).value;
            }else{
                values="";
            }
            arraybusqueda[i].push( values );
            if (document.getElementById("order_"+i+"_2") != null){
                arraybusqueda[i].push( values );
            }    
         }
         
         var values = {
            arraybusqueda : arraybusqueda,
         };   
         var url= path;
         $.ajax({
            type: "POST",
            url: url,
            data: values,
            xhr:function(){// Seems like the only way to get access to the xhr object
                var xhr = new XMLHttpRequest();
                xhr.responseType= 'blob'
                return xhr;
            },
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                const link=document.createElement('a');
                link.href=window.URL.createObjectURL(response);
                link.download=Date.now()+".xls";
                link.click();
            },
            complete: function(response){
                $.unblockUI();            
            }
        });

    }

    return {
        //main function to initiate the module
        init: function () {
          
        },

        buscarRemitos: function () {
            buscarRemitos();
        },

         excel: function (path) {
            excel(path);
        }

    };

}();