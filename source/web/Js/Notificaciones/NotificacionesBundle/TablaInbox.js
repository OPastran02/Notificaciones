var TablaInbox = function () {

    var handleRecords = function (path) {

        var grid = new Datatable();

        grid.init({
            src: $("#TablaInbox"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
            },
            onDataLoad: function(grid) {
                jQuery(document).ready(function() {
                    $('.fecha-picker').datepicker({ dateFormat: "dd-mm-yy" });
                    $(".new-fecha-picker").on("change", function(element) {
                        var FechaInspeccion1= ConvertirStringToDate(element.target.value);

                        if(!FechaInspeccion1){
                            alert("Fecha Incorrecta, por favor utilice el calendario");
                            element.target.value='';
                        }
                    });

                    $(".new-fecha-picker").on("paste", function(e) {
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

                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
                // So when dropdowns used the scrollable div should be removed. 
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "serverSide": true,

                "pagingType": "simple_numbers",

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [10, 20, 50, 100, 150, 999999999],
                    [10, 20, 50, 100, 150, "Todos"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": path, // ajax source
                },
                "order": [
                    [1, "asc"]
                ],// set first column as a default sort by asc
                "aoColumnDefs": [
                        {"sName": "button", "aTargets": [ 0 ], 'bSortable' : true, "bSearchable": true},
                        {"sName": "text", "aTargets": [ 1 ], 'bSortable' : true, "bSearchable": true}, 
                        {"sName": "text", "aTargets": [ 2 ], 'bSortable' : true, "bSearchable": true},
                        {"sName": "text", "aTargets": [ 3 ], 'bSortable' : true, "bSearchable": true},  
                        {"sName": "text", "aTargets": [ 4 ], 'bSortable' : true, "bSearchable": true},
                        {"sName": "text", "aTargets": [ 5 ], 'bSortable' : true, "bSearchable": true},                                                             
                ]
            }
        });

        //grid.setAjaxParam("customActionType", "group_action");
        //grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    }

    return {

        //main function to initiate the module
        init: function (path) {

            handleRecords(path);
            //alert("asd");
        }

    };

}();

