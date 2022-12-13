var ReporteDiario = function () {    

    var eliminarOrden = function (id) {
        var confirmar = confirm("¿Esta seguro de eliminar la orden de inspección?");

        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.eliminarOrden.replace("0",id);
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
    }

    var guardarCheckList = function (id) {
        var confirmar = confirm("¿Esta seguro de guardar la orden de inspección?");

        if(confirmar){

          var checklist = $("#checklist"+id).val();

          console.log(checklist);

          var values = {};   
          var url= routestCheckList.myroutes.guardarCheckList.replace("0",id).replace("-1",checklist);
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
    }

    var anularCheckList = function (id) {
        var values = {};   
        var url= routestCheckList.myroutes.anularOrden.replace("0",id);
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
            $(".modal-body").html(response);
            $('#modalCheckList').modal('show');    
            $(".modal-title").html('Motivo de anulación y nota');            
          },
          complete: function(response){
              $.unblockUI();            
          }
        });        
    }

    var autorizarCheckList  = function (id) {
        var confirmar = confirm("¿Esta seguro de autorizar la orden de inspección?");

        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.autorizarCheckList.replace("0",id);
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
    }

    var anularCheckListSend = function () {
        var values = {};       

        var values = $( "#modalForm" ).serializeArray();
        var resp;
        
         $.ajax({
            type: $("#modalForm").attr( 'method' ),
            url: $("#modalForm").attr( 'action' ),
            data: values,
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                resp =  response;

                alert(resp);                
                $('#modalCheckList').modal('hide');
                
            },
            complete: function(response){
                $.unblockUI();            
            }
        });       
    }

    var editarCheckList = function (id) {
        var values = {};   
        var url= routestCheckList.myroutes.verOrden.replace("0",id);
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
            $(".modal-body").html(response);
            $('.fecha-picker').datepicker({ dateFormat: "dd-mm-yy" });                
            $(".modal-title").html("Editar Orden de Inspección");
            $('#modalCheckList').modal('show');   
          },
          complete: function(response){
              $.unblockUI();            
          }
        });        
    }    

    var editarCheckListSend = function () {
      //Esta funcion se utiliza para enviar los modales de la pagina pagecierre
        var values = {};       

        var values = $( "#modalForm" ).serializeArray();
        var resp;
        
         $.ajax({
            type: $("#modalForm").attr( 'method' ),
            url: $("#modalForm").attr( 'action' ),
            data: values,
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                resp =  response;

                if(resp.result == 'OK'){
                  alert(resp.details);                  
                }else if(resp.result == 'ERROR'){
                  alert(resp.details);
                }else{
                  $(".modal-body").html(response);
                }                
            },
            complete: function(response){
                $.unblockUI();            
            }
        });
    }

    var editarCierreCheckList = function (id,desvincular = 0) {
        var values = {};   
        var url= routestCheckList.myroutes.verCierre.replace("0",id) + "/"+desvincular;        
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
            $(".modal-body").html(response);            
            $('.fecha-picker').datepicker({ dateFormat: "dd-mm-yy" });
            $('.modal-footer').html('<button type="submit" data-toggle="modal" onClick="javascript:$('+"'"+'#modalForm'+"'"+').submit();"  class="btn btn-outline btn-circle blue" >Guardar</button><button type="button" data-dismiss="modal" class="btn btn-outline btn-circle dark">Cerrar</button>');
            $('#modalCheckList').modal('show');
          },
          complete: function(response){            
            $.unblockUI();            
          }
        });        
    }

    var enviarARevisionInspector = function (id) {
      var motivo = prompt("¿Esta seguro de enviar a revision la inspección?");
      if(motivo){        
        var values = {};   
        var url= routestCheckList.myroutes.enviarARevision.replace("0",id).replace("-1",motivo);        
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
    }

    var vincularCheckList = function (id) {
      var confirmar = confirm("¿Esta seguro de vincular la inspección?");
        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.buscarVincular.replace("0",id);
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
              resp =  response;

              if(resp.result == 'OK'){
                alert(resp.details);
                editarCierreCheckList(id);
              }else if(resp.result == 'ERROR'){
                alert(resp.details);
              }else if(resp.result == 'NOENCONTRADO'){
                vincularCheckListNuevoEstablecimiento(id);
              }else{                
                $(".modal-body").html(resp);
              }
            },
            complete: function(response){
                $.unblockUI();            
            }
          });
        }
    }

    var vincularCheckListNuevoEstablecimiento = function (id) {
      var confirmar = confirm("El establecimiento no fue encontrado, se creara uno nuevo, ACEPTE PARA CREARLO");
        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.vincularNuevo.replace("0",id);
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
              resp =  response;

              if(resp == 'OK'){
                alert(resp);
                editarCierreCheckList(id);
              }else{
                alert(resp);
              }
            },
            complete: function(response){
                $.unblockUI();            
            }
          });
        }
    }

    var vincularCheckListNuevoEstablecimientoManual = function (id) {
      var confirmar = confirm("¿Esta seguro de CREAR UN NUEVO ESTABLECIMIENTO?");
        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.vincularNuevo.replace("0",id);
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
              resp =  response;

              if(resp == 'OK'){
                alert(resp);
                editarCierreCheckList(id);
              }else{
                alert(resp);
              }
            },
            complete: function(response){
                $.unblockUI();            
            }
          });
        }
    }

    var vincularCheckListEstablecimiento = function (id,establecimiento) {
      var confirmar = confirm("¿Esta seguro de vincular la orden de inspección con el establecimiento?");
        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.vincularEstablecimiento.replace("0",id).replace("-1",establecimiento);
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
              resp =  response;

              if(resp == 'OK'){
                alert(resp);
                editarCierreCheckList(id);
              }else{
                alert(resp);
              }
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
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                const link=document.createElement('a');
                link.href=window.URL.createObjectURL(response);
                link.download=Date.now()+".xls";
                link.click();
            },
            complete: function(response){
                $.unblockUI();            
            }
        });

    }

    var reInspeccionCheckList = function (id) {
        var values = {};   
        var url= routestCheckList.myroutes.reInspeccion.replace("0",id);
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
            $(".modal-body").html(response);
            $('#modalCheckList').modal('show');    
            $(".modal-title").html('Motivo de reinspeccion');            
          },
          complete: function(response){
              $.unblockUI();            
          }
        });        
    }

    var reInspeccionCheckListSend = function () {
        var values = {};        
        var values = $( "#modalForm" ).serializeArray();
        var resp;
        
          $.ajax({
            type: $("#modalForm").attr( 'method' ),
            url: $("#modalForm").attr( 'action' ),
            data: values,
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                resp =  response;

                alert(resp);                
                $('#modalCheckList').modal('hide');
                
            },
            complete: function(response){
                $.unblockUI();            
            }
        });     
    }

    var desestimarReInspeccion = function (id) {
        var confirmar = confirm("¿Esta seguro de desistimar la re-inspección?");

        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.desestimar.replace("0",id);
          var url2= routestCheckList.myroutes.back;
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
              window.location.replace(url2);
            }
          });
        }
    }

    var cerrarInspeccionesAutomaticamente = function (id) {
        var confirmar = confirm("¿Esta seguro de cerrar las ordenes de inspección automaticamente?");

        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.cierreAutomatico;
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
    }

    var verObservacionesMotivoInspeccion = function (id) {        
        var values = {};
        var url= routestCheckList.myroutes.verObservaciones.replace("0",id);
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

    var reprogramar = function (id) {
        var values = {};   
        var url= routestCheckList.myroutes.reprogramar.replace("-1",id);
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
            $(".modal-body").html(response);
            $('#modalCheckList').modal('show');    
            $(".modal-title").html("Editar Orden de Inspección");        
          },
          complete: function(response){
              $.unblockUI();            
          }
        });        
    }

    var reprogramarSend = function () {
      //Esta funcion se utiliza para enviar los modales de la pagina pagecierre
        var values = {};       

        var values = $( "#modalForm" ).serializeArray();
        var resp;
        
         $.ajax({
            type: $("#modalForm").attr( 'method' ),
            url: $("#modalForm").attr( 'action' ),
            data: values,
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){                
                $('#modalCheckList').modal('hide');
                alert(response);                            
            },
            complete: function(response){
                $.unblockUI();            
            }
        });
    }

    var subirInspeccionFirmada = function (id) {
        var values = {};   
        var url= routestCheckList.myroutes.subirArchivos.replace("0",id);
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
            $(".modal-body").html(response);
            $('#modalCheckList').modal('show');    
            $(".modal-title").html('SUBIR INSPECCION FIRMADA Y COMPLETAR EL CAMPO DE INFORME GRÁFICO');            
          },
          complete: function(response){
              $.unblockUI();            
          }
        });        
    }

    var subirInspeccionFirmadaSend = function () {      
        var values = {};       

        var values = new FormData($('#modalForm')[0]);
        var resp;
        
         $.ajax({
            type: $("#modalForm").attr( 'method' ),
            url: $("#modalForm").attr( 'action' ),
            data: values,
            processData: false,
            contentType: false,
            beforeSend : function (){
             $.blockUI({
                    message: '<div class="page-loading"><img src="assets/img/loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>Cargando...</span></div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){                
                $('#modalCheckList').modal('hide');
                alert(response);                            
            },
            complete: function(response){
                $.unblockUI();            
            }
        });
    }

    var vincularifgra = function (id) {        
        var values = {};
        var url= routestCheckList.myroutes.vincular.replace("0",id);
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

    var rechazar = function (id) {
        var confirmar = confirm("¿Esta seguro de rechazar la orden de inspección?");

        if(confirmar){
          var values = {};
          var url= routestCheckList.myroutes.rechazar.replace("0",id);
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
    }

    return {
        //main function to initiate the module
        init: function () {
          
        },

        excel: function (path) {
            excel(path);
        },

        eliminarOrden: function (id) {
            eliminarOrden(id);
        },

        guardarCheckList: function (id) {
            guardarCheckList(id);
        },

        anularCheckList: function (id) {
            anularCheckList(id);
        },

        anularCheckListSend: function () {
            anularCheckListSend();
        },

        editarCheckList: function (id) {
            editarCheckList(id);
        },

        editarCheckListSend: function () {
            editarCheckListSend();
        },

        editarCierreCheckList: function (id,desvincular = 0) {
            editarCierreCheckList(id,desvincular);
        },

        vincularCheckList: function (id) {
            vincularCheckList(id);
        },

        vincularCheckListEstablecimiento: function (id,establecimiento) {
            vincularCheckListEstablecimiento(id,establecimiento);
        },

        vincularCheckListNuevoEstablecimientoManual: function (id) {
            vincularCheckListNuevoEstablecimientoManual(id);
        },

        autorizarCheckList: function (id) {
          autorizarCheckList (id);
        },

        reInspeccionCheckList: function (id) {
          reInspeccionCheckList (id);
        },

        reInspeccionCheckListSend: function () {
          reInspeccionCheckListSend ();
        },

        desestimarReInspeccion: function (id) {
          desestimarReInspeccion (id);
        },
        cerrarInspeccionesAutomaticamente: function () {
          cerrarInspeccionesAutomaticamente ();
        },
        
        verObservacionesMotivoInspeccion: function (id) {
          verObservacionesMotivoInspeccion(id);
        },

        enviarARevisionInspector: function (id) {
          enviarARevisionInspector(id);
        },        

        reprogramar: function (id) {
            reprogramar(id);
        },

        reprogramarSend: function () {
            reprogramarSend();
        },

        subirInspeccionFirmada: function (id) {
            subirInspeccionFirmada(id);
        },

        subirInspeccionFirmadaSend: function () {
            subirInspeccionFirmadaSend();
        },

        vincularifgra: function (id) {
            vincularifgra(id);
        },

        rechazar: function (id) {
            rechazar(id);
        },
    };

}();

