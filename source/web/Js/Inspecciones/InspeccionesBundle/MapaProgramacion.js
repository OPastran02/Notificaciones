var MapaProgramacion = function () {

    var comunas = [];
    var acumar = [];

    var filtros = [];

    //ARRAYS QUE VAN A CONTENER LOS MARCADORES
    var programadas = [];
    var reinspeccionar = [];
    var reprogramadas = [];
    var automaticas = [];
    var nuncaInspeccionado = [];
    /* rubros ↓ */
    var sinRubro = [];
    var estServicioGNC = [];
    var estServicioDual = [];
    var establecimientosGubernamentales = [];
    var hoteles = [];
    var supermercados = [];
    var fabricas = [];
    var acopios = [];
    var transportes = [];
    var comercios = [];
    var salud = [];
    var clubes = [];
    var frigorificos = [];
    var depositos = [];
    var galvanoplastias = [];
    var casasExpendioComida = [];
    var talleres = [];
    var laboratorios = [];
    var industrias = [];
    var curtiembres = [];
    var lavaderos = [];
    var lavaderosIndustriales = [];
    var tintorerias = [];
    var centrosComerciales = [];
    var centrosEducativos = [];
    var rubroFuentesMoviles = [];
    var imprentas = [];
    var geriatricos = [];
    var empresasServiciosPublicos = [];
    var elaboracionAlimentosBebidas = [];
    var antenas = [];
    var industriasTextiles = [];
    var petroquimicas = [];
    var viviendas = [];
    var sinActividad = [];
    var espacioPublico = [];
    var obras = [];
    var productorasCanales = [];
    var talleresOficiosVarios = [];
    var estServicioLiquido = [];
    var oficinas = [];
    var eventos = [];
    var garages = [];
    var metalurgicas = [];
    var lavanderiasMecanicas = [];
    var consorcios = [];
    var localGrande = [];
    var localPequenio = [];
    /* tiposCedula ↓ */
    var avus = [];
    var caa = [];
    var cedulaCitacion = [];
    var certificadoLimpiezaTanqueAgua = [];
    var certificadoDesinfeccion = [];
    var clubDeportivo = [];
    var dget = [];
    var efluentes = [];
    var empresas = [];
    var fuentesMoviles = [];
    var intimacionInmediata=[]; 
    var matafuegos = [];
    var nocturnidad = [];
    var notificaciones = [];
    var olores = [];
    var otros = [];
    var personal = [];
    var predio = [];
    var productoresEventos = [];
    var productoras = [];
    var prorroga = [];
    var rac = [];
    var patogenicos = [];
    var peligrosos = [];
    var ropaTrabajo = [];
    var ropaHospitalaria = [];
    var ruidos = [];
    var seguroAmbOblig = [];
    var sitiosContaminados = [];
    var suacis = [];
    var superCedulas = [];


    function Filtro(id, nombre)
    {
        this.id = id;
        this.nombre = nombre;
    }


    var initMap = function () {
        var mapa = new GMaps({
            div: '#gmap',
            lat: -34.61,
            lng: -58.45,
            zoom: 12
        });


        //MESES ↓

        var filtrosSeleccionados = [];

        $("#single-prepend-text").change(function() {
            $("#multi-append option:selected").each(function() {
                //filtrosSeleccionados.push($(this).text());
                var i = $(this).val();
                var n = $(this).text();
                var filtro = new Filtro(i, n);
                filtrosSeleccionados.push(filtro);
            });

            //Volver a cargar todos los filtros
            for (var i = 0; i < filtrosSeleccionados.length; i++)
                mostrarEsconder(mapa, filtrosSeleccionados[i], mesSeleccionado(), true);
        });


        //FILTROS ↓

        //Capturar el evento cuando el 'select' de filtros sufre un cambio
        $("#multi-append").change(function () {
            var filtrosChange = [];

            //Volver a recorrer todo el 'select' de filtros para encontrar el filtro que sufrió el cambio
            $("#multi-append option:selected").each(function() {
                //filtrosChange.push($(this).text());
                var i = $(this).val();
                var n = $(this).text();
                var filtro = new Filtro(i, n);
                filtrosChange.push(filtro);
            });

            //Comparar el antes y el después del cambio sufrido en el 'select'
            if(filtros.length < filtrosChange.length)
            {
                //SE AGREGÓ UN FILTRO ↓

                if(filtros.length == 0)
                    mostrarEsconder(mapa, filtrosChange[0], mesSeleccionado(), false);
                else
                {
                    var i = 0;
                    while(incluye(filtros, filtrosChange[i]))
                        i++;

                    mostrarEsconder(mapa, filtrosChange[i], mesSeleccionado(), false);
                }
            }
            else
            {
                //SE QUITÓ UN FILTRO ↓

                mapa.removeMarkers();

                for (var i = 0; i < filtrosChange.length; i++) {
                    mostrarEsconder(mapa, filtrosChange[i], mesSeleccionado(), false);
                }
            }

            filtros = filtrosChange;
        });


        //FILTRAR POR DIRECCIÓN (SIN ESTABLECIMIENTO) ↓

        $("#buscarDireccion").click(function() {            
            var idCalle = $("#single").val();
            var altura = $("#a").val();
            var piso = $("#p").val();
            var dpto = $("#d").val();
            var local = $("#l").val();

            normalizarDireccion(mapa, idCalle, altura);
        });


        //KML

        aniadirAcumar(mapa);
        aniadirComuna(mapa);

        /*$("#kmlcomunas").click(function() {
            kmlComunas(mapa);
        });

        $("#kmlacumar").click(function() {
            kmlAcumar(mapa);
        });*/
    }


    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/


    var incluye = function(incluidor, elementoIncluido)
    {
        var i = 0;
        var nombreElementoBuscado = elementoIncluido.nombre;
        var nombreElementoActual = incluidor[i].nombre;

        while((i < incluidor.length) && (nombreElementoActual != nombreElementoBuscado))
        {
            nombreElementoActual = incluidor[i].nombre;
            i++;
        }

        if(nombreElementoActual == nombreElementoBuscado)
            return true;
        else
            return false;
    }


    var mesSeleccionado = function()
    {
        var valor = $("#single-prepend-text option:selected").val();

        if((valor == undefined) || (valor == ''))
            return moment().format('YYYY-MM-DD');
        else
            return valor;
    }


    //INICIO OPCIONES select ↓


    var mesesSelect = function()
    {
        var opciones = '<option></option>';
        var anio = 2015;
        var eneroDelVeinteQuince = moment('2015-01-01');
        var fechaActual = moment();

        var diferenciaDeMeses = fechaActual.diff(eneroDelVeinteQuince, 'months');

        if((diferenciaDeMeses % 12) == 0)
        {
            for (var i = ((diferenciaDeMeses/12)-1); i >= 0; i--) {
                anio += i;
                opciones += '<optgroup label="' + anio + '">';

                for (var j = 1; j <= 12; j++) {
                    opciones += '<option value="' + anio + '-' + j + '-1">';
                    opciones += dameMes(j) + ' ' + anio;
                    opciones += '</option>';
                }

                opciones += '</optgroup>';
                anio = 2015;
            }
        }
        else
        {
            var mesesDeAniosTerminados = diferenciaDeMeses - (diferenciaDeMeses % 12);
            anio += (mesesDeAniosTerminados/12);
            opciones += '<optgroup label="' + anio + '">';

            for (var i = 1; i <= (diferenciaDeMeses % 12); i++) {
                opciones += '<option value="' + anio + '-' + i + '-1">';
                opciones += dameMes(i) + ' ' + anio;
                opciones += '</option>';
            }
            opciones += '</optgroup>';

            //MESES DE AÑOS TERMINADOS
            anio = 2015;
            for (var i = ((mesesDeAniosTerminados/12)-1); i >= 0; i--) {
                anio += i;
                opciones += '<optgroup label="' + anio + '">';

                for (var j = 1; j <= 12; j++) {
                    opciones += '<option value="' + anio + '-' + j + '-1">';
                    opciones += dameMes(j) + ' ' + anio;
                    opciones += '</option>';
                }

                opciones += '</optgroup>';
                anio = 2015;
            }
        }

        $("#single-prepend-text").html(opciones);
    }


    var dameMes = function(numeroMes)
    {
        switch(numeroMes)
        {
            case 1:
                return 'Enero';
                break;
            case 2:
                return 'Febrero';
                break;
            case 3:
                return 'Marzo';
                break;
            case 4:
                return 'Abril';
                break;
            case 5:
                return 'Mayo';
                break;
            case 6:
                return 'Junio';
                break;
            case 7:
                return 'Julio';
                break;
            case 8:
                return 'Agosto';
                break;
            case 9:
                return 'Septiembre';
                break;
            case 10:
                return 'Octubre';
                break;
            case 11:
                return 'Noviembre';
                break;
            case 12:
                return 'Diciembre';
                break;
        }
    }


    var rubrosSelect = function()
    {
        var values = {};
        var url = routes.select.rubrosPrincipales;
        var gif = routes.map.gif;

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
                var opciones = "";

                for (var i = 0; i < response.length; i++) {
                    opciones += '<option value="' + response[i].id + '">';
                    opciones += response[i].rubroPrincipal;
                    opciones += '</option>';
                }
                $("#rubros").html(opciones);
            },
            complete: function(response){
                $.unblockUI();            
            }
        });
    }


    var cedulasVencidasSelect = function()
    {
        var values = {};
        var url = routes.select.tiposCedulas;
        var gif = routes.map.gif;

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
                var opciones = "";

                for (var i = 0; i < response.length; i++) {
                    opciones += '<option value="' + response[i].id + '">';
                    opciones += response[i].tipoCedula;
                    opciones += '</option>';
                }
                $("#cedulasVencidas").html(opciones);
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    var callesSelect = function()
    {
        var values = {};
        var url = routes.select.calles;
        var gif = routes.map.gif;

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
                var opciones = "";

                for (var i = 0; i < response.length; i++) {
                    opciones += '<option value="' + response[i].id + '">';
                    opciones += response[i].calle;
                    opciones += '</option>';
                }
                $("#single").html(opciones);
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    //FIN OPCIONES select ↑


    //INICIO DATOS MAPA ↓


    var queArrayGet = function(nombre)
    {
        switch(nombre)
        {
            //Rubros
            case 'Sin Rubro':
                return sinRubro;
                break;
            case 'Estación de Servicios GNC':
                return estServicioGNC;
                break;
            case 'Estación de Servicios DUAL':
                return estServicioDual;
                break;
            case 'Establecimientos gubernamentales':
                return establecimientosGubernamentales;
                break;
            case 'Hoteles-Hostels':
                return hoteles;
                break;
            case 'Supermercado':
                return supermercados;
                break;
            case 'Fábrica':
                return fabricas;
                break;
            case 'Acopio combustibles-sustancias peligrosas':
                return acopios;
                break;
            case 'Transporte':
                return transportes;
                break;
            case 'Comercio MAY-MIN':
                return comercios;
                break;
            case 'Sanidad Y Salud':
                return salud;
                break;
            case 'Club':
                return clubes;
                break;
            case 'Frigorificos':
                return frigorificos;
                break;
            case 'Depositos':
                return depositos;
                break;
            case 'Galvanoplastía':
                return galvanoplastias;
                break;
            case 'Casas expendio de comida':
                return casasExpendioComida;
                break;
            case 'Mantenimiento automotor':
                return talleres;
                break;
            case 'Laboratorio-drogerias -no farmacias-':
                return laboratorios;
                break;
            case 'Industria quimica':
                return industrias;
                break;
            case 'Curtiembre':
                return curtiembres;
                break;
            case 'Lavadero de vehiculos':
                return lavaderos;
                break;
            case 'Lavadero industrial':
                return lavaderosIndustriales;
                break;
            case 'Tintorería':
                return tintorerias;
                break;
            case 'Centro comercial':
                return centrosComerciales;
                break;
            case 'Centros educativos':
                return centrosEducativos;
                break;
            case 'Fuentes moviles':
                return rubroFuentesMoviles;
                break;
            case 'Imprenta':
                return imprentas;
                break;
            case 'Geriatrico':
                return geriatricos;
                break;
            case 'Empresas de servicios publicos':
                return empresasServiciosPublicos;
                break;
            case 'Elaboracion de alimentos-bebidas':
                return elaboracionAlimentosBebidas;
                break;
            case 'Antenas':
                return antenas;
                break;
            case 'Industria textil':
                return industriasTextiles;
                break;
            case 'Petroquimica':
                return petroquimicas;
                break;
            case 'Vivienda':
                return viviendas;
                break;
            case 'Sin actividad':
                return sinActividad;
                break;
            case 'Espacio público':
                return espacioPublico;
                break;
            case 'Obra edilicia-infraestructura urbana':
                return obras;
                break;
            case 'Productoras-canales TV':
                return productorasCanales;
                break;
            case 'Talleres oficios varios':
                return talleresOficiosVarios;
                break;
            case 'Estación de Servicios Con Líquido':
                return estServicioLiquido;
                break;
            case 'Oficina':
                return oficinas;
                break;
            case 'Eventos':
                return eventos;
                break;
            case 'Garage':
                return garages;
                break;
            case 'Metalurgica':
                return metalurgicas;
                break;
            case 'Lavanderia Mecanica':
                return lavanderiasMecanicas;
                break;
            case 'Consorcio':
                return consorcios;
                break;
            case 'Local Grande Indeterminado':
                return localGrande;
                break;
            case 'Local Pequeño Indeterminado':
                return localPequenio;
                break;
            //Cédulas
            case 'AVUS':
                return avus;
                break;
            case 'CAA':
                return caa;
                break;
            case 'CEDULA DE CITACION':
                return cedulaCitacion;
                break;
            case 'CERFITICADO LIMPIEZA de TANQUE de AGUA':
                return certificadoLimpiezaTanqueAgua;
                break;
            case 'CERTIFICADO DESINFECCION':
                return certificadoDesinfeccion;
                break;
            case 'CLUB DEPORTIVO':
                return clubDeportivo;
                break;
            case 'DGET':
                return dget;
                break;
            case 'EFLUENTES':
                return efluentes;
                break;
            case 'EMPRESAS DE FUMIGACION/TANQUES':
                return empresas;
                break;
            case 'FUENTES MOVILES':
                return fuentesMoviles;
                break;
            case 'INTIMACION-INMEDIATA':
                return intimacionInmediata;
                break;
            case 'MATAFUEGOS':
                return matafuegos;
                break;
            case 'NOCTURNIDAD':
                return nocturnidad;
                break;
            case 'NOTIFICACIONES':
                return notificaciones;
                break;
            case 'OLORES':
                return olores;
                break;
            case 'OTROS':
                return otros;
                break;
            case 'PERSONAL':
                return personal;
                break;
            case 'PREDIO':
                return predio;
                break;
            case 'PROCTORES EVENTOS':
                return productoresEventos;
                break;
            case 'PRODUCTORAS':
                return productoras;
                break;
            case 'PRORROGA':
                return prorroga;
                break;
            case 'RAC':
                return rac;
                break;
            case 'RES. PATOGENICOS':
                return patogenicos;
                break;
            case 'RES. PELIGROSOS.':
                return peligrosos;
                break;
            case 'ROPA DE TRABAJO':
                return ropaTrabajo;
                break;
            case 'ROPA HOSPITALARIA':
                return ropaHospitalaria;
                break;
            case 'RUIDOS':
                return ruidos;
                break;
            case 'SEGURO AMB. OBLIG':
                return seguroAmbOblig;
                break;
            case 'SITIOS CONTAMINADOS':
                return sitiosContaminados;
                break;
            case 'SUACI':
                return suacis;
                break;
            case 'SUPERMERCADO':
                return superCedulas;
                break;
            //----
            default:
                return undefined;
                break;
        }
    }


    var queArraySet = function(nombre, marcadores)
    {
        switch(nombre)
        {
            //Rubros
            case 'Sin Rubro':
                sinRubro = marcadores;
                break;
            case 'Estación de Servicios GNC':
                estServicioGNC = marcadores;
                break;
            case 'Estación de Servicios DUAL':
                estServicioDual = marcadores;
                break;
            case 'Establecimientos gubernamentales':
                establecimientosGubernamentales = marcadores;
                break;
            case 'Hoteles-Hostels':
                hoteles = marcadores;
                break;
            case 'Supermercado':
                supermercados = marcadores;
                break;
            case 'Fábrica':
                fabricas = marcadores;
                break;
            case 'Acopio combustibles-sustancias peligrosas':
                acopios = marcadores;
                break;
            case 'Transporte':
                transportes = marcadores;
                break;
            case 'Comercio MAY-MIN':
                comercios = marcadores;
                break;
            case 'Sanidad Y Salud':
                salud = marcadores;
                break;
            case 'Club':
                clubes = marcadores;
                break;
            case 'Frigorificos':
                frigorificos = marcadores;
                break;
            case 'Depositos':
                depositos = marcadores;
                break;
            case 'Galvanoplastía':
                galvanoplastias = marcadores;
                break;
            case 'Casas expendio de comida':
                casasExpendioComida = marcadores;
                break;
            case 'Mantenimiento automotor':
                talleres = marcadores;
                break;
            case 'Laboratorio-drogerias -no farmacias-':
                laboratorios = marcadores;
                break;
            case 'Industria quimica':
                industrias = marcadores;
                break;
            case 'Curtiembre':
                curtiembres = marcadores;
                break;
            case 'Lavadero de vehiculos':
                lavaderos = marcadores;
                break;
            case 'Lavadero industrial':
                lavaderosIndustriales = marcadores;
                break;
            case 'Tintorería':
                tintorerias = marcadores;
                break;
            case 'Centro comercial':
                centrosComerciales = marcadores;
                break;
            case 'Centros educativos':
                centrosEducativos = marcadores;
                break;
            case 'Fuentes moviles':
                rubroFuentesMoviles = marcadores;
                break;
            case 'Imprenta':
                imprentas = marcadores;
                break;
            case 'Geriatrico':
                geriatricos = marcadores;
                break;
            case 'Empresas de servicios publicos':
                empresasServiciosPublicos = marcadores;
                break;
            case 'Elaboracion de alimentos-bebidas':
                elaboracionAlimentosBebidas = marcadores;
                break;
            case 'Antenas':
                antenas = marcadores;
                break;
            case 'Industria textil':
                industriasTextiles = marcadores;
                break;
            case 'Petroquimica':
                petroquimicas = marcadores;
                break;
            case 'Vivienda':
                viviendas = marcadores;
                break;
            case 'Sin actividad':
                sinActividad = marcadores;
                break;
            case 'Espacio público':
                espacioPublico = marcadores;
                break;
            case 'Obra edilicia-infraestructura urbana':
                obras = marcadores;
                break;
            case 'Productoras-canales TV':
                productorasCanales = marcadores;
                break;
            case 'Talleres oficios varios':
                talleresOficiosVarios = marcadores;
                break;
            case 'Estación de Servicios Con Líquido':
                estServicioLiquido = marcadores;
                break;
            case 'Oficina':
                oficinas = marcadores;
                break;
            case 'Eventos':
                eventos = marcadores;
                break;
            case 'Garage':
                garages = marcadores;
                break;
            case 'Metalurgica':
                metalurgicas = marcadores;
                break;
            case 'Lavanderia Mecanica':
                lavanderiasMecanicas = marcadores;
                break;
            case 'Consorcio':
                consorcios = marcadores;
                break;
            case 'Local Grande Indeterminado':
                localGrande = marcadores;
                break;
            case 'Local Pequeño Indeterminado':
                localPequenio = marcadores;
                break;
            //Cédulas
             case 'INTIMACION-INMEDIATA':
                intimacionInmediata = marcadores;
                break;
            case 'AVUS':
                avus = marcadores;
                break;
            case 'CAA':
                caa = marcadores;
                break;
            case 'CEDULA DE CITACION':
                cedulaCitacion = marcadores;
                break;
            case 'CERFITICADO LIMPIEZA de TANQUE de AGUA':
                certificadoLimpiezaTanqueAgua = marcadores;
                break;
            case 'CERTIFICADO DESINFECCION':
                certificadoDesinfeccion = marcadores;
                break;
            case 'CLUB DEPORTIVO':
                clubDeportivo = marcadores;
                break;
            case 'DGET':
                dget = marcadores;
                break;
            case 'EFLUENTES':
                efluentes = marcadores;
                break;
            case 'EMPRESAS DE FUMIGACION/TANQUES':
                empresas = marcadores;
                break;
            case 'FUENTES MOVILES':
                fuentesMoviles = marcadores;
                break;
            case 'MATAFUEGOS':
                matafuegos = marcadores;
                break;
            case 'NOCTURNIDAD':
                nocturnidad = marcadores;
                break;
            case 'NOTIFICACIONES':
                notificaciones = marcadores;
                break;
            case 'OLORES':
                olores = marcadores;
                break;
            case 'OTROS':
                otros = marcadores;
                break;
            case 'PERSONAL':
                personal = marcadores;
                break;
            case 'PREDIO':
                predio = marcadores;
                break;
            case 'PROCTORES EVENTOS':
                productoresEventos = marcadores;
                break;
            case 'PRODUCTORAS':
                productoras = marcadores;
                break;
            case 'PRORROGA':
                prorroga = marcadores;
                break;
            case 'RAC':
                rac = marcadores;
                break;
            case 'RES. PATOGENICOS':
                patogenicos = marcadores;
                break;
            case 'RES. PELIGROSOS.':
                peligrosos = marcadores;
                break;
            case 'ROPA DE TRABAJO':
                ropaTrabajo = marcadores;
                break;
            case 'ROPA HOSPITALARIA':
                ropaHospitalaria = marcadores;
                break;
            case 'RUIDOS':
                ruidos = marcadores;
                break;
            case 'SEGURO AMB. OBLIG':
                seguroAmbOblig = marcadores;
                break;
            case 'SITIOS CONTAMINADOS':
                sitiosContaminados = marcadores;
                break;
            case 'SUACI':
                suacis = marcadores;
                break;
            case 'SUPERMERCADO':
                superCedulas = marcadores;
                break;
            //----
            default:
                break;
        }
    }


    var queIcono = function(nombre, id)
    {
        switch(nombre)
        {
            case 'Programadas':
                return routes.icons.programadas;
                break;
            case 'Reinspeccionar':
                return routes.icons.reinspeccionar;
                break;
            case 'Reprogramar':            
                return routes.icons.reprogramadas;
                break;
            case 'Automáticas':
                return traerIconoRubro(id);
                break;
            case 'Rubro':
                return traerIconoRubro(id);
                break;
            case 'Cédula':
                return traerIconoCedula();
                break;
            case 'intimacionInmediata':
                return routes.icons.cedula;
                break;
            default:
                console.log('undefined icon');
                break;
        }
    }


    var traerIconoRubro = function(id)
    {
        switch(id)
        {
            case '1':
                return routes.icons.sinRubro;
                break;
            case '2':
                return routes.icons.estServicioGNC;
                break;
            case '3':
                return routes.icons.estServicioDual;
                break;
            case '5':
                return routes.icons.establecimientosGubernamentales;
                break;
            case '6':
                return routes.icons.hoteles;
                break;
            case '7':
                return routes.icons.supermercados;
                break;
            case '8':
                return routes.icons.fabricas;
                break;
            case '9':
                return routes.icons.acopios;
                break;
            case '10':
                return routes.icons.transportes;
                break;
            case '11':
                return routes.icons.comercios;
                break;
            case '12':
                return routes.icons.salud;
                break;
            case '13':
                return routes.icons.clubes;
                break;
            case '14':
                return routes.icons.frigorificos;
                break;
            case '15':
                return routes.icons.depositos;
                break;
            case '17':
                return routes.icons.galvanoplastias;
                break;
            case '18':
                return routes.icons.casasExpendioComida;
                break;
            case '19':
                return routes.icons.talleres;
                break;
            case '20':
                return routes.icons.laboratorios;
                break;
            case '21':
                return routes.icons.industrias;
                break;
            case '22':
                return routes.icons.curtiembres;
                break;
            case '23':
                return routes.icons.lavaderos;
                break;
            case '24':
                return routes.icons.lavaderosIndustriales;
                break;
            case '25':
                return routes.icons.tintorerias;
                break;
            case '26':
                return routes.icons.centrosComerciales;
                break;
            case '27':
                return routes.icons.centrosEducativos;
                break;
            case '28':
                return routes.icons.rubroFuentesMoviles;
                break;
            case '29':
                return routes.icons.imprentas;
                break;
            case '30':
                return routes.icons.geriatricos;
                break;
            case '31':
                return routes.icons.empresasServiciosPublicos;
                break;
            case '32':
                return routes.icons.elaboracionAlimentosBebidas;
                break;
            case '33':
                return routes.icons.antenas;
                break;
            case '34':
                return routes.icons.industriasTextiles;
                break;
            case '35':
                return routes.icons.petroquimicas;
                break;
            case '36':
                return routes.icons.viviendas;
                break;
            case '37':
                return routes.icons.sinActividad;
                break;
            case '38':
                return routes.icons.espacioPublico;
                break;
            case '39':
                return routes.icons.obras;
                break;
            case '40':
                return routes.icons.productorasCanales;
                break;
            case '41':
                return routes.icons.talleresOficiosVarios;
                break;
            case '42':
                return routes.icons.estServicioLiquido;
                break;
            case '43':
                return routes.icons.oficinas;
                break;
            case '44':
                return routes.icons.eventos;
                break;
            case '46':
                return routes.icons.garages;
                break;
            case '48':
                return routes.icons.metalurgicas;
                break;
            case '49':
                return routes.icons.lavanderiasMecanicas;
                break;
            case '50':
                return routes.icons.consorcios;
                break;
            default:
                return routes.icons.otros;
                break;
        }
    }


    var traerIconoCedula = function()
    {
        return routes.icons.cedula;
    }


    var mostrarEsconder = function(mapa, filtro, fecha, cambioFecha)
    {
        switch(filtro.nombre)
        {
            case 'Programadas':
                filtroProgramadas(mapa, fecha, cambioFecha);
                break;
            case 'Reinspeccionar':                
                filtroReprogramadas(mapa,1);
                break;
            case 'Reprogramar':
                filtroReprogramadas(mapa,0);
                break;
            case 'Automáticas':
                filtroAutomaticas(mapa, fecha, cambioFecha);
                break;
            case 'Nunca Inspeccionado':
                filtroNuncaInspeccionadas(mapa, fecha, cambioFecha);
                break;
            case 'Sin Rubro':
            case 'Estación de Servicios GNC':
            case 'Estación de Servicios DUAL':
            case 'Establecimientos gubernamentales':
            case 'Hoteles-Hostels':
            case 'Supermercado':
            case 'Fábrica':
            case 'Acopio combustibles-sustancias peligrosas':
            case 'Transporte':
            case 'Comercio MAY-MIN':
            case 'Sanidad Y Salud':
            case 'Club':
            case 'Frigorificos':
            case 'Depositos':
            case 'Galvanoplastía':
            case 'Casas expendio de comida':
            case 'Mantenimiento automotor':
            case 'Laboratorio-drogerias -no farmacias-':
            case 'Industria quimica':
            case 'Curtiembre':
            case 'Lavadero de vehiculos':
            case 'Lavadero industrial':
            case 'Tintorería':
            case 'Centro comercial':
            case 'Centros educativos':
            case 'Fuentes moviles':
            case 'Imprenta':
            case 'Geriatrico':
            case 'Empresas de servicios publicos':
            case 'Elaboracion de alimentos-bebidas':
            case 'Antenas':
            case 'Industria textil':
            case 'Petroquimica':
            case 'Vivienda':
            case 'Sin actividad':
            case 'Espacio público':
            case 'Obra edilicia-infraestructura urbana':
            case 'Productoras-canales TV':
            case 'Sanidad Y Salud':
            case 'Talleres oficios varios':
            case 'Estación de Servicios Con Líquido':
            case 'Oficina':
            case 'Eventos':
            case 'Garage':
            case 'Metalurgica':
            case 'Lavanderia Mecanica':
            case 'Consorcio':
            case 'Local Grande Indeterminado':
            case 'Local Pequeño Indeterminado':
                filtroRubro(filtro, mapa, fecha, cambioFecha);
                break;
            case 'AVUS':
            case 'CAA':
            case 'CEDULA DE CITACION':
            case 'CERFITICADO LIMPIEZA de TANQUE de AGUA':
            case 'CERTIFICADO DESINFECCION':
            case 'CLUB DEPORTIVO':
            case 'DGET':
            case 'EFLUENTES':
            case 'EMPRESAS DE FUMIGACION/TANQUES':
            case 'FUENTES MOVILES':
            case 'INTIMACION-INMEDIATA':
            case 'MATAFUEGOS':
            case 'NOCTURNIDAD':
            case 'NOTIFICACIONES':
            case 'OLORES':
            case 'OTROS':
            case 'PERSONAL':
            case 'PREDIO':
            case 'PROCTORES EVENTOS':
            case 'PRODUCTORAS':
            case 'PRORROGA':
            case 'RAC':
            case 'RES. PATOGENICOS':
            case 'RES. PELIGROSOS.':
            case 'ROPA DE TRABAJO':
            case 'ROPA HOSPITALARIA':
            case 'RUIDOS':
            case 'SEGURO AMB. OBLIG':
            case 'SITIOS CONTAMINADOS':
            case 'SUACI':
            case 'SUPERMERCADO':
                filtroCedula(filtro, mapa, fecha, cambioFecha);
                break;
            case 'INTIMACION INMEDIATA':
                filtroIntimacionInmediata(mapa, fecha, cambioFecha);
                break;
            default:
                alert('Todavía no hay nada armado');
                break;
        }
    }


    /*------------------------------------*/


    /*------------------------------------*/


    /*------------------------------------*/


    /*------------------------------------*/


    /*------------------------------------*/


    var transformarResponseEnMarcadores = function(mapa, response)
    {
        if(response == undefined || response == '')
            alert('No hay datos');
        else
        {
            if(response[0] == 'Error al consultar los datos')
            {
                var str = '';
                for (var i = 0; i < response.length; i++)
                    str += response[i] + '\n';

                alert(str);
            }
            else
            {
                var icono = '';

                if(response.constructor.name == 'Array')
                {
                    if(response[0].tipo == 'programada')
                    {                        
                        //Programadas: idEstablecimiento, lon, lat
                        icono = queIcono('Programadas', undefined);

                        for (var i = 0; i < response.length; i++)
                        {
                            mapa.addMarker({
                                id: response[i].id,
                                lng: response[i].lon,
                                lat: response[i].lat,
                                icon: icono,
                                click: function(){
                                    mostrarModalProgramar(this.id);
                                }
                            });
                        }
                    }
                    else if(response[0].tipo == 'intimacionInmediata')
                    {
                        //Programadas: idEstablecimiento, lon, lat
                        icono = queIcono('intimacionInmediata', undefined);

                        for (var i = 0; i < response.length; i++)
                        {
                            mapa.addMarker({
                                id: response[i].id,
                                lng: response[i].lon,
                                lat: response[i].lat,
                                icon: icono,
                                click: function(){
                                    mostrarModalProgramar(this.id);
                                }
                            });
                        }
                    }else if(response[0].tipo == 'Reinspeccionar')
                    {                        
                        icono = queIcono('Reinspeccionar', undefined);

                        for (var i = 0; i < response.length; i++)
                        {
                            mapa.addMarker({
                                id: response[i].id,
                                lng: response[i].lon,
                                lat: response[i].lat,
                                icon: icono,
                                click: function(){
                                    mostrarModalReInspeccionar(this.id,1);
                                }
                            });
                        }
                    }else if(response[0].tipo == 'Reprogramar'){                        
                        icono = queIcono('Reprogramar', undefined);
                        for (var i = 0; i < response.length; i++)
                        {
                            mapa.addMarker({
                                id: response[i].id,
                                lng: response[i].lon,
                                lat: response[i].lat,
                                icon: icono,
                                click: function(){
                                    mostrarModalReInspeccionar(this.id,0);
                                }
                            });
                        }  
                    }else if(response[0].tipo == 'automatica' || response[0].tipo =='nuncaInspeccionada')
                    {
                        //Automáticas: idEstablecimiento, lon, lat                        

                        for (var i = 0; i < response.length; i++)
                        {
                            icono = queIcono('Automáticas', response[i].Id_Rubro_Principal);
                            mapa.addMarker({
                                id: response[i].id,
                                lng: response[i].Lon,
                                lat: response[i].Lat,
                                icon: icono,
                                click: function(){
                                    mostrarModalProgramar(this.id);
                                }
                            });
                        }
                    }else
                    {                        
                        if(response[0].idRubroPrincipal)
                        {
                            //Rubro
                            //PorRubroPrincipal: idEstablecimiento, lon, lat, idRubro
                            icono = queIcono('Rubro', response[0].idRubroPrincipal);

                            for (var i = 0; i < response.length; i++)
                            {
                                mapa.addMarker({
                                    id: response[i].id,
                                    lng: response[i].lon,
                                    lat: response[i].lat,
                                    icon: icono,
                                    click: function(){
                                        mostrarModalProgramar(this.id);
                                    }
                                });
                            }
                        }else
                        {
                            //PorCedula: idEstablecimiento, lon, lat, idCedula
                            icono = queIcono('Cédula', response[0].idCedula);

                            for (var i = 0; i < response.length; i++)
                            {
                                mapa.addMarker({
                                    id: response[i].id,
                                    lng: response[i].lon,
                                    lat: response[i].lat,
                                    icon: icono,
                                    click: function(){
                                        mostrarModalProgramar(this.id);
                                    }
                                });
                            }
                        }                            
                        
                    }
                }
                else
                {
                    //Direcciones buscadas
                    if(response.constructor.name == 'Object')
                    {
                        icono = routes.icons.direBuscada;

                        var idCalle = $("#single").val();
                        var altura = $("#a").val();
                        var piso = $("#p").val();
                        var dpto = $("#d").val();
                        var local = $("#l").val();

                        mapa.addMarker({
                            lng: response.Lon,
                            lat: response.Lat,
                            smp: response.SMP,
                            icon: icono,
                            draggable: true,
                            animation: google.maps.Animation.DROP,
                            idcalle: idCalle,
                            altura: altura,
                            piso: piso,
                            dpto: dpto,
                            local: local,
                            click: function(){
                                buscarDireccion(mapa, this.smp, this.idcalle, this.altura, this.piso, this.dpto, this.local);
                            }
                        });
                    }
                }
            }
        }
    }


    /*------------------------------------*/


    /*------------------------------------*/


    /*------------------------------------*/


    /*------------------------------------*/


    /*------------------------------------*/


    //PROGRAMADAS ↓


    var filtroProgramadas = function(mapa, fecha, cambioFecha)
    {
        if(programadas.length == 0 || cambioFecha)
        {
            var values = {};
            var url = routes.map.programadas + '/' + fecha;
            var gif = routes.map.gif;

            console.log(url);

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
                    programadas = response;
                    transformarResponseEnMarcadores(mapa, programadas);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            transformarResponseEnMarcadores(mapa, programadas);
    }


    /*------------------------------------*/

    //INTIMACION INMEDIATA ↓


    var filtroIntimacionInmediata = function(mapa, fecha, cambioFecha)
    {
        if(intimacionInmediata.length == 0 || cambioFecha)
        {
            var values = {};
            var url = routes.map.intimacionInmediata + '/' + fecha;
            var gif = routes.map.gif;

            console.log(url);

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
                    intimacionInmediata = response;
                    transformarResponseEnMarcadores(mapa, intimacionInmediata);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            transformarResponseEnMarcadores(mapa, intimacionInmediata);
    }


    /*------------------------------------*/

    //REPROGRAMADAS ↓


    var filtroReprogramadas = function(mapa,tipo)
    {
        if((reprogramadas.length == 0 && tipo == 0) || (reinspeccionar.length == 0 && tipo == 1))
        {
            var values = {};
            var url = routes.map.reprogramadas.replace("0", tipo);
            var gif = routes.map.gif;            

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
                    if(tipo == 0){
                        reprogramadas = response;
                    }else{
                        reinspeccionar = response;
                    }                    
                    transformarResponseEnMarcadores(mapa, response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else{
            if(tipo == 0){
                transformarResponseEnMarcadores(mapa, reprogramadas);
            }else{
                transformarResponseEnMarcadores(mapa, reinspeccionar);
            }            
        }
    }


    /*------------------------------------*/


    //AUTOMÁTICAS ↓


    var filtroAutomaticas = function(mapa, fecha, cambioFecha)
    {
        if(automaticas.length == 0 || cambioFecha)
        {
            var values = {};
            var url = routes.map.automaticas + '/' + fecha;           
            
            var gif = routes.map.gif;

            console.log(url);

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
                    automaticas = response;
                    transformarResponseEnMarcadores(mapa, response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            transformarResponseEnMarcadores(mapa, automaticas);
    }


    /*------------------------------------*/

    //NuncaInspeccionadas ↓


    var filtroNuncaInspeccionadas = function(mapa, fecha, cambioFecha)
    {
        if(nuncaInspeccionado.length == 0 || cambioFecha)
        {
            var values = {};
            var url = routes.map.nuncaInspeccionada + '/' + fecha;            
            var gif = routes.map.gif;

            console.log(url);

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
                    nuncaInspeccionado = response;
                    transformarResponseEnMarcadores(mapa, response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            transformarResponseEnMarcadores(mapa, nuncaInspeccionado);
    }


    /*------------------------------------*/


    //RUBROS ↓


    var filtroRubro = function(filtro, mapa, fecha, cambioFecha)
    {
        var marcadoresRubro = queArrayGet(filtro.nombre);

        if(marcadoresRubro.length == 0 || cambioFecha)
        {
            var values = {};
            var url = routes.map.buscarPorRubro + '/' + filtro.id + '/' + fecha;

            console.log(url);
            var res = '';

            var gif = routes.map.gif;

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
                    res = response;
                    marcadoresRubro = res;
                    transformarResponseEnMarcadores(mapa, marcadoresRubro);
                    queArraySet(filtro.nombre, marcadoresRubro);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    $.unblockUI();
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            transformarResponseEnMarcadores(mapa, marcadoresRubro);
    }


    /*------------------------------------*/


    //CÉDULAS ↓


    var filtroCedula = function(filtro, mapa, fecha, cambioFecha)
    {
        var marcadoresCedula = queArrayGet(filtro.nombre);

        if(marcadoresCedula.length == 0 || cambioFecha)
        {
            var values = {};
            var url = routes.map.buscarPorCedula + '/' + filtro.id + '/' + fecha;

            console.log(url);
            var res = '';

            var gif = routes.map.gif;

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
                    res = response;
                    marcadoresCedula = res;
                    transformarResponseEnMarcadores(mapa, marcadoresCedula);
                    queArraySet(filtro.nombre, marcadoresCedula);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    $.unblockUI();
                    transformarResponseEnMarcadores(['Error al consultar los datos', xhr, ajaxOptions, thrownError]);
                },
                complete: function(response){
                    $.unblockUI();
                }
            });
        }
        else
            transformarResponseEnMarcadores(mapa, marcadoresCedula);
    }


    //FIN DATOS MAPA ↑


    /*------------------------------------*/


    //Buscar dirección
    var normalizarDireccion = function(mapa, idCalle, altura)
    {
        var values = {};
        var url = routes.map.normalizarDireccion.replace("0", idCalle);
        
        url = url.replace("-1", altura);

        console.log(url);

        var gif = routes.map.gif;

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
                if(response == 'ERROR')
                    alert('No existe la dirección buscada');
                else
                    transformarResponseEnMarcadores(mapa, response);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var log = ['Error al consultar los datos', xhr, ajaxOptions, thrownError];
                transformarResponseEnMarcadores(mapa, log);
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    //Buscar coincidencias con la dirección buscada
    var buscarDireccion = function(mapa, smp, idCalle, altura, piso, dpto, local)
    {
        var values = {};
        var url = routes.map.buscarDireccion.replace("0", idCalle);
        
        url = url.replace("-1", altura);

        if(smp != '')
            url = url.replace("-2", smp);
        else
            url = url.replace("-2", '$');

        if(piso != '')
            url = url.replace("-3", piso);
        else
            url = url.replace("-3", '$');

        if(dpto != '')
            url = url.replace("-4", dpto);
        else
            url = url.replace("-4", '$');

        if(local != '')
            url = url.replace("-5", local);
        else
            url = url.replace("-5", '$');

        console.log(url);

        $.ajax({
            type: "POST",
            url: url,
            data: values,
            success: function(response){
                var establecimientos = '';
                
                if(response.Resultado == 'No Encontrado')
                    programarSinEstablecimiento(idCalle, altura, piso, dpto, local);
                else
                {
                    if(response.Resultado == 'Especifico')
                    {
                        mostrarModalProgramar(response[0].idEstablecimiento);
                    }
                    else
                    {
                        //'Parcela'                        
                        piso = "'" + piso + "'";
                        dpto = "'" + dpto + "'";
                        local = "'" + local + "'";
                        
                        var html = '  <div class="col-md-5"></div>' +
                                        '<a href="javascript:MapaProgramacion.programarSinEstablecimiento(' + idCalle + ',' + altura + ',' + piso + ',' + dpto + ',' + local + ');" class="btn blue">' +
                                            '<i class="fa fa-plus"></i> Nuevo establecimiento </a>';                        

                        for (var i = 0; i <= response.Cantidad-1; i++)
                            establecimientos += response[i].idEstablecimiento + '***' + response[i].direccion + '***' + response[i].razonSocial + '***';                        

                        mostrarModalDecision(establecimientos, html);
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log('error');
            }
        });
    }


    var mostrarModalDecision = function(establecimientos, boton)
    {
        var values = establecimientos;        

        var url = routes.map.decidirProgramacion;
        var gif = routes.map.gif;

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
                $('.modal-body').html(response + boton);
                $('.modal-title').html("Seleccionar establecimiento");
                $('#modalProgramar').modal('show');
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    var programarSinEstablecimiento = function(idCalle, altura, piso, dpto, local)
    {
        var values = {};
        var url = routes.map.programarSinEstablecimiento.replace("0", idCalle);
        
        url = url.replace("-1", altura);

        if(piso != '')
            url = url.replace("-2", piso);
        else
            url = url.replace("-2", '$');

        if(dpto != '')
            url = url.replace("-3", dpto);
        else
            url = url.replace("-3", '$');

        if(local != '')
            url = url.replace("-4", local);
        else
            url = url.replace("-4", '$');

        var gif = routes.map.gif;

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
                $('.modal-body').html(response);
                $('.fecha-picker').datepicker({ dateFormat: "dd-mm-yy" });
                $('.modal-title').html("Programación");
                $('#modalProgramar').modal('show');
            },
            complete: function(response){
                $.unblockUI();
            }
        });        
    }


    //Programar CON establecimiento
    var mostrarModalProgramar = function (id)
    {
        var values = {};

        var url = routes.map.programar.replace("0", id);
        var gif = routes.map.gif;

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
                $('.modal-body').html(response);
                $('.fecha-picker').datepicker({ dateFormat: "dd-mm-yy" });
                $('.modal-title').html("Programación");
                $('#modalProgramar').modal('show');
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    var mostrarModalReInspeccionar = function (idOrdenInspeccion,tipo)
    {
        var values = {};
        if(tipo == 1){
            var url = routes.map.reinspeccionar.replace("0", idOrdenInspeccion);
        }else{
            var url = routes.map.reprogramar.replace("-1", idOrdenInspeccion);
        }
        
        var gif = routes.map.gif;

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
                $('.modal-body').html(response);
                $('.modal-title').html("Reprogramación");
                $('#modalProgramar').modal('show');
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }


    var send = function () {
        //var values = {};

        var values = new FormData($('#modalForm')[0]);

        //var values = $("#modalForm").serializeArray();
        var resp;

        var gif = routes.map.gif;
          
        $.ajax({
            type: $("#modalForm").attr('method'),
            url: $("#modalForm").attr('action'),
            data: values,
            processData: false,
            contentType: false,
            beforeSend: function (){
                $.blockUI({
                    message: '<div class="page-loading"><img src="' + gif + '"/>&nbsp;&nbsp;<span>Cargando...</span></  div>',
                    css: { border: 'none'}
                });
            },
            success: function(response){
                resp =  response;

                if(resp.result == "OK")
                {
                    $(".page-content").append('<input class="successFlashMessage" value="Guardado" hidden>');
                    UIToastr.init();
                    $('#modalProgramar').modal('hide');
                }
                else
                {
                    if(resp.result == "ERROR")
                    {
                        $(".page-content").append('<input class="errorFlashMessage" value="' + resp.details + '" hidden>');
                        UIToastr.init();
                    }
                    else
                    {
                        $('.modal-body').html(resp);
                    }
                }
            },
            complete: function(response){
                $.unblockUI();
            }
        });
    }

    var eliminarOrden = function (id) {
        var confirmar = confirm("¿Esta seguro de eliminar la orden de inspección?");

        if(confirmar){
          var values = {};
          var url= routes.javascript.eliminarOrden.replace("0",id);
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

    var elimarArchivo = function(id,archivo){
        var confirmar = confirm("¿Esta seguro de eliminar el archivo?");

        if(confirmar){
          var values = {};
          var url= routes.javascript.eliminarArchivo.replace("0",archivo);
          $.ajax({
            type: "POST",
            url: url,
            data: values,
            beforeSend : function (){             
            },
            success: function(response){
              document.getElementById(id).style.visibility = 'hidden';              
            },
            complete: function(response){                
            }
          });
        }
    }

    var aniadirComuna = function (map) {
        comunas = map.cargarKml('https://raw.githubusercontent.com/SistemasDgconta/MapasKml/master/ZonaDgConta2.kml');
    }

    var aniadirAcumar = function (map) {
        acumar = map.cargarKml('https://raw.githubusercontent.com/SistemasDgconta/MapasKml/master/KMLCUENCAMRXG');
    }

    /*var kmlComunas = function (map) {
        $("#kmlcomunas").click(function () {
            if($(this).is(":checked"))
                map.esconderKml(comunas);
            else
                comunas = map.cargarKml('https://raw.githubusercontent.com/SistemasDgconta/MapasKml/master/ZonaDgConta.kml');
        });
    }

    var kmlAcumar = function (map) {
        $("#kmlacumar").click(function () {
            if($(this).is(":checked"))
                map.esconderKml(acumar);
            else
                acumar = map.cargarKml('https://raw.githubusercontent.com/SistemasDgconta/MapasKml/master/OK.KML');
        });
    }*/

    return {
        init: function () {
            initMap();
            mesesSelect();
            rubrosSelect();
            cedulasVencidasSelect();
            callesSelect();
        },

        mostrarModalProgramar: function(id) {
            mostrarModalProgramar(id);
        },

        programarSinEstablecimiento: function(idCalle, altura, piso, dpto, local) {
            programarSinEstablecimiento(idCalle, altura, piso, dpto, local);
        },

        mostrarModalDecision: function(establecimientos, boton) {
            mostrarModalDecision(establecimientos, boton);
        },

        mostrarModalReInspeccionar: function(idOrdenInspeccion) {
            mostrarModalReInspeccionar(idOrdenInspeccion);
        },

        send: function() {
            send();
        },

        eliminarOrden: function(idOrdenInspeccion) {
            eliminarOrden(idOrdenInspeccion);
        },

        elimarArchivo: function(id,archivo) {
            elimarArchivo(id,archivo);
        },
    };

}();


jQuery(document).ready(function() {
    MapaProgramacion.init();
});