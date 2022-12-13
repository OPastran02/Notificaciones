var jsUsuario = function () {

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
        //main function to initiate the module
        init: function () {
          
        },

        restartPassword: function (id) {
            restartPassword(id);
        },

    };

}();