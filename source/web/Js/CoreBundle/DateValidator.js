jQuery(document).ready(function() {

    $(".fecha-picker").on("change", function(element) {
        var FechaInspeccion1= ConvertirStringToDate(element.target.value);
    
        if(!FechaInspeccion1){
            alert("Fecha Incorrecta, por favor utilice el calendario");
            element.target.value='';
        }
    });

    $(".fecha-picker").on("paste", function(e) {
        e.preventDefault();
    });


    function ConvertirStringToDate(fechaString)
    {
        var fechas = fechaString.split('/');
        if (fechas.length != 3)
            fechas = fechaString.split('-');
        var d = fechas[0];
        var m = fechas[1];
        var y = fechas[2];
        return m > 0 && m < 13 && y > 0 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate();
    }

});