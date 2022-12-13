var TablaMuestra = function () {

    var handleRecords = function (path) {

        var grid = new Datatable();

        grid.init({
            src: $("#TablaMuestra"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
                //console.log(grid.getDataTable().ajax.params().valueOf().columns);
            },
            onError: function (grid) {
                // execute some code on network or other general error  
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
                /*console.log(grid.getDataTable().column(1).header());
                console.log("a");*/
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
                    [10, 20, 50, 100, 150, 999999],
                    [10, 20, 50, 100, 150, "Todos"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": path, // ajax source
                },
                "order": [
                    [1, "desc"]
                ],// set first column as a default sort by asc
                "aoColumnDefs": [
                    {"sName": "sos0", "aTargets": [ 0 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos1", "aTargets": [ 1 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos2", "aTargets": [ 2 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos3", "aTargets": [ 3 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos4", "aTargets": [ 4 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos5", "aTargets": [ 5 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos6", "aTargets": [ 6 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos7", "aTargets": [ 7 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos8", "aTargets": [ 8 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos9", "aTargets": [ 9 ], 'bSortable' : true, "bSearchable": true},
                    {"sName": "sos10", "aTargets": [ 10 ], 'bSortable' : true, "bSearchable": true},
                ],
                responsive: true,

                colReorder: true,
            }
        });

        /*grid.getDataTable().on( 'column-reorder', function ( e, settings, details ) {
            var headerCell = $( grid.column( details.to ).header() );
         
            headerCell.addClass( 'reordered' );
         
            setTimeout( function () {
                headerCell.removeClass( 'reordered' );
            }, 2000 );
        } );    */

        //grid.setAjaxParam("customActionType", "group_action");
        grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    }

    return {

        //main function to initiate the module
        init: function (path) {

            handleRecords(path);
            //alert(path);
        }

    };

}();