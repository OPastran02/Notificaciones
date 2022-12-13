<?php

namespace Establecimiento\EstablecimientoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ResultadosUltimaInspeccionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('proximaInspeccion',DateType::class, array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false))
        ->add('inspeccionarCara',NumberType::class, array('required' => false))
        ->add('superficieCubierta',NumberType::class, array('required' => false))
        ->add('superficieTotal',NumberType::class, array('required' => false))
        ->add('inscripto326', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),
                'required' => false
            )
        )
        ->add('cantTanques',NumberType::class, array('required' => false))
        ->add('tanquesActivos',NumberType::class, array('required' => false))
        ->add('tanquesCegadosInertizados',NumberType::class, array('required' => false))
        ->add('tanquesErradicados',NumberType::class, array('required' => false))
        ->add('curl',NumberType::class, array('required' => false))
        ->add('generaEfluentesLiquidosInd', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI'
                ),'required' => false))
        ->add('caudalVuelcoMax',NumberType::class, array('required' => false))
        ->add('horaVuelvoInicial',TimeType::class, array('widget' => 'single_text', 'required' => false))
        ->add('horaVuelcoFinal',TimeType::class, array('widget' => 'single_text', 'required' => false))
        ->add('tramiteEfluentesEstado', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('reCirculacionAgua', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('cTMMC', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('realizaTratamientoEfluentesPrevioVuelco', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('generaBarros', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('protocoloVuelcaLimitesPermitidos', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('muestreoLaboratorio', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('resultadosLaboratorioLimitesPermisibles', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'
                ),'required' => false))
        ->add('videoInspeccionoUIT', ChoiceType::class, array(
                'choices' => array(
                    '' => null,
                    'NO' => 'NO',
                    'SI' => 'SI',
                    'N/A' => 'NA'                    
                ),'required' => false))
        ->add('tipoResiduos', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:TipoResiduos',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipoResiduos();
                    },
                    'multiple'=>true,
                    'required' => false
                )
            )
        ->add('tipoTratamiento', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:TipoTratamiento',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipoTratamiento();
                    },
                    'multiple'=>true,
                    'required' => false
                )
            )
        ->add('estado326', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:EstadoRes326',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllEstadosRes();
                    },
                    'required' => false
                )
            )
        ->add('tramiteEfluentes', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:TramiteEfluentes',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTramiteEfluentes();
                    },
                    'required' => false,
                )
            )
        ->add('destinoVuelcoEfluentes', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:DestinoVuelco',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllDestinoVuelco();
                    },
                    'required' => false,
                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\ResultadosUltimaInspeccion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'establecimiento_establecimientobundle_resultadosultimainspeccion';
    }


}
