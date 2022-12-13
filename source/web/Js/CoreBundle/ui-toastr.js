var UIToastr = function () {

    return {
        
        init: function () {

            var exitos = document.getElementsByClassName('successFlashMessage');
            for(var i = 0; i < exitos.length; i++)
            {
            	var shortCutFunction = 'success';
                var msg = exitos[i].value;
                var title = '';

                toastr.options.showDuration = 1000;
                toastr.options.hideDuration = 1000;
                toastr.options.timeOut = 20000;
                toastr.options.extendedTimeOut = 1000;
                toastr.options.showEasing = 'swing';
                toastr.options.hideEasing = 'linear';
                toastr.options.showMethod = 'fadeIn';
                toastr.options.hideMethod = 'fadeOut';

                toastr.options = {
                    closeButton: true,
                    debug: false,
                    positionClass: 'toast-top-right',
                    onclick: null
                };

                var $toast = toastr[shortCutFunction](msg, title);
            }

            var advertencias = document.getElementsByClassName('alertFlashMessage');
            for(var i = 0; i < advertencias.length; i++)
            {
            	var shortCutFunction = 'error';
                var msg = advertencias[i].value;
                var title = '';

                toastr.options.showDuration = 1000;
                toastr.options.hideDuration = 1000;
                toastr.options.timeOut = 20000;
                toastr.options.extendedTimeOut = 1000;
                toastr.options.showEasing = 'swing';
                toastr.options.hideEasing = 'linear';
                toastr.options.showMethod = 'fadeIn';
                toastr.options.hideMethod = 'fadeOut';

                toastr.options = {
                    closeButton: true,
                    debug: false,
                    positionClass: 'toast-top-right',
                    onclick: null
                };

                var $toast = toastr[shortCutFunction](msg, title);
            }

            var errores = document.getElementsByClassName('errorFlashMessage');
            for(var i = 0; i < errores.length; i++)
            {
            	var shortCutFunction = 'error';
                var msg = errores[i].value;
                var title = '';

                toastr.options.showDuration = 1000;
                toastr.options.hideDuration = 1000;
                toastr.options.timeOut = 20000;
                toastr.options.extendedTimeOut = 1000;
                toastr.options.showEasing = 'swing';
                toastr.options.hideEasing = 'linear';
                toastr.options.showMethod = 'fadeIn';
                toastr.options.hideMethod = 'fadeOut';

                toastr.options = {
                    closeButton: true,
                    debug: false,
                    positionClass: 'toast-top-right',
                    onclick: null
                };

                var $toast = toastr[shortCutFunction](msg, title);
            }

        }

    };

}();

jQuery(document).ready(function() {    
   UIToastr.init();
});