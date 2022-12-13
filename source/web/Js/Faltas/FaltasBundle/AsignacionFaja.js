var AsignacionFaja = function () {

    var mostrarModalReasignar = function (id) {
        var gif = routesUsuario.myroutes.gif;

        var values = {};
        var url = routesUsuario.myroutes.modalReasignacion;

        $.ajax({
            type: "POST",
            url: routesUsuario.myroutes.modalReasignacion.replace("0", id),
            data: values,
            beforeSend : function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                $('#modalReasignar div.modal-body').html(response);
                $("#referenciaFaja").val(id);
                $('.fecha-picker').datepicker({ dateFormat: "dd-mm-yy" });
                $('#modalReasignar').modal('show');
                UIButtons.init();
                ComponentsSelect2.init();
            },
            complete: function(response){
                $.unblockUI();
            }
        });

    }
    
    var excel = function (path) {
        var gif = routesUsuario.myroutes.gif;
         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head").children.length;

         for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            //alert(document.getElementById("order_"+i).value);
            if (document.getElementById("order_"+i) != null){

                values=document.getElementById("order_"+i).value;
            }else{
                values="";
            }
            arraybusqueda[i].push(values) ;
            if (document.getElementById("order_"+i+"_2") != null){
                values=document.getElementById("order_"+i+"_2").value;
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

    var enviarNuevaReasignacion = function (){
        var gif = routesUsuario.myroutes.gif;
        
        var values = {};       

        var values = $( "#modalForm" ).serializeArray();
        var resp;

        var id = $("#referenciaFaja").val();        
        
        $.ajax({
            type: $("#modalForm").attr( 'method' ),
            url: routesUsuario.myroutes.modalReasignacion.replace("0", id),
            data: values,
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                if(response == "OK")
                {
                    $(".page-content").append('<input class="successFlashMessage" value="Guardado" hidden>');
                    UIToastr.init();
                }
                else
                {
                    if(response == "REASIGNACION YA EXISTENTE")
                    {
                        $(".page-content").append('<input class="errorFlashMessage" value="REASIGNACION YA EXISTENTE" hidden>');
                        UIToastr.init();
                    }
                    else
                        $('.modal-body').html(response);
                }
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    return {
        //main function to initiate the module
        init: function () {},

        mostrarModalReasignar: function (id) {
            mostrarModalReasignar(id);
        },

        enviarNuevaReasignacion: function () {
            enviarNuevaReasignacion();
        },

        excel: function (path) {

            excel(path);
            //alert("asd");
        }

    };

}();