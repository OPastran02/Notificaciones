<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new Establecimiento\EstablecimientoBundle\EstablecimientoEstablecimientoBundle(),
            new Inspecciones\InspeccionesBundle\InspeccionesInspeccionesBundle(),
            new Usuario\UsuarioBundle\UsuarioUsuarioBundle(),
            new CoreBundle\CoreBundle(),
            new Faltas\FaltasBundle\FaltasFaltasBundle(),
            new Notificaciones\NotificacionesBundle\NotificacionesNotificacionesBundle(),
            new Home\HomeBundle\HomeHomeBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Encuesta\EncuestaBundle\EncuestaEncuestaBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Reportes\ReportesBundle\ReportesReportesBundle(),
            new Publico\PublicoBundle\PublicoPublicoBundle(),
            new Laboratorio\PedidoBundle\LaboratorioPedidoBundle(),
            new KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle(),
            new \Aws\Symfony\AwsBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                //$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        /*if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }*/

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
