var TablaEstablecimiento = function () {

    var handleRecords = function (path) {

        var grid = new Datatable();

        grid.init({
            src: $("#TablaEstablecimiento"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Cargando...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                "serverSide": true,

                "pagingType": "simple_numbers",

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [5, 20, 50, 100, 150, 99999999999],
                    [5, 20, 50, 100, 150, "Todos"] // change per page values here
                ],
                "pageLength": 5, // default record count per page
                "ajax": {
                    "url": path, // ajax source
                },
                "order": [
                    [1, "asc"]
                ],// set first column as a default sort by asc
                "columnDefs": [
                        {"sWidth": "10%","sName": "Accion", "aTargets": [ 0 ]},
                        {"sWidth": "10%","sName": "Id", "aTargets": [ 1 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "10%","sName": "Estado", "aTargets": [ 2 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "10%","sName": "Rubro Principal", "aTargets": [ 3 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "10%","sName": "Rubros", "aTargets": [ 4 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "5%","sName": "Direcciones", "aTargets": [ 5 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "10","sName": "Comunas", "aTargets": [ 6 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "10%","sName": "Actuaciones", "aTargets": [ 7 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "10%","sName": "Razon Social", "aTargets": [ 8 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "5%","sName": "CUIT", "aTargets": [ 9 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "5%","sName": "SMP", "aTargets": [ 10 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "5%","sName": "Favorito", "aTargets": [ 11 ], 'bSortable' : true, "bSearchable": true},
                        {"sWidth": "5%","sName": "Cmr", "aTargets": [ 12 ], 'bSortable' : true, "bSearchable": true},
                ],
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

