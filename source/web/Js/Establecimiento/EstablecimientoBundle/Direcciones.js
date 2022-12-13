var Direcciones = function () {    

    var verificarDireccion = function (id) { 
         
        var res = '';
         id = id.replace("altura","").replace("calle","");
         
         if($('#'+id+"calle").val() != '' && $('#'+id+"altura").val() != '' ){
            var values = {};
            var url= routeDireccionSMP.replace("0",$('#'+id+"calle").val()).replace("-1",$('#'+id+"altura").val());
            var gif = routesUsuario.myroutes.gif;

            var res = '';
            var link = '';
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
                res = response;                
                if(res.result != 'ERROR' && res.result != 'NO'){                                  
                  $("#direccionessmpbody").html(response);
                  $('#modaldireccionessmp').modal('show');
                  
                }else if(res.result == 'ERROR'){
                  alert(res.data);
                }else{}
              },
              complete: function(response){
                  $.unblockUI();            
              }
            });
         }         
    }

    return {
        //main function to initiate the module
        init: function () {          

        },

        verificarDireccion: function(id){
          verificarDireccion(id);
        },


    };

}();