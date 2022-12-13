var Pedido = function () {    

    var autorizar = function () {
        var confirmar = confirm("¿Está seguro de que desea autorizar el pedido?");

        if(confirmar)
        {
            //alert('ANOTACIÓN: terminar el modal y añadir el campo');
            var values = {};
            var gif = routesPedido.myroutes.gif;
            var url = routesPedido.myroutes.decidirAutorizacion;

            $.ajax({
                type: "POST",
                url: url,
                data: values,
                beforeSend: function () {
                    $.blockUI({
                        message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                        css: { border: 'none'}
                    });
                },
                success: function(response) {
                    $('.modal-body').html(response);
                    $('.modal-title').html("AUTORIZAR PEDIDO Y CREAR MUESTRA");
                    $('#modalAutorizar').modal('show');
                },
                complete: function(response) {
                    $.unblockUI();
                }
            });
        }
    }

    var confirmarAutorizacion = function (id) {
        var numeroMuestra = $('#numeroMuestra').val();

        if (numeroMuestra && numeroMuestra != '0')
        {
            //Quitar espacios
            numeroMuestra = numeroMuestra.trim();
            //Convertir en array
            numeroMuestra = numeroMuestra.split('');

            //Quitar ceros
            var j = 0;
            var cero = true;
            var eliminar = []
            while (j < numeroMuestra.length && cero)
            {
                if (numeroMuestra[j] == '0')
                {
                    eliminar.push(j);
                    j++;
                }
                else
                    cero = false;
            }

            if (eliminar.length < numeroMuestra.length)
            {
                //Eliminar ceros (del principio)
                eliminar.forEach(function (elemento, indice, array) {
                    numeroMuestra.splice(elemento, 1, 'undefined');
                });
                numeroMuestra.clean('undefined');
                numeroMuestra = numeroMuestra.join('');

                //Chequear que todos los elementos sean números
                var bandera = false;
                for (var i = 0; i < numeroMuestra.length; i++)
                {
                    switch (numeroMuestra[i])
                    {
                        case '0':
                        case '1':
                        case '2':
                        case '3':
                        case '4':
                        case '5':
                        case '6':
                        case '7':
                        case '8':
                        case '9':
                            break;
                        default:
                            bandera = true;
                            break;
                    }
                }

                if (!bandera)
                {
                    var values = {};
                    var gif = routesPedido.myroutes.gif;
                    var url= routesPedido.myroutes.autorizar.replace("0", id);
                    url = url.replace("-1", numeroMuestra);

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

                            if (response != 'No se puede crear la muestra, ya existe una con ese número')
                                location.reload();
                        },
                        complete: function(response){
                            $.unblockUI();
                        }
                    });
                }
                else
                    alert('Ingrese un número válido de muestra');
            }
            else
                alert('Ingrese un número de muestra');
        }
        else
            alert('Ingrese un número de muestra');
    }

    var anular = function (id) {
        var confirmar = confirm("¿Está seguro de que desea anular el pedido?");

        if(confirmar){
          var values = {};
          var gif = routesPedido.myroutes.gif;
          var url= routesPedido.myroutes.anular.replace("0",id);

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
              location.reload();
            },
            complete: function(response){
                $.unblockUI();            
            }
          });
        }
    }

    var eliminar = function (id) {
        var confirmar = confirm("¿Está seguro de que desea eliminar el pedido?");

        if(confirmar){
          var values = {};
          var gif = routesPedido.myroutes.gif;
          var url = routesPedido.myroutes.eliminar.replace("0",id);

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
              location.reload();
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
        
        var gif = routesPedido.myroutes.gif;
        var values = {
            arraybusqueda : arraybusqueda,
        };
        var url= path;

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
                window.open(response,"_self");
                //alert(response);
            },
            complete: function(response){
                $.unblockUI();
            }
        });

    }

    var premuestreo = function ()
    {
        var values = {};
        var gif = routesPedido.myroutes.gif;
        var url = routesPedido.myroutes.premuestreo;

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
                $('#modal .modal-body').html(response);
                $('#modal .modal-title').html("Crear muestra");
                $('#modal').modal('show');
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var crearPremuestra = function ()
    {
        var numeroMuestra = $('#numeroMuestra').val();

        if (numeroMuestra && numeroMuestra != '0')
        {
            //Quitar espacios
            numeroMuestra = numeroMuestra.trim();
            //Convertir en array
            numeroMuestra = numeroMuestra.split('');

            //Quitar ceros
            var j = 0;
            var cero = true;
            var eliminar = []
            while (j < numeroMuestra.length && cero)
            {
                if (numeroMuestra[j] == '0')
                {
                    eliminar.push(j);
                    j++;
                }
                else
                    cero = false;
            }

            if (eliminar.length < numeroMuestra.length)
            {
                //Eliminar ceros (del principio)
                eliminar.forEach(function (elemento, indice, array) {
                    numeroMuestra.splice(elemento, 1, 'undefined');
                });
                numeroMuestra.clean('undefined');
                numeroMuestra = numeroMuestra.join('');

                //Chequear que todos los elementos sean números
                var bandera = false;
                for (var i = 0; i < numeroMuestra.length; i++)
                {
                    switch (numeroMuestra[i])
                    {
                        case '0':
                        case '1':
                        case '2':
                        case '3':
                        case '4':
                        case '5':
                        case '6':
                        case '7':
                        case '8':
                        case '9':
                            break;
                        default:
                            bandera = true;
                            break;
                    }
                }

                if (!bandera)
                {
                    var fechaMuestra = $('#fechaMuestra').val();
                    var idPrograma = $('#selectPrograma').val();

                    if (fechaMuestra)
                    {
                        var formato = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/; //Primer filtro
                        //Se sabe que el formato de la fecha es el correcto, pero aún no se sabe si la misma es válida

                        if (fechaMuestra.match(formato))
                        {
                            //Test which seperator is used '/' or '-'
                            //Determina cuál es el separador utilizado
                            var opera1 = fechaMuestra.split('/');
                            var opera2 = fechaMuestra.split('-');
                            lopera1 = opera1.length;
                            lopera2 = opera2.length;

                            if (lopera1>1)
                                var pdate = fechaMuestra.split('/');
                            else
                                if (lopera2>1)
                                    var pdate = fechaMuestra.split('-');

                            // Extract the string into month, date and year
                            // Extrae el día, mes y año del string
                            var dd = parseInt(pdate[0]);
                            var mm  = parseInt(pdate[1]);
                            var yy = parseInt(pdate[2]);

                            // Create list of days of a month [assume there is no leap year by default]
                            // Crea la lista de días de los meses (se asume, por defecto, que no se trata de un año bisiesto)
                            var ListofDays = [31,28,31,30,31,30,31,31,30,31,30,31];
                            var fechaValida = true;

                            if (mm==1 || mm>2)
                            {
                                if (dd>ListofDays[mm-1])
                                    fechaValida = false;
                            }
                            else
                            {
                                if (mm==2)
                                {
                                    //Determina si es año bisiesto o no (leap year)
                                    var lyear = false;
                                    if ( (!(yy % 4) && yy % 100) || !(yy % 400) )
                                        lyear = true;

                                    if ( (lyear==false) && (dd>=29) || (lyear==true) && (dd>29) )
                                        fechaValida = false;
                                }
                            }

                            if (fechaValida)
                            {
                                if (idPrograma)
                                {
                                    //alert('Número de muestra: ' + numeroMuestra + '\nFecha de muestreo: ' + fechaMuestra + '\nID programa: ' + idPrograma);
                                    fechaMuestra = dd + "," + mm + "," + yy;

                                    var values = {};
                                    var gif = routesPedido.myroutes.gif;
                                    var url= routesPedido.myroutes.crearPremuestra.replace("0", numeroMuestra);
                                    url = url.replace("-1", fechaMuestra);
                                    url = url.replace("-2", idPrograma);

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
                                            if (response == 'No se puede crear la muestra, ya existe una con ese número')
                                                alert(response);
                                            else
                                                window.open(response, "_blank");

                                            //if (response != 'No se puede crear la muestra, ya existe una con ese número')
                                            //    location.reload();
                                        },
                                        complete: function(response){
                                            $.unblockUI();
                                        }
                                    });
                                }
                                else
                                    alert('No se seleccionó ningún programa');
                            }
                            else
                                alert('Ingrese una fecha de muestreo válida');
                        }
                        else
                            alert('Ingrese una fecha de muestreo válida');
                    }
                    else
                        alert('Ingrese una fecha de muestreo');
                }
                else
                    alert('Ingrese un número válido de muestra');
            }
            else
                alert('Ingrese un número de muestra');
        }
        else
            alert('Ingrese un número de muestra');
    }


    function validatedate (inputText)
    {
        var dateformat = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/;
        // Match the date format through regular expression
        if(inputText.value.match(dateformat))
        {
            document.form1.text1.focus();
            //Test which seperator is used '/' or '-'
            var opera1 = inputText.value.split('/');
            var opera2 = inputText.value.split('-');
            lopera1 = opera1.length;
            lopera2 = opera2.length;
            // Extract the string into month, date and year
            if (lopera1>1)
            {
                var pdate = inputText.value.split('/');
            }
            else if (lopera2>1)
            {
                var pdate = inputText.value.split('-');
            }
            var dd = parseInt(pdate[0]);
            var mm  = parseInt(pdate[1]);
            var yy = parseInt(pdate[2]);
            // Create list of days of a month [assume there is no leap year by default]
            var ListofDays = [31,28,31,30,31,30,31,31,30,31,30,31];
            if (mm==1 || mm>2)
            {
                if (dd>ListofDays[mm-1])
                {
                    alert('Invalid date format!');
                    return false;
                }
            }
            if (mm==2)
            {
                var lyear = false;
                if ( (!(yy % 4) && yy % 100) || !(yy % 400)) 
                {
                    lyear = true;
                }
                if ((lyear==false) && (dd>=29))
                {
                    alert('Invalid date format!');
                    return false;
                }
                if ((lyear==true) && (dd>29))
                {
                    alert('Invalid date format!');
                    return false;
                }
            }
        }
        else
        {
            alert("Invalid date format!");
            document.form1.text1.focus();
            return false;
        }
    }



    Array.prototype.clean = function( deleteValue ) {
      for ( var i = 0, j = this.length ; i < j; i++ ) {
        if ( this[ i ] == deleteValue ) {
          this.splice( i, 1 );
          i--;
        }
      }
      return this;
    };

    return {
        //main function to initiate the module
        init: function () {
        },

        autorizar: function () {
            autorizar();
        },

        anular: function (id) {
            anular(id);
        },

        excel: function (path) {
            excel(path);
        },

        eliminar: function (id) {
            eliminar(id);
        },

        confirmarAutorizacion: function (id) {
            confirmarAutorizacion(id);
        },

        premuestreo: function () {
            premuestreo();
        },

        crearPremuestra: function () {
            crearPremuestra();
        },
    };

}();

