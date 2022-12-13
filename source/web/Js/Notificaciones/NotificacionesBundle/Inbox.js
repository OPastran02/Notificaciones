var Inbox = function () {    

    var vistas = function (id) {
       var link = routesInbox.myroutes.ImprimirInbox.replace("0",id);
       window.open(link,"_blank");
    }

    var vistasautorizar = function (id) {
       var link = routesAutorizar.myroutes.ImprimirInbox.replace("0",id);
       window.open(link,"_blank");
    }

    var guardarCedula = function (id) {
        var confirmar = confirm("¿esta seguro de cambiar datos en la cedula?");
        
        if(confirmar){
          var values = {
            fechaEnvioFirma: $("#fechaEnvioFirma"+id).val(),
            fechaDevolucion: $("#fechaDevolucion"+id).val(),
            observaciones: $("#observaciones"+id).val(),
            id:id
          };   
          var url= routesInbox.myroutes.guardarCedula.replace("0",id);
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


    var eliminarCedula = function (id) {
        var confirmar = confirm("¿esta seguro de eliminar la cedula?");
        
        if(confirmar){
          var values = {
            id:id
          };   
          var url= routesInbox.myroutes.eliminarCedula.replace("0",id);
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

    var buscarRemitos = function () {
         var values = {
            fechaInicial: $('#fechaInicial').val(),
            fechaFinal: $('#fechaFinal').val(),
         };   
         var url= routesInbox.myroutes.buscarremitos;
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
              $('#lista').html(response);
            },
            complete: function(response){
                $.unblockUI();            
            }
        });

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


    return {
        //main function to initiate the module
        init: function () {
          
        },

        guardarCedula: function (id) {
            guardarCedula(id);
        },

        eliminarCedula: function (id) {
            eliminarCedula(id);
        },

        buscarRemitos: function () {
            buscarRemitos();
        },

        excel: function (path) {
            excel(path);
        },

        vistas: function (id) {
            vistas(id);
        },

        vistasautorizar: function (id) {
            vistasautorizar(id);
        }

    };

}();