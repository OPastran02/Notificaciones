var FormActions = function () {    

    var optionFilter = function () {

        // creamos la data, solo con el campo del pais,
        // ya que es el dato relevante en este caso.
        var data = $('#filtro').val();

        var values = $( "#formReportes" ).serializeArray();

        // Hacemos un envío del formulario, lo que ejecutará el evento preSubmit
        // del listener AddStateFieldSubscriber,
        // y actualizará el campo state, con los estados del pais seleccionado.
        
        $.ajax({
            url : routesReportes.myroutes.Seleccion,
            type: $( '#formReportes' ).attr( 'method' ),
            data : values,
            success: function(html) {
                //alert(html);
                // la variable html representa toda la página junto con el select de estados.
                // el cual tomamos y colocamos para reemplazar el select actual.
                //console.log($(html).find('#establecimiento_establecimientobundle_actuacion_option'));

                $('#form').html(html);
                
                //$('#option').replaceWith($(html).find('#establecimiento_establecimientobundle_actuacion_option'));
            }
        });
        
              
    }

    return {
        //main function to initiate the module
        init: function () {          

        },

        optionFilter: function(){
          optionFilter();
        },


    };

}();