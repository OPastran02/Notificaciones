var Establecimiento = function () {    

    var verificarClausura = function () { 
          var values = {};

          var url= routesUsuario.myroutes.VerificarClausura;
          var gif = routesUsuario.myroutes.gif;

          var res = '';
          var link = '';
          $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){
             /*$.blockUI({
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });*/
            },
            success: function(response){
              res = response;
              if(res.result == 'SI'){                                
                link = document.getElementById("linkDispo").href;
                link = link.replace("/0","/"+res.data);
                document.getElementById("linkDispo").href = link;                
                $('#notaClausura').removeClass('hidden');    
              }
            },
            complete: function(response){
                //$.unblockUI();            
            }
          });
    }


    return {
        //main function to initiate the module
        init: function () {
            verificarClausura();
        },

    };

}();