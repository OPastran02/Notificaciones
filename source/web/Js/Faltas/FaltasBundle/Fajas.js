var Fajas = function () {

    var excel = function (path) {
        var gif = routesUsuario.myroutes.gif;

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


    var restartPassword = function (id) {
         var confirmar = confirm("Esta seguro de Resetear la contraseña");
         var values = {
            id:id,
            confirmar:confirmar
         };   
         var url= routesUsuario.myroutes.resetPassword;
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

    

    return {
        restartPassword: function (id) {
            restartPassword(id);
        },

        excel: function (path) {
                excel(path);
        },

        mostrarmodalReasignar: function(){
            $('#modal').modal('show');
         }


    };

}();