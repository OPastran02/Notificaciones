var jsHome = function () {

    var restartPassword = function (id) {

        var gif = routesUsuario.myroutes.gif;
        
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
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
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

    var excel1 = function (path) {

        var gif = routesUsuario.myroutes.gif;

         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head1").children.length;
        

         for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            if (document.getElementById("1order_"+i) != null){
                values=document.getElementById("1order_"+i).value;
            }else{
                values="";
            }
            arraybusqueda[i].push( values );
            if (document.getElementById("1order_"+i+"_2") != null){
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
                //window.open(response);
                const link=document.createElement('a');
                link.href=window.URL.createObjectURL(response);
                link.download=Date.now()+".xls";
                link.click();
                //alert(response);
            },
            complete: function(response){
                $.unblockUI();            
            }
        });

    }

    var excel2 = function (path) {

        var gif = routesUsuario.myroutes.gif;        
         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head2").children.length;
        
         for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            if (document.getElementById("2order_"+i) != null){
                if (document.getElementById("2order_"+i+"_2") != null){ 
                    arraybusqueda[i].push( document.getElementById("2order_"+i).value );
                    arraybusqueda[i].push( document.getElementById("2order_"+i+"_2").value );
                }else{
                    arraybusqueda[i].push( document.getElementById("2order_"+i).value );
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
                //alert(response);
            },
            complete: function(response){
                $.unblockUI();            
            }
        });

      }


    var excel3 = function (path) {

        var gif = routesUsuario.myroutes.gif;

         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head3").children.length;
        
         for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            if (document.getElementById("3order_"+i) != null){
                if (document.getElementById("3order_"+i+"_2") != null){ 
                    arraybusqueda[i].push( document.getElementById("3order_"+i).value );
                    arraybusqueda[i].push( document.getElementById("3order_"+i+"_2").value );
                }else{
                    arraybusqueda[i].push( document.getElementById("3order_"+i).value );
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
                //alert(response);
            },
            complete: function(response){
                $.unblockUI();            
            }
        });

      }   

      var excel4 = function (path) {

        var gif = routesUsuario.myroutes.gif;

         var encontro=true;
         var arraybusqueda=new Array();
         var values;
         var c = document.getElementById("head4").children.length;
        
        for ( i=0 ;i<=c-1 ;i++){
            arraybusqueda[i]=new Array();
            if (document.getElementById("4order_"+i) != null){
                if (document.getElementById("4order_"+i+"_2") != null){ 
                    arraybusqueda[i].push( document.getElementById("4order_"+i).value );
                    arraybusqueda[i].push( document.getElementById("4order_"+i+"_2").value );
                }else{
                    arraybusqueda[i].push( document.getElementById("4order_"+i).value );
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
                //alert(response);
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

        excel1: function (path) {
            excel1(path);
        },

        excel2: function (path) {
            excel2(path);
        },

        excel3: function (path) {
            excel3(path);
        },

        excel4: function (path) {
            excel4(path);
        }

    };

}();