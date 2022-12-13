var Protocolo = function () {

    var protocoloIntermedio = function (idMuestra, idArea) {
        var url = rutaProtocoloIntermedio.replace("0", idMuestra);
        url = url.replace("-1", idArea);
        window.open(url, "_blank");
    }

    var protocolo = function (idMuestra, ausenciaGerente) {
        var url = rutaProtocolo.replace("0", idMuestra);
        var varBool = ausenciaGerente ? 1 : 0;
        url = url.replace("-1", varBool);
        window.open(url, "_blank");
    }

    return {
        //main function to initiate the module
        init: function () {

        },

        protocoloIntermedio: function (idMuestra, idArea) {
            protocoloIntermedio(idMuestra, idArea);
        },

        protocolo: function (idMuestra, ausenciaGerente = false) {
            protocolo(idMuestra, ausenciaGerente);
        }

    };

}();