var TablaMisPedidos = function () {

    var handleRecords = function (path) {

        var grid = new Datatable();

        grid.init({
            src: $("#TablaMisPedidos"),
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
                    {"sName": "text", "aTargets": [ 6 ], 'bSortable' : true, "bSearchable": true},
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