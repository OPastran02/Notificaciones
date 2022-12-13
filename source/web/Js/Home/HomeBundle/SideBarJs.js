var SideBarJs = function () {

    var verAlertasInspeccion = function () {
        
         var values = {};   
         var url= routesSideBar.myroutes.alerta;
         $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){             
            },
            success: function(response){
              $('#header_notification_bar').html(response);
            },
            complete: function(response){
                
            }
        });
    }    

    var verAlertasReInspeccion = function () {
        
         var values = {};   
         var url= routesSideBar.myroutes.alertaReInspeccion;
         $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){             
            },
            success: function(response){
              $('#header_notification_bar2').html(response);
            },
            complete: function(response){
                
            }
        });
    }

    return {
        //main function to initiate the module
        init: function () {
          
        },

        verAlertasInspeccion: function () {
            verAlertasInspeccion();
        },

        verAlertasReInspeccion: function () {
            verAlertasReInspeccion();
        },
        
    };

}();