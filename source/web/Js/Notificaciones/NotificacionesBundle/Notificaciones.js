var Notificaciones = function () {    

    var initMap = function () {
      var mapa = new GMaps({
        div: '#gmap',
        lat: -34.61,
        lng: -58.45,
        zoom: 12
      });
    }


    var autorizarPedido = function (id) {
        var confirmar = confirm("¿Esta seguro de autorizar el pedido?");

        if(confirmar){
          var values = {};
          var url= routesAutorizar.myroutes.autorizar.replace("0",id);
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
            },
            complete: function(response){
                $.unblockUI();            
            }
          });
        }
    }


    var eliminarPedido = function (id) {
        var confirmar = confirm("¿Esta seguro de eliminar el pedido?");

        if(confirmar){
          var values = {};   
          var url= routesAutorizar.myroutes.eliminar.replace("0",id);
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
            },
            complete: function(response){
                $.unblockUI();            
            }
          });
        }
    }


    var excel = function (path) {

         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head").children.length;
        
         for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            if (document.getElementById("order_"+i) != null){
                if (document.getElementById("order_"+i+"_2") != null){ 
                    arraybusqueda[i].push( document.getElementById("order_"+i).value );
                    arraybusqueda[i].push( document.getElementById("order_"+i+"_2").value );
                }else{
                    arraybusqueda[i].push( document.getElementById("order_"+i).value );
                }    
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
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
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


    var excelAutorizar = function (path) {

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
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
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


    var ubicar = function (id) {
      var cuadras = $("#cuadras").val();
      var values = {};

      var url = (routesUsuario.myroutes.distanciaNotificacion.replace("0", id)).replace("-1", cuadras);
      var gif = "{{ asset('public/assets/global/img/loading-spinner-grey.gif') }}";

      $.ajax({
        type: "POST",
        url: url,
        data: values,
        beforeSend : function (){
          $.blockUI({
            message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
            css: { border: 'none'}
          });
        },
        success: function(response){
          $('#resultados').html(response);

          $('html,body').animate({
            scrollTop: $("#resultados").offset().top
          }, 1500);

          // INICIO DIBUJADO DE MARCADORES

          var mapa = new GMaps({
            div: '#gmap',
            lat: -34.61,
            lng: -58.45,
            zoom: 12
          });

          var i = 0;
          var long = $('#longitud0').val();
          var lat = $('#latitud0').val();
          var dire = $('#direccion0').val();

          while(lat != undefined) //podría preguntar por cualquiera
          {
            mapa.addMarker({
              lng: long,
              lat: lat,
              details: dire,
              click: function(){
                alert(this.details);
              }
            });

            i++;
            var long = $('#longitud' + i).val();
            var lat = $('#latitud' + i).val();
            var dire = $('#direccion' + i).val();
          }

          // FIN DIBUJADO DE MARCADORES
        },
        complete: function(response){
          $.unblockUI();
        }
      });
    }

    
    var send = function () {
      var values = {};       

      var values = $( "#formulario" ).serializeArray();
      var resp;
      
      $.ajax({
        type: $("#formulario").attr( 'method' ),
        url: $("#formulario").attr( 'action' ),
        data: values,
        beforeSend : function (){
          $.blockUI({
            message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
            css: { border: 'none'}
          });
        },
        success: function(response){
          resp =  response;

          $(".page-content").append('<input class="successFlashMessage" value="Guardado" hidden>');
          UIToastr.init();

          setTimeout(function() {
            $.blockUI({
              message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando notificaciones...</span></div>',
              css: { border: 'none'}
            });
          }, 1000);

          setTimeout(function() {
            window.location.reload();
          }, 3000);
        },
        complete: function(response){
            $.unblockUI();
        }
      });
    }


    /*----------------------------------------*/


    return {
        //main function to initiate the module
        init: function () {
            initMap();
        },

        autorizarPedido: function (id) {
            autorizarPedido(id);
        },

        eliminarPedido: function (id) {
            eliminarPedido(id);
        },

        excel: function (path) {
            excel(path);
        },

        excelAutorizar: function (path) {
            excelAutorizar(path);
        },

        ubicar: function (id) {
            ubicar(id);
        },

        send: function() {
            send();
        },

    };

}();