<?php

namespace Laboratorio\PedidoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use Laboratorio\PedidoBundle\Entity\Pedido;
use Laboratorio\PedidoBundle\Entity\Muestra;
//use Laboratorio\PedidoBundle\Entity\MuestraEstados; -
use Laboratorio\PedidoBundle\Entity\CargaResultados;

use Laboratorio\PedidoBundle\Form\Protocolo1Type;
//use Laboratorio\PedidoBundle\Form\Protocolos2Type; -

use CoreBundle\Logic\TablaAjax;
use Symfony\Component\Yaml\Yaml;
use CoreBundle\Logic\encriptador;

class ProtocoloController extends Controller
{
    private function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    public function protocoloAguasAction(Request $request, $idMuestra, $ausenciaGerente)
    {
        //\Doctrine\Common\Util\Debug::dump($idMuestra);
        //exit();
        $final = array();
        $final['registros'] = [];
        $idsAreas = [19 => 'CAMPO', 20 => 'BIOLOGICO', 21 => 'INSTRUMENTAL', 22 => 'FISICO-QUIMICA'];
        $tiposLegislaciones = ['legislacionContacto', 'legislacionSinContacto', 'legislacionUsoPasivo', 'legislacionSinClasificar'];

        if (!$idMuestra)
            throw $this->createNotFoundException('No se seleccionó ninguna muestra');
        else
        {
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');

            //Verificando que la muestra exista
            $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);
            $verificada = false;

            if ($idMuestra > 0)
            {
                $muestra = $em->findOneByIdMuestra($idMuestra);

                if ($muestra)
                    $verificada = true; //La muestra existe
            }

            if (!$verificada)
                throw $this->createNotFoundException('No se encontró la muestra especificada');
            else
            {
                //Sacando el programa de la muestra
                $programa = $muestra->getPedido()->getPrograma();
                $final['programa'] = $programa;
                $final['muestra'] = $muestra;

                foreach ($idsAreas as $key => $value)
                {
                    $resultadosAreaMuestra = $this->preProtocolo($muestra->getId(), $muestra->getNumeroMuestra(), $key);

                    if (!empty($resultadosAreaMuestra)) //Si hay resultados
                    {
                        $legislaciones = $columnas = array();
                        $mayor = null;

                        $i = 0;
                        while ($i < count($resultadosAreaMuestra) && is_null($resultadosAreaMuestra[$i]['fechaFinAnalisis']))
                            $i++;

                        if ($i < count($resultadosAreaMuestra)) // (se sabe que hay mayor, es decir, hay fechas)
                        {
                            $mayor = $resultadosAreaMuestra[$i]['fechaFinAnalisis'];

                            //sacando la fecha de finalización de análisis...
                            foreach ($resultadosAreaMuestra as $resultado)
                                if (!is_null($resultado['fechaFinAnalisis']))
                                    if ($resultado['fechaFinAnalisis'] > $mayor)
                                        $mayor = $resultado['fechaFinAnalisis'];
                        }

                        foreach ($resultadosAreaMuestra as $resultado)
                            foreach ($tiposLegislaciones as $tipoLegislacion) //Recopilando legislaciones
                                if ($resultado[$tipoLegislacion])
                                    $legislaciones[($resultado[$tipoLegislacion])->getId()] = ($resultado[$tipoLegislacion])->getLegislacion();

                        $final['registros'][$value][$muestra->getId()]['legislaciones'] = $legislaciones;
                        $final['registros'][$value][$muestra->getId()]['fechaFinalizacionAnalisis'] = $mayor;
                        $final['registros'][$value][$muestra->getId()]['resultados'] = $resultadosAreaMuestra;
                    }

                    if (array_key_exists($value, $final['registros']))
                    {
                        //ENCONTRAR LA MAYOR FECHA DE TODAS LAS MUESTRAS (POR ÁREAS)
                        $fechaFinalizacionAnalisis = null;
                        foreach ($final['registros'][$value] as $registrosMuestra) // (ya sé que es la forma menos eficiente, pero buen...)
                            if (!is_null($registrosMuestra['fechaFinalizacionAnalisis']))
                                $fechaFinalizacionAnalisis = $registrosMuestra['fechaFinalizacionAnalisis'];

                        if ($fechaFinalizacionAnalisis) // (se sabe que hay mayor, es decir, hay fechas)
                        {
                            $rm = current($final['registros'][$value]);
                            $mayor = $rm['fechaFinalizacionAnalisis'];

                            foreach ($final['registros'][$value] as $registrosMuestra)
                                if (!is_null($registrosMuestra['fechaFinalizacionAnalisis']))
                                    if ($registrosMuestra['fechaFinalizacionAnalisis'] > $mayor)
                                        $mayor = $registrosMuestra['fechaFinalizacionAnalisis'];
                        }

                        $final['fechaFinalizacionAnalisis'][$value] = $mayor;

                        //ORDENAR LAS DETERMINACIONES
                        if ($value == 'CAMPO')
                        {
                            $ram = $results = $parameters = array();
                            $numeroAspecto = $numeroPH = $numeroConductividad = $numeroTemperatura = $numeroOxigeno = $numeroTurbidez = $numeroTransparencia = $numeroCloro = $numeroDetritos = $numeroSolidos = $numeroSalinidad = -1;

                            switch ($programa->getId())
                            {
                                case 3:
                                case 4:
                                case 5:
                                case 6:
                                case 7:
                                case 21:
                                    $numeroAspecto = 0;
                                    $numeroPH = 1;
                                    $numeroConductividad = 2;
                                    $numeroTemperatura = 3;
                                    $numeroOxigeno = 4;
                                    $numeroTurbidez = 5;
                                    $numeroSalinidad = 6;
                                    $numeroSolidos = 7;
                                    break;

                                case 8:
                                    $numeroAspecto = 0;
                                    $numeroPH = 1;
                                    $numeroCloro = 2;
                                    $numeroConductividad = 3;
                                    $numeroTemperatura = 4;
                                    $numeroOxigeno = 5;
                                    $numeroTurbidez = 6;
                                    $numeroSalinidad = 7;
                                    $numeroSolidos = 8;
                                    break;

                                case 2:
                                    $numeroAspecto = 0;
                                    $numeroPH = 1;
                                    $numeroConductividad = 2;
                                    $numeroTemperatura = 3;
                                    $numeroCloro = 4;
                                    $numeroTurbidez = 5;
                                    $numeroSalinidad = 6;
                                    $numeroSolidos = 7;
                                    break;

                                case 1:
                                    $numeroTransparencia = 0;
                                    $numeroPH = 1;
                                    $numeroCloro = 2;
                                    $numeroTurbidez = 3;
                                    $numeroTemperatura = 4;
                                    $numeroDetritos = 5;
                                    $numeroConductividad = 6;
                                    $numeroSolidos = 7;
                                    $numeroSalinidad = 8;
                                    break;

                                case 17:
                                    $numeroAspecto = 0;
                                    $numeroCloro = 1;
                                    $numeroPH = 2;
                                    $numeroTurbidez = 3;
                                    $numeroTemperatura = 4;
                                    $numeroConductividad = 5;
                                    $numeroSolidos = 6;
                                    $numeroSalinidad = 7;
                                    break;
                            }

                            foreach ($final['registros'][$value] as $kmue => $mue) //Debería sacarlo, pero no tengo tiempo para probarlo (2502)
                            {
                                $iCampo = 10;
                                // MUESTRA
                                foreach ($mue['resultados'] as $kre => $re)
                                {
                                    $iCampo++;
                                    switch ($re['nombreDeterminacion'])
                                    {
                                        case 'Aspecto':
                                            if ($numeroAspecto != -1)
                                                $parameters[$numeroAspecto] = $re;
                                            break;

                                        case 'Transparencia':
                                            if ($numeroTransparencia != -1)
                                                $parameters[$numeroTransparencia] = $re;
                                            break;

                                        case 'pH a 25°C':
                                            if ($numeroPH != -1)
                                                $parameters[$numeroPH] = $re;
                                            break;

                                        case 'Conductividad a 25°C':
                                            if ($numeroConductividad != -1)
                                                $parameters[$numeroConductividad] = $re;
                                            break;

                                        case 'Temperatura':
                                            if ($numeroTemperatura != -1)
                                                $parameters[$numeroTemperatura] = $re;
                                            break;

                                        case 'Oxígeno':
                                            if ($numeroOxigeno != -1)
                                                $parameters[$numeroOxigeno] = $re;
                                            break;

                                        case 'Turbidez':
                                            if ($numeroTurbidez != -1)
                                                $parameters[$numeroTurbidez] = $re;
                                            break;

                                        case 'Cloro Residual':
                                            if ($numeroCloro != -1)
                                                $parameters[$numeroCloro] = $re;
                                            break;

                                        case 'Detritos, espumas y cuerpos extraños':
                                            if ($numeroDetritos != -1)
                                                $parameters[$numeroDetritos] = $re;
                                            break;

                                        case 'Sólidos Disueltos Totales':
                                            if ($numeroSolidos != -1)
                                                $parameters[$numeroSolidos] = $re;
                                            break;

                                        case 'Salinidad':
                                            if ($numeroSalinidad != -1)
                                                $parameters[$numeroSalinidad] = $re;
                                            break;

                                        default:
                                            $parameters[$iCampo] = $re;
                                            break;
                                    }
                                }

                                ksort($parameters); # para ordenar por keys
                                $mue['resultados'] = $parameters;

                                $final['registros'][$value][$kmue]['resultados'] = $parameters;
                            }
                        }

                        if ($value == 'FISICO-QUIMICA')
                        {
                            $ram = $results = $parameters = array();
                            foreach ($final['registros'][$value] as $kmue => $mue) //Igual que en campo
                            {
                                $iFisicoQuimica = 29;
                                // MUESTRA
                                foreach ($mue['resultados'] as $kre => $re)
                                {
                                    $iFisicoQuimica++;
                                    switch ($re['nombreDeterminacion'])
                                    {
                                        case 'Nitritos':
                                            $parameters[0] = $re;
                                            break;

                                        case 'Nitrógeno Amoniacal':
                                            $parameters[1] = $re;
                                            break;

                                        case 'Nitratos':
                                            $parameters[2] = $re;
                                            break;

                                        case 'Sólidos Totales (103-105)°C':
                                            $parameters[3] = $re;
                                            break;

                                        case 'Sólidos Fijos':
                                            $parameters[4] = $re;
                                            break;

                                        case 'Sólidos volátiles':
                                            $parameters[5] = $re;
                                            break;

                                        case 'Dureza':
                                            $parameters[6] = $re;
                                            break;

                                        case 'Alcalinidad Total':
                                            $parameters[7] = $re;
                                            break;

                                        case 'Cloruros':
                                            $parameters[8] = $re;
                                            break;

                                        case 'Sólidos Sedimentables 1 hs':
                                            $parameters[9] = $re;
                                            break;

                                        case 'Sólidos Sedimentables 10 min':
                                            $parameters[10] = $re;
                                            break;

                                        case 'Sólidos Sedimentables 2 hs':
                                            $parameters[11] = $re;
                                            break;

                                        case 'Sólidos Suspendidos':
                                            $parameters[12] = $re;
                                            break;

                                        case 'DQO':
                                            $parameters[13] = $re;
                                            break;

                                        case 'DBO5':
                                            $parameters[14] = $re;
                                            break;

                                        case 'Fósforo Total':
                                            $parameters[15] = $re;
                                            break;

                                        case 'Detergentes':
                                            $parameters[16] = $re;
                                            break;

                                        case 'Sulfuros':
                                            $parameters[17] = $re;
                                            break;

                                        case 'Sulfatos':
                                            $parameters[18] = $re;
                                            break;

                                        case 'Sustancias Fenólicas':
                                            $parameters[19] = $re;
                                            break;

                                        case 'Nitrógeno Kjeldahl':
                                            $parameters[20] = $re;
                                            break;

                                        case 'Aluminio':
                                            $parameters[21] = $re;
                                            break;

                                        case 'Hidrocarburos':
                                            $parameters[22] = $re;
                                            break;

                                        case 'Aceites y Grasas':
                                            $parameters[23] = $re;
                                            break;

                                        // SUELOS ↓

                                        case 'Aspecto (características)':
                                            $parameters[24] = $re;
                                            break;

                                        case 'Materia Orgánica':
                                            $parameters[25] = $re;
                                            break;

                                        case 'pH a 25°C (1:5)':
                                            $parameters[26] = $re;
                                            break;

                                        case 'Conductividad a 25°C (1:5)':
                                            $parameters[27] = $re;
                                            break;

                                        case 'Humedad % (Base seca)':
                                            $parameters[28] = $re;
                                            break;

                                        default:
                                            $parameters[$iFisicoQuimica] = $re;
                                            break;
                                    }
                                }

                                ksort($parameters); # para ordenar por keys
                                $mue['resultados'] = $parameters;

                                $final['registros'][$value][$kmue]['resultados'] = $parameters;
                            }
                        }

                        if ($value == 'INSTRUMENTAL')
                        {
                            $ram = $results = $parameters = array();
                            foreach ($final['registros'][$value] as $kmue => $mue) //Igual que en físico química
                            {
                                $iInstrumental = 18;
                                // MUESTRA
                                foreach ($mue['resultados'] as $kre => $re)
                                {
                                    $iInstrumental++;
                                    switch ($re['nombreDeterminacion'])
                                    {
                                        case 'Aluminio':
                                            $parameters[0] = $re;
                                            break;

                                        case 'Benceno':
                                            $parameters[1] = $re;
                                            break;

                                        case 'Tolueno':
                                            $parameters[2] = $re;
                                            break;

                                        case 'Etil-Benceno':
                                            $parameters[3] = $re;
                                            break;

                                        case 'm/p-Xileno':
                                            $parameters[4] = $re;
                                            break;

                                        case 'o-Xileno':
                                            $parameters[5] = $re;
                                            break;

                                        case 'Níquel total':
                                            $parameters[6] = $re;
                                            break;

                                        case 'Cobre total':
                                            $parameters[7] = $re;
                                            break;

                                        case 'Zinc total':
                                            $parameters[8] = $re;
                                            break;

                                        case 'Hierro total':
                                            $parameters[9] = $re;
                                            break;

                                        case 'Manganeso total':
                                            $parameters[10] = $re;
                                            break;

                                        case 'Cobalto total':
                                            $parameters[11] = $re;
                                            break;

                                        case 'Cromo total':
                                            $parameters[12] = $re;
                                            break;

                                        case 'Arsénico total':
                                            $parameters[13] = $re;
                                            break;

                                        case 'Plomo total':
                                            $parameters[14] = $re;
                                            break;

                                        case 'Cadmio total':
                                            $parameters[15] = $re;
                                            break;

                                        case 'Plata total':
                                            $parameters[16] = $re;
                                            break;

                                        case 'Mercurio total':
                                            $parameters[17] = $re;
                                            break;

                                        //SUELOS ya está incluido ↑

                                        default:
                                            $parameters[$iInstrumental] = $re;
                                            break;
                                    }
                                }

                                ksort($parameters); # para ordenar por keys
                                $mue['resultados'] = $parameters;

                                $final['registros'][$value][$kmue]['resultados'] = $parameters;
                            }
                        }

                        if ($value == 'BIOLOGICO')
                        {
                            $ram = $results = $parameters = array();
                            $numeroBacterias = $numeroColTotales = $numeroColFecales = $numeroEscherichia = $numeroPseudomonas = $numeroEstreptococos = $numeroEnterococos = $numeroColTotales35 = $numeroColFecales44 = -1;

                            foreach ($final['registros'][$value] as $kmue => $mue) //Igual que en instrumental
                            {
                                $iBiologico = 10;
                                // MUESTRA
                                foreach ($mue['resultados'] as $kre => $re)
                                {
                                    $iBiologico++;
                                    switch ($re['nombreDeterminacion'])
                                    {
                                        case 'Bacterias aerobias mesófilas':
                                            $parameters[0] = $re;
                                            break;

                                        case 'Bacterias aerobias mesófilas a 35°C':
                                            $parameters[1] = $re;
                                            break;

                                        case 'Coliformes totales':
                                            $parameters[2] = $re;
                                            break;

                                        case 'Coliformes totales a 35°C':
                                            $parameters[3] = $re;
                                            break;

                                        case 'Coliformes Fecales':
                                            $parameters[4] = $re;
                                            break;

                                        case 'Coliformes Fecales a 44,5°C':
                                            $parameters[5] = $re;
                                            break;

                                        case 'Escherichia coli':
                                            $parameters[6] = $re;
                                            break;

                                        case 'Pseudomonas aeruginosa':
                                            $parameters[7] = $re;
                                            break;

                                        case 'Estreptococos fecales':
                                            $parameters[8] = $re;
                                            break;

                                        case 'Enterococos':
                                            $parameters[9] = $re;
                                            break;

                                        default:
                                            $parameters[$iBiologico] = $re;
                                            break;
                                    }
                                }

                                ksort($parameters); # para ordenar por keys
                                $mue['resultados'] = $parameters;

                                $final['registros'][$value][$kmue]['resultados'] = $parameters;
                            }
                        }
                    }
                }

                //Sacar la fecha más grande de finalización
                $mayor = null;
                foreach ($final['fechaFinalizacionAnalisis'] as $fecha)
                    if ($fecha > $mayor)
                        $mayor = $fecha;

                $final['fechaFinalizacionAnalisis'] = $mayor;

                //Sacar incertidumbre
                $final['incertidumbre'] = $muestra->getIncertidumbre();

                //Ordenando las áreas intervinientes de acuerdo a criterio de la gerencia
                $registrosOrdenados = array();
                if (array_key_exists('CAMPO', $final['registros']))
                    $registrosOrdenados['CAMPO'] = $final['registros']['CAMPO'];

                if (array_key_exists('FISICO-QUIMICA', $final['registros']))
                    $registrosOrdenados['FISICO-QUIMICA'] = $final['registros']['FISICO-QUIMICA'];

                if (array_key_exists('INSTRUMENTAL', $final['registros']))
                    $registrosOrdenados['INSTRUMENTAL'] = $final['registros']['INSTRUMENTAL'];

                if (array_key_exists('BIOLOGICO', $final['registros']))
                    $registrosOrdenados['BIOLOGICO'] = $final['registros']['BIOLOGICO'];

                if (!empty($registrosOrdenados))
                    $final['registros'] = $registrosOrdenados;

                //-+-+-+-+-+-+-+-+-+-+-+-

                $vista = '';
                if ($programa->getId() == 14 || $programa->getId() == 15 || $programa->getId() == 16)
                    $vista = 'protocoloSuelos';
                else
                    $vista = 'protocoloAguas';

                $footer = $this->renderView('LaboratorioPedidoBundle:Default:footer.html.twig');
                $variables = array('resultados' => $final, 'numeroMuestraCompleto' => $muestra->getNumeroMuestraCompleto(), 'ausenciaGerente' => $ausenciaGerente);
                //return $this->render('LaboratorioPedidoBundle:Default:prueba.html.twig', array('resultados' => $variables));
                $html = $this->renderView('LaboratorioPedidoBundle:Default:'.$vista.'.html.twig', $variables);

                $snappy = $this->get('knp_snappy.pdf');
                $snappy->setOption('page-size', 'folio');
                //$snappy->setOption('page-width', 215); #extraoficio
                //$snappy->setOption('page-height', 355); #extraoficio
                $snappy->setOption('zoom', 1 );
                $snappy->setOption('dpi', 300 );
                //$snappy->setOption('header-html', $header);
                $snappy->setOption('footer-html', $footer); //El footer iría al final del protocolo
                //$snappy->setOption('footer-right', utf8_decode('Página [page] de [topage] -'.date('\ d-m-Y\ ')));
                //$snappy->setOption('footer-center', utf8_decode('PÃ¡gina [page]'));
                //$snappy->setOption('footer-font-size', 7);
                $snappy->setOption('margin-top', 5);
                $snappy->setOption('margin-right', 5);
                $snappy->setOption('margin-bottom', 21);
                $snappy->setOption('margin-left', 5);
                $snappy->setOption('images', true);
                $snappy->setOption('title', $muestra->getNumeroMuestraCompleto());

                $filename = sprintf('%s.pdf', $muestra->getNumeroMuestra());
                //sprintf('attachment; filename="%s"', $filename)

                return new Response(
                    $snappy->getOutputFromHtml($html),
                    200,
                    array(
                        'Content-Type'          => 'application/pdf',
                        'Content-Disposition'   => sprintf('filename="%s"', $filename)
                    )
                );
            }
        }
    }

    public function indexProtocoloAction(Request $request)
    {
        $data = array();
        $arrayMuestras = array();

        $j=1;
        $form = $this->createForm(Protocolo1Type::class,$data, array( 'action' => $this->generateUrl('laboratorio_pedido_protocolo_index' ) ) );

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {

                $muestras = $form->get('Muestras')->getData();
                //$protocolo = $form->get('Protocolo')->getData();                

                foreach ($muestras as $muestra) {
                    $linkMuestra['idMuestra'.$j] = encriptador::mrcrypt_encrypt($muestra->getId());
                    $j++;
                }

                for ($i=$j; $i < 7 ; $i++) {
                    $linkMuestra['idMuestra'.$i] = 0;
                }

                return $this->redirectToRoute('laboratorio_pedido_protocolo_protocolo1', array(
                    'idMuestra1' => $linkMuestra['idMuestra1'],
                    'idMuestra2' => $linkMuestra['idMuestra2'],
                    'idMuestra3' => $linkMuestra['idMuestra3'],
                    'idMuestra4' => $linkMuestra['idMuestra4'],
                    'idMuestra5' => $linkMuestra['idMuestra5'],
                    'idMuestra6' => $linkMuestra['idMuestra6']
                ));
            }

        }

        return $this->render('LaboratorioPedidoBundle:Default:indexProtocolo.html.twig' , array('form' => $form->createview()));
    }

    public function protocolo1Action(Request $request, $idMuestra1, $idMuestra2, $idMuestra3, $idMuestra4, $idMuestra5, $idMuestra6)
    {
        $final = array();
        $muestras = array();
        $final['registros'] = [];
        $idsAreas = [19 => 'CAMPO', 20 => 'BIOLOGICO', 21 => 'INSTRUMENTAL', 22 => 'FISICO-QUIMICA'];
        $tiposConlusiones = ['conclusionContacto', 'conclusionSinContacto', 'conclusionUsoPasivo', 'conclusionSinClasificar'];
        $tiposLegislaciones = ['legislacionContacto', 'legislacionSinContacto', 'legislacionUsoPasivo', 'legislacionSinClasificar'];

        $arg_list = func_get_args();
        $arg_list = array_slice($arg_list, 1);

        //Sacando los '0' de la lista de argumentos
        foreach ($arg_list as $key => $value)
            if (!$value)
                unset($arg_list[$key]);

        if (count($arg_list) <= 0)
            throw $this->createNotFoundException('No se seleccionó ninguna muestra');
        else
        {
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');

            //Verificando que, las muestras, existan
            foreach ($arg_list as $idMuestra)
            {
                $idMuestra = (int)encriptador::mrcrypt_decrypt($idMuestra);

                if ($idMuestra > 0)
                {
                    $muestra = $em->findOneByIdMuestra($idMuestra);

                    if ($muestra)
                        array_push($muestras, $muestra);
                }
            }

            if (count($muestras) <= 0)
                throw $this->createNotFoundException('No se encontró ninguna de las muestras especificadas');
            else
            {
                //Verificando que, todas las muestras, correspondan al mismo programa
                $programa = $muestras[0]->getPedido()->getPrograma();
                $i = 1;

                while ($i < count($muestras) && $programa->getId() == $muestras[$i]->getPedido()->getPrograma()->getId())
                    $i++;

                if ($i < count($muestras))
                    throw $this->createNotFoundException('Todas las muestras no corresponden al mismo programa');
                else
                {
                    //Programa del protocolo
                    $final['programa'] = $programa;

                    //Ordenar muestras
                    $muestras = $this->ordenarMuestras($muestras);

                    //Sacar la información por áreas
                    foreach ($idsAreas as $key => $value)
                    {
                        foreach ($muestras as $muestra)
                        {
                            //Agregar la muestra actual al array de muestras
                            $final['muestras'][$muestra->getId()] = $muestra;
                            //Sacar los resultados de la muestra, en el área actual
                            $resultadosAreaMuestra = $this->preProtocolo($muestra->getId(), $muestra->getNumeroMuestra(), $key);

                            if (!empty($resultadosAreaMuestra)) // (si hay resultados)
                            {
                                $conclusiones = $legislaciones = $columnas = array();
                                $conclusionArea = $observacionArea = '';
                                $mayor = $menor = null;

                                if ($key != 19) // (si no estamos en CAMPO, porque acá no hay fechas)
                                {
                                    //Encontrando el menor (sólo la primera fecha, o sea, el primer menor)...
                                    $i = 0;
                                    while ($i < count($resultadosAreaMuestra) && is_null($resultadosAreaMuestra[$i]['fechaInicioAnalisis']))
                                        $i++;

                                    if ($i < count($resultadosAreaMuestra)) // (se sabe que hay menor, es decir, hay fechas)
                                    {
                                        $menor = $resultadosAreaMuestra[$i]['fechaInicioAnalisis'];

                                        //sacando la fecha de finalización de análisis...
                                        foreach ($resultadosAreaMuestra as $resultado)
                                            if (!is_null($resultado['fechaInicioAnalisis']))
                                                if ($resultado['fechaInicioAnalisis'] < $menor)
                                                    $menor = $resultado['fechaInicioAnalisis'];
                                    }

                                    if ($key == 22) // (en FISICO-QUIMICA, uso siempre la FechaInicio, para mayor y menor)
                                    {
                                        //Encontrando el mayor (sólo la primera fecha, o sea, el primer mayor)...
                                        $i = 0;
                                        while ($i < count($resultadosAreaMuestra) && is_null($resultadosAreaMuestra[$i]['fechaInicioAnalisis']))
                                            $i++;

                                        if ($i < count($resultadosAreaMuestra)) // (se sabe que hay mayor, es decir, hay fechas)
                                        {
                                            $mayor = $resultadosAreaMuestra[$i]['fechaInicioAnalisis'];

                                            //sacando la fecha de finalización de análisis...
                                            foreach ($resultadosAreaMuestra as $resultado)
                                                if (!is_null($resultado['fechaInicioAnalisis']))
                                                    if ($resultado['fechaInicioAnalisis'] > $mayor)
                                                        $mayor = $resultado['fechaInicioAnalisis'];
                                        }
                                    }
                                    else
                                    {
                                        //Encontrando el mayor (sólo la primera fecha, o sea, el primer mayor)...
                                        $i = 0;
                                        while ($i < count($resultadosAreaMuestra) && is_null($resultadosAreaMuestra[$i]['fechaFinAnalisis']))
                                            $i++;

                                        if ($i < count($resultadosAreaMuestra)) // (se sabe que hay mayor, es decir, hay fechas)
                                        {
                                            $mayor = $resultadosAreaMuestra[$i]['fechaFinAnalisis'];

                                            //sacando la fecha de finalización de análisis...
                                            foreach ($resultadosAreaMuestra as $resultado)
                                                if (!is_null($resultado['fechaFinAnalisis']))
                                                    if ($resultado['fechaFinAnalisis'] > $mayor)
                                                        $mayor = $resultado['fechaFinAnalisis'];
                                        }
                                    }
                                }

                                foreach ($resultadosAreaMuestra as $resultado)
                                {
                                    //recopilando conclusiones de la muestra...
                                    foreach ($tiposConlusiones as $tipoConclusion)
                                        if ($resultado[$tipoConclusion])
                                            $conclusiones[$resultado[$tipoConclusion]] = $resultado[$tipoConclusion];

                                    //recopilando legislaciones...
                                    foreach ($tiposLegislaciones as $tipoLegislacion)
                                        if ($resultado[$tipoLegislacion])
                                            $legislaciones[($resultado[$tipoLegislacion])->getId()] = ($resultado[$tipoLegislacion])->getLegislacion();

                                    $final['registros'][$value][$muestra->getId()]['operadores'][$resultado['analista']] = $resultado['analista'];
                                }

                                $me = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:MuestraEstados');
                                $muestraEstado = $me->findOneBy(array('muestra' => $muestra->getId(), 'area' => $key));

                                if (!is_null($muestraEstado))
                                {
                                    //Conclusión (área)
                                    if (!is_null($muestraEstado->getConclusion()))
                                        $conclusionArea = $muestraEstado->getConclusion();

                                    //Observación (área)
                                    if (!is_null($muestraEstado->getObservacion()))
                                        $observacionArea = $muestraEstado->getObservacion();
                                }

                                //viendo qué resultados se muestran y cuáles no (en distintas filas)
                                for ($z = 0 ; $z < count($resultadosAreaMuestra) ; $z++)
                                {
                                    $resultadosAreaMuestra[$z]['mostrar'] = true;

                                    foreach ($final['registros'][$value] as $m)
                                        if ($resultadosAreaMuestra[$z]['mostrar'] && array_key_exists('resultados', $m))
                                            foreach ($m['resultados'] as $n)
                                                if ($resultadosAreaMuestra[$z]['mostrar'] && $n['nombreDeterminacion'] == $resultadosAreaMuestra[$z]['nombreDeterminacion'] && $n['metodologia'] == $resultadosAreaMuestra[$z]['metodologia'])
                                                    $resultadosAreaMuestra[$z]['mostrar'] = false;
                                }


                                $final['registros'][$value][$muestra->getId()]['conclusionArea'] = $conclusionArea;
                                $final['registros'][$value][$muestra->getId()]['observacionArea'] = $observacionArea;
                                $final['registros'][$value][$muestra->getId()]['legislaciones'] = $legislaciones;
                                $final['registros'][$value][$muestra->getId()]['conclusiones'] = $conclusiones;
                                $final['registros'][$value][$muestra->getId()]['fechaInicializacionAnalisis'] = $menor;
                                $final['registros'][$value][$muestra->getId()]['fechaFinalizacionAnalisis'] = $mayor;
                                $final['registros'][$value][$muestra->getId()]['columnas'] = $this->encontrarColumnas($resultadosAreaMuestra);
                                //$final['registros'][$value][$muestra->getId()]['muestras'][$muestra->getId()] = $muestra;
                                $final['registros'][$value][$muestra->getId()]['resultados'] = $resultadosAreaMuestra;
                            }
                        }
                        //exit();

                        $final['conclusiones'][$value] = [];

                        if (array_key_exists($value, $final['registros']))
                        {
                            $final['conclusiones'][$value] = $this->acotarConclusiones($final['registros'][$value], $value, $programa);

                            # Completando el array de conclusiones
                            $numerosMuestra = array();
                            foreach ($muestras as $m)
                                array_push($numerosMuestra, $m->getNumeroMuestra());

                            if (!empty($final['conclusiones']))
                            {
                                foreach ($numerosMuestra as $numu)
                                    foreach ($final['conclusiones'] as $ca)
                                        if (!empty($ca))
                                            if (!array_key_exists($numu, $ca))
                                                $final['conclusiones'][$value][$numu] = [];
                            }
                            else
                                foreach ($numerosMuestra as $numu)
                                    $final['conclusiones'][$value][$numu] = [];

                            //QUITAR DUPLICADOS DEL ARRAY DE OPERADORES
                            $analistas = array();
                            foreach ($final['registros'][$value] as $mu)
                                if (!empty($mu['operadores']))
                                    foreach ($mu['operadores'] as $analista)
                                        if (!$this->in_array_r($analista, $analistas))
                                            array_push($analistas, $analista);

                            $final['analistas'][$value] = $analistas;

                            //ENCONTRAR LA MENOR FECHA DE TODAS LAS MUESTRAS
                            $fechaInicializacionAnalisis = null;
                            foreach ($final['registros'][$value] as $registrosMuestra) // (ya sé que es la forma menos eficiente, pero buen...)
                                if (!is_null($registrosMuestra['fechaInicializacionAnalisis']))
                                    $fechaInicializacionAnalisis = $registrosMuestra['fechaInicializacionAnalisis'];

                            if ($fechaInicializacionAnalisis) // (se sabe que hay menor, es decir, hay fechas)
                            {
                                $rm = current($final['registros'][$value]);
                                $menor = $rm['fechaInicializacionAnalisis'];

                                foreach ($final['registros'][$value] as $registrosMuestra)
                                    if (!is_null($registrosMuestra['fechaInicializacionAnalisis']))
                                        if ($registrosMuestra['fechaInicializacionAnalisis'] < $menor)
                                            $menor = $registrosMuestra['fechaInicializacionAnalisis'];
                            }

                            $final['fechaInicializacionAnalisis'][$value] = $menor;

                            //ENCONTRAR LA MAYOR FECHA DE TODAS LAS MUESTRAS
                            $fechaFinalizacionAnalisis = null;
                            foreach ($final['registros'][$value] as $registrosMuestra) // (ya sé que es la forma menos eficiente, pero buen...)
                                if (!is_null($registrosMuestra['fechaFinalizacionAnalisis']))
                                    $fechaFinalizacionAnalisis = $registrosMuestra['fechaFinalizacionAnalisis'];

                            if ($fechaFinalizacionAnalisis) // (se sabe que hay mayor, es decir, hay fechas)
                            {
                                $rm = current($final['registros'][$value]);
                                $mayor = $rm['fechaFinalizacionAnalisis'];

                                foreach ($final['registros'][$value] as $registrosMuestra)
                                    if (!is_null($registrosMuestra['fechaFinalizacionAnalisis']))
                                        if ($registrosMuestra['fechaFinalizacionAnalisis'] > $mayor)
                                            $mayor = $registrosMuestra['fechaFinalizacionAnalisis'];
                            }

                            $final['fechaFinalizacionAnalisis'][$value] = $mayor;

                            //ORDENAR LAS DETERMINACIONES
                            if ($value == 'BIOLOGICO')
                            {
                                $ram = $results = $parameters = array();
                                foreach ($final['registros'][$value] as $kmue => $mue)
                                {
                                    // MUESTRA
                                    foreach ($mue['resultados'] as $kre => $re)
                                    {
                                        switch ($re['nombreDeterminacion'])
                                        {
                                            case 'Recuento bacterias aerobias mesófilas':
                                                $parameters[0] = $re;
                                                break;

                                            case 'Coliformes totales':
                                                $parameters[1] = $re;
                                                break;

                                            case 'Coliformes fecales':
                                                $parameters[2] = $re;
                                                break;

                                            case 'Escherichia coli':
                                                $parameters[3] = $re;
                                                break;

                                            case 'Estreptococos fecales':
                                                $parameters[4] = $re;
                                                break;

                                            case 'Enterococos':
                                                $parameters[5] = $re;
                                                break;

                                            default:
                                                if (array_key_exists(6, $parameters))
                                                    $parameters[count($parameters)] = $re;
                                                else
                                                    $parameters[6] = $re;
                                                break;
                                        }
                                    }

                                    ksort($parameters); # para ordenar por keys
                                    $mue['resultados'] = $parameters;

                                    $final['registros'][$value][$kmue]['resultados'] = $parameters;
                                }
                            }

                            //ORDENAR LAS DETERMINACIONES (método alternativo)
                            /*public function ordenaArray($array){
                                $numElemArray = count($array);
                                $isOrdered = false;
                                while(!$isOrdered){
                                $isOrdered = true;
                                    for($i = 1; $i < $numElemArray; $i++){
                                        if($array[$i] < $array[$i - 1]){
                                            $aux = $array[$i];
                                            $array[$i] = $array[$i - 1];
                                            $array[$i - 1] = $aux;  
                                            $isOrdered = false;
                                        }
                                    }
                                    
                                }   
                                return $array;  
                            }*/
                        }
                    }

                    $final['conclusionesAbreviadas'] = $this->abreviarConclusiones($final['conclusiones'], $muestras, $programa);
                    //return $this->render('LaboratorioPedidoBundle:Default:prueba.html.twig' , array('resultados' => $final['conclusionesAbreviadas']));

                    //Mirando si, en el array de muestras, hay diferencia de fechas
                    if (count($muestras) > 1)
                    {
                        $i = 1;
                        $fechaTM = $muestras[0]->getFechaTomaMuestra();
                        while ($i < count($muestras) && $fechaTM == $muestras[$i]->getFechaTomaMuestra())
                            $i++;

                        if ($i < count($muestras))
                            $final['diferenciaDeFechas'] = true;
                        else
                            $final['diferenciaDeFechas'] = false;
                    }
                    else
                        $final['diferenciaDeFechas'] = false;

                    //Ordenando las áreas intervinientes de acuerdo a criterio de la gerencia
                    $registrosOrdenados = array();
                    if (array_key_exists('CAMPO', $final['registros']))
                        $registrosOrdenados['CAMPO'] = $final['registros']['CAMPO'];

                    if (array_key_exists('FISICO-QUIMICA', $final['registros']))
                        $registrosOrdenados['FISICO-QUIMICA'] = $final['registros']['FISICO-QUIMICA'];

                    if (array_key_exists('BIOLOGICO', $final['registros']))
                        $registrosOrdenados['BIOLOGICO'] = $final['registros']['BIOLOGICO'];

                    if (array_key_exists('INSTRUMENTAL', $final['registros']))
                        $registrosOrdenados['INSTRUMENTAL'] = $final['registros']['INSTRUMENTAL'];

                    if (!empty($registrosOrdenados))
                        $final['registros'] = $registrosOrdenados;


                    //\Doctrine\Common\Util\Debug::dump($final['conclusionesAbreviadas']);
                    //exit();

                    # ************************************************************************************
                    # ************************************************************************************
                    # ************************************************************************************
                    // ARMAR PROTOCOLO Y PASAR DOCUMENTO ARMADO DESDE AQUÍ
                    /* $this->armarProtocolo($final); */
                    # ************************************************************************************
                    # ************************************************************************************
                    # ************************************************************************************

                    //return $this->render('LaboratorioPedidoBundle:Default:prueba.html.twig' , array('resultados' => $final));

                    $html = $this->renderView('LaboratorioPedidoBundle:Default:protocolo1.html.twig', array('resultados' => $final));

                    $footer = $this->renderView('LaboratorioPedidoBundle:Default:footer.html.twig');
                    //$header = $this->renderView('LaboratorioPedidoBundle:Default:header.html.twig', array(
                    //    'programa' => $final['programa']
                    //    )
                    //);

                    $snappy = $this->get('knp_snappy.pdf');
                    $snappy->setOption('page-size', 'folio');
                    $snappy->setOption('zoom', 1 );
                    $snappy->setOption('dpi', 300 );
                    //$snappy->setOption('header-html', $header);
                    $snappy->setOption('footer-html', $footer);
                    //$snappy->setOption('footer-right', utf8_decode('Página [page] de [topage] -'.date('\ d-m-Y\ ')));
                    //$snappy->setOption('footer-font-size', 8);
                    $snappy->setOption('margin-top', 5);
                    $snappy->setOption('margin-right', 5);
                    $snappy->setOption('margin-bottom', 20);
                    $snappy->setOption('margin-left', 5);

                    return new Response(
                        $snappy->getOutputFromHtml($html),
                        200,
                        array(
                            'Content-Type'          => 'application/pdf',
                            'Content-Disposition'   => 'filename="file.pdf"'
                        )
                    );
                }
            }
        }
    }

    private function preProtocolo($idMuestra, $numeroMuestra, $idArea)
    {
        $final =  array();

        if($idMuestra <= 0)
            throw $this->createNotFoundException('No existe la muestra especificada');
        else
        {
            $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
            $muestra = $em->findOneByIdMuestra($idMuestra);

            $resultados = $muestra->getResultados();

            $dl = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:DeterminacionLegislacion');
            $registros = array();
            $resultadoDeterminacion = 0;
            $conclusionContacto = $conclusionSinContacto = $conclusionUsoPasivo = $conclusionSinClasificar = $valorLimiteContacto = $valorLimiteSinContacto = $valorLimiteUsoPasivo = $valorLimiteSinClasificar = $legislacionSinClasificar = '';

            foreach ($resultados as $resultado)
            {
                if ($resultado->getDeterminacion()->getArea()->getId() == $idArea)
                {
                    /*\Doctrine\Common\Util\Debug::dump($resultado->getDeterminacion()->getNombre());
                    \Doctrine\Common\Util\Debug::dump($resultado->getResultado());
                    \Doctrine\Common\Util\Debug::dump($this->estaEnExponencial($resultado->getResultado()));*/

                    if ($this->estaEnExponencial($resultado->getResultado()))
                        $resultadoDeterminacion = $this->darNumeroReal($resultado->getResultado());
                    else
                        $resultadoDeterminacion = $resultado->getResultado();

                    $idPM = $muestra->getPedido()->getPrograma()->getId(); //para legislaciones especiales (sin clasificar)
                    if ($idPM == 1 || $idPM == 2 || $idPM == 8 || $idPM == 17 || $idPM == 21)
                    {
                        if ($resultado->getLegislacion())
                        {
                            $determinacionLegislacion = $dl->findOneBy(array('legislacion' => $resultado->getLegislacion()->getId(), 'determinacion' => $resultado->getDeterminacion()->getId(), 'tipo' => 4));

                            if($determinacionLegislacion)
                            {
                                $sinClasificar = $this->sacarConclusion($numeroMuestra, $resultadoDeterminacion, $determinacionLegislacion->getMin(), $determinacionLegislacion->getMax(), $determinacionLegislacion->getMinIgual(), $determinacionLegislacion->getMaxIgual(), 'conclusionSinClasificar', $resultado->getDeterminacion()->getNombre());
                                $legislacionSinClasificar = $resultado->getLegislacion();
                                //$valorLimiteSinClasificar = $sinClasificar['valorLimite'];
                                $valorLimiteSinClasificar = $determinacionLegislacion->getMostrarComo();
                                //$conclusionSinClasificar = $sinClasificar['conclusion'];
                            }
                        }
                        else
                            $valorLimiteSinClasificar = '';
                    }
                    else
                    {
                        if ($resultado->getLegislacion() != '')
                        {
                            $determinacionLegislacion = $dl->findOneBy(array('legislacion' => $resultado->getLegislacion()->getId(), 'determinacion' => $resultado->getDeterminacion()->getId(), 'tipo' => 1));

                            if($determinacionLegislacion)
                            {
                                $contacto = $this->sacarConclusion($numeroMuestra, $resultadoDeterminacion, $determinacionLegislacion->getMin(), $determinacionLegislacion->getMax(), $determinacionLegislacion->getMinIgual(), $determinacionLegislacion->getMaxIgual(), 'conclusionContacto', $resultado->getDeterminacion()->getNombre());
                                //$valorLimiteContacto = $contacto['valorLimite'];
                                $valorLimiteContacto = $determinacionLegislacion->getMostrarComo();
                                //$conclusionContacto = $contacto['conclusion'];
                            }/*
                            else
                            {
                                $determinacionLegislacion = $dl->findOneBy(array('legislacion' => $resultado->getLegislacion()->getId(), 'determinacion' => $resultado->getDeterminacion()->getId(), 'tipo' => 4));

                                if($determinacionLegislacion)
                                {
                                    $sinClasificar = $this->sacarConclusion($numeroMuestra, $resultadoDeterminacion, $determinacionLegislacion->getMin(), $determinacionLegislacion->getMax(), $determinacionLegislacion->getMinIgual(), $determinacionLegislacion->getMaxIgual(), 'conclusionSinClasificar', $resultado->getDeterminacion()->getNombre());
                                    $legislacionSinClasificar = $resultado->getLegislacion();
                                    //$valorLimiteSinClasificar = $sinClasificar['valorLimite'];
                                    $valorLimiteSinClasificar = $determinacionLegislacion->getMostrarComo();
                                    $conclusionSinClasificar = $sinClasificar['conclusion'];
                                }
                            }*/
                        }
                        else
                            $valorLimiteContacto = '';

                        if ($resultado->getLegislacionSinContacto() != '')
                        {
                            $determinacionLegislacion = $dl->findOneBy(array('legislacion' => $resultado->getLegislacionSinContacto()->getId(), 'determinacion' => $resultado->getDeterminacion()->getId(), 'tipo' => 2));

                            if ($determinacionLegislacion)
                            {
                                $sinContacto = $this->sacarConclusion($numeroMuestra, $resultadoDeterminacion, $determinacionLegislacion->getMin(), $determinacionLegislacion->getMax(), $determinacionLegislacion->getMinIgual(), $determinacionLegislacion->getMaxIgual(), 'conclusionSinContacto', $resultado->getDeterminacion()->getNombre());
                                //$valorLimiteSinContacto = $sinContacto['valorLimite'];
                                $valorLimiteSinContacto = $determinacionLegislacion->getMostrarComo();
                                //$conclusionSinContacto = $sinContacto['conclusion'];
                            }
                        }
                        else
                            $valorLimiteSinContacto = '';

                        if ($resultado->getLegislacionPasivo() != '')
                        {
                            $determinacionLegislacion = $dl->findOneBy(array('legislacion' => $resultado->getLegislacionPasivo()->getId(), 'determinacion' => $resultado->getDeterminacion()->getId(), 'tipo' => 3));

                            if ($determinacionLegislacion)
                            {
                                $usoPasivo = $this->sacarConclusion($numeroMuestra, $resultadoDeterminacion, $determinacionLegislacion->getMin(), $determinacionLegislacion->getMax(), $determinacionLegislacion->getMinIgual(), $determinacionLegislacion->getMaxIgual(), 'conclusionUsoPasivo', $resultado->getDeterminacion()->getNombre());
                                //$valorLimiteUsoPasivo = $usoPasivo['valorLimite'];
                                $valorLimiteUsoPasivo = $determinacionLegislacion->getMostrarComo();
                                //$conclusionUsoPasivo = $usoPasivo['conclusion'];
                            }
                        }
                        else
                            $valorLimiteUsoPasivo = '';
                    }

                    //$r = array();

                    $r = array(
                        'idDeterminacion' => $resultado->getDeterminacion()->getId(),
                        'nombreDeterminacion' => $resultado->getDeterminacion()->getNombre(),
                        'tipoDato' => $resultado->getDeterminacion()->getTipoDato(),
                        'resultado' => $resultadoDeterminacion,
                        'unidadMedida' => $resultado->getDeterminacion()->getUnidadMedida(),
                        'legislacionContacto' => $resultado->getLegislacion(),
                        'valorLimiteContacto' => $valorLimiteContacto,
                        //'conclusionContacto' => $conclusionContacto,
                        'legislacionSinContacto' => $resultado->getLegislacionSinContacto(),
                        'valorLimiteSinContacto' => $valorLimiteSinContacto,
                        //'conclusionSinContacto' => $conclusionSinContacto,
                        'legislacionUsoPasivo' => $resultado->getLegislacionPasivo(),
                        'valorLimiteUsoPasivo' => $valorLimiteUsoPasivo,
                        //'conclusionUsoPasivo' => $conclusionUsoPasivo,
                        'legislacionSinClasificar' => $legislacionSinClasificar,
                        'valorLimiteSinClasificar' => $valorLimiteSinClasificar,
                        //'conclusionSinClasificar' => $conclusionSinClasificar,
                        'metodologia' => $resultado->getDeterminacion()->getMetodologia(),
                        //'analista' => $resultado->getUsuario()->getApellido().', '.$resultado->getUsuario()->getNombre(),
                        'fechaInicioAnalisis' => $resultado->getFechaInicioAnalisis(),
                        'fechaFinAnalisis' => $resultado->getFechaFinAnalisis(),
                        'programa' => $resultado->getMuestra()->getPedido()->getPrograma()->getPrograma(),
                        'limiteCuantificable' => ''
                    );

                    if ($resultadoDeterminacion == 0)
                        $r['limiteCuantificable'] = $resultado->getDeterminacion()->getLimiteCuantificable();

                    array_push($registros, $r);
                }
            }

            //\Doctrine\Common\Util\Debug::dump($registros);
            //exit();
            return $registros;
        }
    }

    private function ordenarMuestras ($muestras)
    {
        if (!empty($muestras))
        {
            $aux = array();

            foreach ($muestras as $m)
                array_push($aux, $m->getNumeroMuestra());

            sort($aux);

            $aux2 = array();

            foreach ($aux as $a)
            {
                $i = 0;
                while ($i < count($muestras) && $a != $muestras[$i]->getNumeroMuestra())
                    $i++;

                if ($i < count($muestras))
                    array_push($aux2, $muestras[$i]);
            }

            $muestras = $aux2;
        }

        return $muestras;
    }

    private function encontrarColumnas ($resultados)
    {
        $columnas = array();
        $i = 0;

        while ($i < count($resultados) && $this->noHayNiUnaLegislacion($resultados[$i]))
            $i++;

        if ($i < count($resultados)) // (hay, por lo menos una, legislación)
        {
            $legislacionContacto = $legislacionSinContacto = $legislacionUsoPasivo = $legislacionSinClasificar = false;
            $i = 0;

            //$legislacionContacto && $legislacionSinContacto
            //$legislacionSinContacto && $legislacionUsoPasivo
            //$legislacionContacto && $legislacionSinClasificar
            //while ($i < count($resultados) && ( () || () || () ))

            while ($i < count($resultados) && (!$legislacionContacto || !$legislacionSinContacto || !$legislacionUsoPasivo || !$legislacionSinClasificar))
            {
                if (!$legislacionContacto)
                {
                    if ($this->hayLegislacion($resultados[$i]['legislacionContacto'], $resultados[$i]['valorLimiteContacto']))
                    {
                        $legislacionContacto = true;
                        array_push($columnas, 'legislacionContacto');
                    }
                }

                if (!$legislacionSinContacto)
                {
                    if ($this->hayLegislacion($resultados[$i]['legislacionSinContacto'], $resultados[$i]['valorLimiteSinContacto']))
                    {
                        $legislacionSinContacto = true;
                        array_push($columnas, 'legislacionSinContacto');
                    }
                }

                if (!$legislacionUsoPasivo)
                {
                    if ($this->hayLegislacion($resultados[$i]['legislacionUsoPasivo'], $resultados[$i]['valorLimiteUsoPasivo']))
                    {
                        $legislacionUsoPasivo = true;
                        array_push($columnas, 'legislacionUsoPasivo');
                    }
                }

                if (!$legislacionSinClasificar)
                {
                    if ($this->hayLegislacion($resultados[$i]['legislacionSinClasificar'], $resultados[$i]['valorLimiteSinClasificar']))
                    {
                        $legislacionSinClasificar = true;
                        array_push($columnas, 'legislacionSinClasificar');
                    }
                }

                $i++;
            }
        }

        //$columnas = $this->analizarColumnas($columnas);

        //Ordenar columnas en vez de analizar y quitar. Si hay algún error, es más fácil detectarlo así
        $columnas = $this->ordenarColumnas($columnas);

        return $columnas;
    }

    private function ordenarColumnas ($columnas)
    {
        if (!empty($columnas))
        {
            if (count($columnas) > 1)
            {
                sort($columnas);

                if ($this->in_array_r('legislacionSinClasificar', $columnas))
                {
                    $i = 0;
                    while ($i < count($columnas) && $columnas[$i] != 'legislacionSinClasificar')
                        $i++;

                    if ($i < (count($columnas) - 1))
                    {
                        $columnas[$i] = $columnas[count($columnas)-1];
                        $columnas[count($columnas)-1] = 'legislacionSinClasificar';
                    }
                }
            }
        }

        return $columnas;
    }

    private function analizarColumnas($columnas)
    {
        //$legislacionContacto && $legislacionSinContacto
        //$legislacionSinContacto && $legislacionUsoPasivo
        //$legislacionContacto && $legislacionSinClasificar

        if (!empty($columnas))
        {
            if (count($columnas) == 1)
            {
                switch ($columnas[0])
                {
                    case 'legislacionContacto':
                        array_push($columnas, 'legislacionSinContacto');
                        break;

                    case 'legislacionSinContacto':
                        array_push($columnas, 'legislacionUsoPasivo');
                        break;

                    case 'legislacionUsoPasivo':
                        array_unshift($columnas, 'legislacionSinContacto');
                        break;

                    case 'legislacionSinClasificar':
                        array_unshift($columnas, 'legislacionContacto');
                        break;
                }
            }
            else
            {
                if (count($columnas) == 2)
                {
                    if ($this->in_array_r('legislacionContacto', $columnas))
                    {
                        if ($this->in_array_r('legislacionSinContacto', $columnas))
                        {
                            //OK
                            //$legislacionContacto && $legislacionSinContacto
                        }
                        else
                        {
                            if ($this->in_array_r('legislacionSinClasificar', $columnas))
                            {
                                //OK
                                //$legislacionContacto && $legislacionSinClasificar
                            }
                            else
                            {
                                //ERROR
                                unset($columnas);

                                $columnas = ['legislacionContacto', 'legislacionSinContacto', 'legislacionUsoPasivo', 'legislacionSinClasificar'];
                            }
                        }
                    }
                    else
                    {
                        if ($this->in_array_r('legislacionSinContacto', $columnas))
                        {
                            if ($this->in_array_r('legislacionUsoPasivo', $columnas))
                            {
                                //OK
                                //$legislacionSinContacto && $legislacionUsoPasivo
                            }
                            else
                            {
                                //ERROR
                                unset($columnas);

                                $columnas = ['legislacionContacto', 'legislacionSinContacto', 'legislacionUsoPasivo', 'legislacionSinClasificar'];
                            }
                        }
                        else
                        {
                            //ERROR
                            unset($columnas);

                            $columnas = ['legislacionContacto', 'legislacionSinContacto', 'legislacionUsoPasivo', 'legislacionSinClasificar'];
                        }
                    }
                }
                else
                {
                    //Más de 2 columnas
                    //ERROR
                    if (count($columnas) == 3 || count($columnas) == 4)
                    {
                        //Algo pasa
                        //ERROR

                        unset($columnas);

                        $columnas = ['legislacionContacto', 'legislacionSinContacto', 'legislacionUsoPasivo', 'legislacionSinClasificar'];
                    }
                }
            }
        }

        return $columnas;
    }

    private function sacarConclusion($numeroMuestra, $resultado, $min, $max, $minIgual, $maxIgual, $tipoConclusion, $nombreDeterminacion)
    {
        $arreglo = array();
        $conclusion = '';
        $arreglo['valorLimite'] = '';
        $arreglo['conclusion'] = '';

        //Si hay mínimo o máximo
        if (!is_null($min) || !is_null($max))
        {
            //Hay mínimo o máximo, distinto de 0 o ''
            if (($min != '' && !is_null($min)) || ($max != '' && !is_null($max)))
            {
                //$arreglo['valorLimite'] = $this->darValorLimite($min, $max, $minIgual, $maxIgual);
                $intro = 'El valor de '.$nombreDeterminacion.' en la muestra '.$numeroMuestra.' ';

                //Hay mínimo y máximo
                if (($min != '' && !is_null($min)) && ($max != '' && !is_null($max)))
                {
                    if ($resultado < $min)
                        $conclusion .= 'es inferior al ';
                    else
                    {
                        if ($resultado == $min)
                        {
                            if (!$minIgual)
                                $conclusion .= 'es inferior al ';
                        }
                        else
                        {
                            if ($resultado == $max)
                            {
                                if (!$maxIgual)
                                    $conclusion .= 'es superior al ';
                            }
                            else
                                if ($resultado > $max)
                                    $conclusion .= 'es superior al ';
                        }
                    }
                }
                else
                {
                    //Hay mínimo o máximo
                    if ($min != '' && !is_null($min))
                    {
                        if ($resultado < $min)
                            $conclusion .= 'es inferior al ';
                        else
                            if ($resultado == $min)
                                if (!$minIgual)
                                    $conclusion .= 'es inferior al ';
                    }
                    elseif ($max != '' && !is_null($max))
                    {
                        if ($resultado > $max)
                            $conclusion .= 'es superior al ';
                        else
                            if ($resultado == $max)
                                if (!$maxIgual)
                                    $conclusion .= 'es superior al ';
                    }
                }

                if ($conclusion != '')
                {
                    switch ($tipoConclusion)
                    {
                        case 'conclusionContacto':
                            $tipoConclusion = ' con contacto directo';
                            break;

                        case 'conclusionSinContacto':
                            $tipoConclusion = ' sin contacto directo';
                            break;

                        case 'conclusionUsoPasivo':
                            $tipoConclusion = ' de uso pasivo';
                            break;

                        case 'conclusionSinClasificar':
                            $tipoConclusion = ' sin clasificar';
                            break;
                    }

                    $arreglo['conclusion'] = $intro.$conclusion.'valor referencial'.$tipoConclusion;
                }
            }
        }

        return $arreglo;
    }

    private function acotarConclusiones($registrosArea, $area, $programa)
    {
        //return $registrosArea;
        //\Doctrine\Common\Util\Debug::dump($conclusiones);
        //exit();
        $conclusiones = array();

        foreach ($registrosArea as $datosMuestra)
        {
            if (!empty($datosMuestra['conclusiones']))
            {
                foreach ($datosMuestra['conclusiones'] as $conclusion)
                {
                    $pos = ((strpos($conclusion, 'muestra ')) + 8);
                    $numero = $conclusion[$pos];
                    $i = 1;
                    while ($conclusion[$pos+$i-1] != ' ')
                    {
                        $numero .= $conclusion[$pos+$i];
                        $i++;
                    }
                    $numero = substr($numero, 0, -1); #numeroMuestra

                    #------

                    $parametro = '';
                    $pos = strpos($conclusion, ' en la muestra');
                    $i = 0;
                    while (($i+12) != $pos)
                    {
                        $parametro .= $conclusion[$i+12];
                        $i++;
                    } #parametro

                    #------

                    if (strpos($conclusion, 'con contacto directo') !== false)
                        $conclusiones[$numero]['determinaciones'][$parametro]['conclusionContacto'][$conclusion] = $conclusion;
                    elseif (strpos($conclusion, 'sin contacto directo') !== false)
                        $conclusiones[$numero]['determinaciones'][$parametro]['conclusionSinContacto'][$conclusion] = $conclusion;
                    elseif (strpos($conclusion, 'uso pasivo') !== false)
                        $conclusiones[$numero]['determinaciones'][$parametro]['conclusionUsoPasivo'][$conclusion] = $conclusion;
                    elseif (strpos($conclusion, 'sin clasificar') !== false)
                        $conclusiones[$numero]['determinaciones'][$parametro]['conclusionSinClasificar'][$conclusion] = $conclusion; #tipoConclusion
                }
            }
        }

        return $conclusiones;
    }

    private function abreviarConclusiones ($conclusiones, $muestras, $programa)
    {
        //sacar 'en la muestra'
        $conclusionesAux = $conclusiones;
        foreach ($conclusionesAux as $area => $areaMuestras)
        {
            foreach ($areaMuestras as $numeroMuestra => $datosMuestra)
            {
                if (!empty($datosMuestra))
                {
                    foreach ($datosMuestra['determinaciones'] as $parametro => $conclusionesParametro)
                    {
                        foreach ($conclusionesParametro as $tipoConclusion => $conclusionImaginaria)
                        {
                            foreach ($conclusionImaginaria as $conclusion)
                            {
                                $pos = strpos($conclusion, 'en la muestra');
                                $primeraParte = substr($conclusion, 0, $pos);

                                $pos = $pos + 15;
                                while ($conclusion[$pos] != ' ')
                                    $pos++;
                                $segundaParte = substr($conclusion, ($pos+1), (strlen($conclusion)-1));

                                unset($conclusiones[$area][$numeroMuestra]['determinaciones'][$parametro][$tipoConclusion][$conclusion]);
                                $conclusiones[$area][$numeroMuestra]['determinaciones'][$parametro][$tipoConclusion][$primeraParte.$segundaParte] = $primeraParte.$segundaParte;
                            }
                        }
                    }
                }
            }
        }

        //return $conclusiones;

        //--------------------

        $conclusionesAbreviadas = array();

        foreach ($conclusiones as $area => $areaMuestras)
        {
            $conclusionesAbreviadas[$area]['conclusionGeneral'] = '';

            $tieneAlgo = false;
            foreach ($areaMuestras as $numeroMuestra => $datosMuestra)
                if (!empty($datosMuestra))
                    $tieneAlgo = true;

            //CAMBIA EN 3, 5 Y 6 (La muestra no supera el valor referencial para uso recreativo con y sin contacto directo)
            $singular = $plural = '';
            if ($programa->getId() == 3 || $programa->getId() == 5 || $programa->getId() == 6)
            {
                $singular = 'La muestra no supera el valor referencial para uso recreativo con y sin contacto directo';
                $plural = 'Las muestras no superan el valor referencial para uso recreativo con y sin contacto directo';
            }
            else
            {
                $singular = 'Muestra apta';
                $plural = 'Todas las muestras son aptas';
            }

            if (!$tieneAlgo) # Todas las muestras están vacías
            {
                if (count($areaMuestras) == 1)
                    $conclusionesAbreviadas[$area]['conclusionGeneral'] = $singular;
                else
                    $conclusionesAbreviadas[$area]['conclusionGeneral'] = $plural;
            }
            else # Hay muestras que tienen datos por analizar
            {
                foreach ($areaMuestras as $numeroMuestra => $datosMuestra)
                {
                    $numeroMuestra = $this->darNumeroMuestraCompleto($numeroMuestra, $muestras, $programa); # ----------------

                    $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['conclusionMuestra'] = '';

                    if (empty($datosMuestra))
                        $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['conclusionMuestra'] = $singular;
                    else
                    {
                        foreach ($datosMuestra['determinaciones'] as $parametro => $conclusionesParametro)
                        {
                            $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['conclusionParametro'] = '';

                            if (count($conclusionesParametro) > 1)
                            {
                                $i = 0; # (inútil)
                                while ((list(, $conclusion) = each($conclusionesParametro)) && (strpos(current($conclusion), 'inferior') !== false))
                                    $i++; # (inútil)

                                if (!$conclusion)
                                { # Ambos inferiores
                                    //\Doctrine\Common\Util\Debug::dump($conclusionesParametro);
                                    //\Doctrine\Common\Util\Debug::dump('Ambos inferiores');
                                    //\Doctrine\Common\Util\Debug::dump($datosMuestra['determinaciones'][$parametro]['conclusionContacto']);
                                    //exit();

                                    $c = '';
                                    while ((list(, $k) = each($datosMuestra['determinaciones'][$parametro])) && $c == '')
                                        $c = current($k);

                                    $pos = strpos($c, 'valor referencial');
                                    $primeraParte = substr($c, 0, ($pos + 18));
                                    //\Doctrine\Common\Util\Debug::dump($primeraParte);
                                    //exit();

                                    $segundaParte = '';
                                    $afectados = array();
                                    foreach ($datosMuestra['determinaciones'][$parametro] as $n => $p)
                                    {
                                        switch ($n)
                                        {
                                            case 'conclusionContacto':
                                                array_push($afectados, 'con contacto');
                                                break;

                                            case 'conclusionSinContacto':
                                                array_push($afectados, 'sin contacto');
                                                break;

                                            case 'conclusionUsoPasivo':
                                                array_push($afectados, 'de uso pasivo');
                                                break;

                                            case 'conclusionSinClasificar':
                                                array_push($afectados, 'sin clasificar');
                                                break;

                                            default:
                                                break;
                                        }
                                    }

                                    if (count($afectados) == 1)
                                        $segundaParte = current($afectados);
                                    else
                                    {
                                        if (count($afectados) == 2)
                                        {
                                            if ($afectados[0] == 'con contacto' && $afectados[1] == 'sin contacto')
                                                $segundaParte = 'con y sin contacto directo';
                                            else
                                                $segundaParte = $afectados[0].' y '.$afectados[1];
                                        }
                                        elseif (count($afectados) > 2)
                                        {
                                            foreach ($afectados as $af)
                                            {
                                                if ($af == $afectados[0])
                                                    $segundaParte .= $af; # primer elemento
                                                else
                                                {
                                                    if ($af == end($afectados))
                                                        $segundaParte .= ' y '.$af; # último elemento
                                                    else
                                                        $segundaParte .= ', '.$af; # del medio
                                                }
                                            }
                                        }
                                    }

                                    //\Doctrine\Common\Util\Debug::dump($primeraParte.$segundaParte);
                                    //exit();

                                    $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['conclusionParametro'] = $primeraParte.$segundaParte;

                                    foreach ($conclusionesParametro as $tipoConclusion => $conclusion)
                                        $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['tiposConclusiones'][$tipoConclusion][current($conclusion)] = current($conclusion);
                                }
                                else
                                {
                                    $i = 0; # (inútil)
                                    while ((list(, $conclusion) = each($conclusionesParametro)) && (strpos(current($conclusion), 'superior') !== false))
                                        $i++; # (inútil)

                                    if (!$conclusion)
                                    { # Ambos superiores
                                        //\Doctrine\Common\Util\Debug::dump($conclusionesParametro);
                                        //\Doctrine\Common\Util\Debug::dump('Ambos superiores');

                                        $c = '';
                                        while ((list(, $k) = each($datosMuestra['determinaciones'][$parametro])) && $c == '')
                                            $c = current($k);

                                        $pos = strpos($c, 'valor referencial');
                                        $primeraParte = substr($c, 0, ($pos + 18));
                                        //\Doctrine\Common\Util\Debug::dump($primeraParte);
                                        //exit();

                                        $segundaParte = '';
                                        $afectados = array();
                                        foreach ($datosMuestra['determinaciones'][$parametro] as $n => $p)
                                        {
                                            switch ($n)
                                            {
                                                case 'conclusionContacto':
                                                    array_push($afectados, 'con contacto');
                                                    break;

                                                case 'conclusionSinContacto':
                                                    array_push($afectados, 'sin contacto');
                                                    break;

                                                case 'conclusionUsoPasivo':
                                                    array_push($afectados, 'de uso pasivo');
                                                    break;

                                                case 'conclusionSinClasificar':
                                                    array_push($afectados, 'sin clasificar');
                                                    break;

                                                default:
                                                    break;
                                            }
                                        }

                                        if (count($afectados) == 1)
                                            $segundaParte = current($afectados);
                                        else
                                        {
                                            if (count($afectados) == 2)
                                            {
                                                if ($afectados[0] == 'con contacto' && $afectados[1] == 'sin contacto')
                                                    $segundaParte = 'con y sin contacto directo';
                                                else
                                                    $segundaParte = $afectados[0].' y '.$afectados[1];
                                            }
                                            elseif (count($afectados) > 2)
                                            {
                                                foreach ($afectados as $af)
                                                {
                                                    if ($af == $afectados[0])
                                                        $segundaParte .= $af; # primer elemento
                                                    else
                                                    {
                                                        if ($af == end($afectados))
                                                            $segundaParte .= ' y '.$af; # último elemento
                                                        else
                                                            $segundaParte .= ', '.$af; # del medio
                                                    }
                                                }
                                            }
                                        }

                                        $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['conclusionParametro'] = $primeraParte.$segundaParte;

                                        foreach ($conclusionesParametro as $tipoConclusion => $conclusion)
                                            $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['tiposConclusiones'][$tipoConclusion][current($conclusion)] = current($conclusion);
                                    }
                                    else
                                    { # Ni uno ni otro
                                        foreach ($conclusionesParametro as $tipoConclusion => $conclusion)
                                            $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['tiposConclusiones'][$tipoConclusion][current($conclusion)] = current($conclusion);
                                    }
                                }
                                //\Doctrine\Common\Util\Debug::dump(current($conclusion));
                                //\Doctrine\Common\Util\Debug::dump('-----------------------');
                                //exit();
                                //\Doctrine\Common\Util\Debug::dump('$datosMuestra');
                                //exit();
                            }
                            else
                            { # Sólo una conclusión
                                foreach ($conclusionesParametro as $tipoConclusion => $conclusion)
                                    $conclusionesAbreviadas[$area]['muestras'][$numeroMuestra]['determinaciones'][$parametro]['tiposConclusiones'][$tipoConclusion][current($conclusion)] = current($conclusion);
                            }
                        }
                        //exit();
                    }
                }
            }

            //\Doctrine\Common\Util\Debug::dump($datosMuestra);
            //exit();
        }

        //exit();
        return $conclusionesAbreviadas;
    }

    private function darValorLimite($min, $max, $minIgual, $maxIgual)
    {
        if ($min == '' && $max == '')
            return '';
        else
        {
            if ($min != '' && $max != '') {
                if ($minIgual && $maxIgual)
                    return $min.' ≤ x ≤ '.$max;
                elseif ($minIgual && !$maxIgual)
                    return $min.' ≤ x < '.$max;
                elseif (!$minIgual && $maxIgual)
                    return $min.' < x ≤ '.$max;
                else
                    return $min.' < x < '.$max;
            }
            else
            {
                if ($min != '')
                    if ($minIgual)
                        return 'x ≥ '.$min;
                    else
                        return 'x > '.$min;
                else
                    if ($maxIgual)
                        return 'x ≤ '.$max;
                    else
                        return 'x < '.$max;
            }
        }
    }

    private function estaEnExponencial($resultado)
    {
        $numero = str_split($resultado);

        switch ($numero[0]) {
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
            case '-':
            case '.':
                if ($this->in_array_r('E', $numero))
                    return true;
                else
                    return false;
                break;

            default:
                return false;
                break;
        }

        /*if (is_int($numero[0]) || $numero[0] == '-' || $numero[0] == '.')
        {
            if ($this->in_array_r('E', $numero))
                return true;
            else
                return false;
        }
        else
            return false;*/
    }

    private function darNumeroReal($exponencial)
    {
        //Nunca puede ser nulo (es un resultado... no podrían haber cargado '')
        $exponencial = explode('E', $exponencial);
        //$exponencial = (string)($exponencial[0] * (pow(10, $exponencial[1])));
        $numero = $exponencial[0] * (10**$exponencial[1]);
        //$exponencial = $this->easy_number_format($exponencial, ',', '.');
        //\Doctrine\Common\Util\Debug::dump(gettype($numero));
        //exit();

        return $numero;
    }

    private function easy_number_format($number, $dec_point, $thousands_sep)
    {
        $number = rtrim(sprintf('%f', $number), "0");
        if (fmod($number, 1) != 0) {
            $array_int_dec = explode('.', $number);
        } else {
            $array_int_dec= array(strlen($number), 0);
        }
        (strlen($array_int_dec[1]) < 2) ? ($decimals = 2) : ($decimals = strlen($array_int_dec[1]));
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    private function hayLegislacion($legislacion, $valorLimite)
    {
        if (($valorLimite != '') && (!is_null($legislacion)) && ($legislacion != '') && ($legislacion->getLegislacion() != ''))
            return true;
        else
            return false;
    }

    private function noHayLegislacion($legislacion, $valorLimite)
    {
        if ($valorLimite == '')
            return true;
        else
        {
            if (is_null($legislacion))
                return true;
            else
            {
                if ($legislacion == '')
                    return true;
                else
                {
                    if ($legislacion->getLegislacion() == '')
                        return true;
                    else
                        return false;
                }
            }
        }
    }

    private function noHayNiUnaLegislacion($resultado)
    {
        /*\Doctrine\Common\Util\Debug::dump($resultado['legislacionContacto']->getLegislacion());
        \Doctrine\Common\Util\Debug::dump($resultado['legislacionSinContacto']->getLegislacion());
        \Doctrine\Common\Util\Debug::dump($resultado['legislacionUsoPasivo']->getLegislacion());
        \Doctrine\Common\Util\Debug::dump($resultado['legislacionSinClasificar']->getLegislacion());
        exit();*/
        if ($this->noHayLegislacion($resultado['legislacionContacto'], $resultado['valorLimiteContacto']) && $this->noHayLegislacion($resultado['legislacionSinContacto'], $resultado['valorLimiteSinContacto']) && $this->noHayLegislacion($resultado['legislacionUsoPasivo'], $resultado['valorLimiteUsoPasivo']) && $this->noHayLegislacion($resultado['legislacionSinClasificar'], $resultado['valorLimiteSinClasificar']))
            return true;
        else
            return false;
    }

    public function indexProtocolo2Action(Request $request)
    {
        $data = array();
        $arrayMuestras = array();

        $j=1;
        $form = $this->createForm(Protocolos2Type::class,$data, array( 'action' => $this->generateUrl('laboratorio_protocolo_index2' ) ) );

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {

                $fechaInicio = $form->get('fechaInicio')->getData()->format('Y-m-d H:i:s');
                $fechaFin = $form->get('fechaFin')->getData()->format('Y-m-d H:i:s');
                $programa = $form->get('Programa')->getData();
                $protocolo = $form->get('Protocolo')->getData();

                return $this->redirectToRoute('laboratorio_protocolo2_protocolo1', array(
                    'fechaInicio' => encriptador::mrcrypt_encrypt($fechaInicio),
                    'fechaFin' => encriptador::mrcrypt_encrypt($fechaFin),
                    'programa' => encriptador::mrcrypt_encrypt($programa->getId()),
                    'protocolo' => encriptador::mrcrypt_encrypt($protocolo)
                ));
            }

        }

        return $this->render('LaboratorioPedidoBundle:Default:indexProtocolos2.html.twig' , array('form' => $form->createview()));
    }

    public function protocolo2Action(Request $request, $fechaInicio, $fechaFin, $programa, $protocolo)
    {
        $matriz = array();
        $i = 0;

        $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:Muestra');
        $muestras = $em->findMuestraByDate(20,encriptador::mrcrypt_decrypt($programa),encriptador::mrcrypt_decrypt($fechaInicio),encriptador::mrcrypt_decrypt($fechaFin));

        foreach ($muestras as $muestra) {
            $matriz[$i] = array();

            //var_dump($muestra->getPedido()->getEstablecimiento()->getId());
            $establecimiento = $muestra->getPedido()->getEstablecimiento();

            //Obtengo la institucion.
            $razonesSociales = $establecimiento->getRazonesSociales();
            if(count($razonesSociales) > 0){
                $tipoRazonSocial = $razonesSociales[0]->getRazonSocial()->getTipo();
                if($tipoRazonSocial == "F"){
                    $matriz[$i]['institucion'] = $razonesSociales[0]->getRazonSocial()->getNombre1()." ".$razonesSociales[0]->getRazonSocial()->getNombre2();
                }else{
                    $matriz[$i]['institucion'] = $razonesSociales[0]->getRazonSocial()->getNombre1();
                }
            }
            //fin institucion

            $direcciones = $establecimiento->getDirecciones();
            $matriz[$i]['direccion'] = $direcciones[0]->__toString();
            $matriz[$i]['comuna'] = $direcciones[0]->getComuna();
            $matriz[$i]['muestra'] = $muestra->getId();
            $matriz[$i]['natatorio'] = $muestra->getPuntoExtraccion();
            $matriz[$i]['esApta'] = "APTA";
            $matriz[$i]['analista'] = array();
            $matriz[$i]['determinaciones'] = array();
            $matriz[$i]['metodologia'] = array();
            $matriz[$i]['legislacion'] = array();

            $resultados = $muestra->getResultados();
            $matriz[$i]['fechaInicio'] = "";
            $matriz[$i]['fechaFin'] = "";
            foreach ($resultados as $resultado) {
                if($matriz[$i]['fechaInicio'] == ""){
                    $matriz[$i]['fechaInicio'] = $resultado->getFechaInicioAnalisis();
                }
                if($matriz[$i]['fechaFin'] == ""){
                    $matriz[$i]['fechaInicio'] = $resultado->getFechaInicioAnalisis();
                }

                if($matriz[$i]['fechaInicio'] > $resultado->getFechaInicioAnalisis() && $resultado->getDeterminacion()->getArea()->getId() == 19){
                    $matriz[$i]['fechaInicio'] = $resultado->getFechaInicioAnalisis();
                }

                if($matriz[$i]['fechaFin'] < $resultado->getFechaFinAnalisis()){
                    //if($matriz[$i]['fechaFin'] < $resultado->getFechaFinAnalisis() && $resultado->getDeterminacion()->getArea()->getId() == 20){
                    $matriz[$i]['fechaFin'] = $resultado->getFechaFinAnalisis();
                }

                //if($resultado->getDeterminacion()->getArea()->getId() == 20){
                $analista = $resultado->getUsuario()->getApellido().", ".$resultado->getUsuario()->getNombre();
                if(!in_array($analista, $matriz[$i]['analista'])){
                    array_push($matriz[$i]['analista'],$analista);
                }
                $metodologia = $resultado->getDeterminacion()->getMetodologia();
                if(!in_array($metodologia, $matriz[$i]['metodologia'])){
                    array_push($matriz[$i]['metodologia'],$metodologia);
                }
                /*$legislacion = $resultado->getLegislacion()->getLegislacion();
                if(!in_array($legislacion, $matriz[$i]['legislacion'])){
                    array_push($matriz[$i]['legislacion'],$legislacion);
                }

                $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()] = array();
                $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:DeterminacionLegislacion');
                $determinacionLegislacion = $em->findOneBy(array('legislacion' => $resultado->getLegislacion()->getId(), 'determinacion' =>  $resultado->getDeterminacion()->getId() ));

                if(!$determinacionLegislacion){
                    throw $this->createNotFoundException('Legislacion no encontrada');
                }*/

                if($resultado->getLegislacion() != null)
                {
                    $legislacion = $resultado->getLegislacion()->getLegislacion();
                    if(!in_array($legislacion, $matriz[$i]['legislacion'])){
                        array_push($matriz[$i]['legislacion'],$legislacion);
                    }

                    $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()] = array();
                    $em = $this->getDoctrine()->getRepository('LaboratorioPedidoBundle:DeterminacionLegislacion');
                    $determinacionLegislacion = $em->findOneBy(array('legislacion' => $resultado->getLegislacion()->getId(), 'determinacion' =>  $resultado->getDeterminacion()->getId() ));

                    if(!$determinacionLegislacion){
                        throw $this->createNotFoundException('Legislacion no encontrada');
                    }

                    $min = $determinacionLegislacion->getMin();
                    $max = $determinacionLegislacion->getMax();

                    $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['min'] = $min;
                    $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['max'] = $max;

                    if($resultado->getResultado() == 0){
                        $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['resultado'] = $resultado->getDeterminacion()->getLimiteCuantificacble();
                    }else{
                        $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['resultado'] = $resultado->getResultado();

                        //ACA HAY Q REVISARA SI ES SVRBAM o SMAVRBAM
                        if(($min && $resultado->getResultado() < $min) || ($max && $resultado->getResultado() > $max)){
                            $matriz[$i]['esApta'] = "NO APTA";
                        }
                    }
                }

                /*$min = $determinacionLegislacion->getMin();
                $max = $determinacionLegislacion->getMax();

                $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['min'] = $min;
                $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['max'] = $max;

                if($resultado->getResultado() == 0){
                    $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['resultado'] = $resultado->getDeterminacion()->getLimiteCuantificacble();
                }else{
                    $matriz[$i]['determinaciones'][$resultado->getDeterminacion()->getNombre()]['resultado'] = $resultado->getResultado();

                    //ACA HAY Q REVISARA SI ES SVRBAM o SMAVRBAM
                    if(($min && $resultado->getResultado() < $min) || ($max && $resultado->getResultado() > $max)){
                        $matriz[$i]['esApta'] = "NO APTA";
                    }
                }*/
                //}
            }

            $i++;
        }

        $html = $this->renderView('LaboratorioPedidoBundle:Default:protocolo2.html.twig',array('matriz' => $matriz));

        $footer = $this->renderView('LaboratorioPedidoBundle:Default:footerNumber.html.twig');

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('page-size', 'folio');
        $snappy->setOption('zoom', 1 );
        $snappy->setOption('dpi', 300 );
        $snappy->setOption('footer-html', $footer);
        $snappy->setOption('margin-top', 5);
        $snappy->setOption('margin-right', 5);
        $snappy->setOption('margin-bottom', 20);
        $snappy->setOption('margin-left', 5);
        $snappy->setOption('orientation','Landscape');

        //return $this->render('LaboratorioPedidoBundle:Default:protocolo2.html.twig',array('matriz' => $matriz));

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'filename="file.pdf"'
            )
        );

        return $this->render('LaboratorioPedidoBundle:Default:protocolo2.html.twig');
    }
}