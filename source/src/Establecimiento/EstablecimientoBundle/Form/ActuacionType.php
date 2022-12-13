<?php

namespace Establecimiento\EstablecimientoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ActuacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('tipo',EntityType::class,array(
            'class' => 'EstablecimientoEstablecimientoBundle:TipoActuacion',
            'label' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->findAllTipos();
            },
            'attr' => array(
                'class' => 'form-control select2'
                )
            )
        )
        ->add('numero',NumberType::class,array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control'
                )
            )
        )
        ->add('reparticion',EntityType::class,array(
            'class' => 'EstablecimientoEstablecimientoBundle:Reparticion',
            'label' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->findAllReparticiones();
            },
            'attr' => array(
                'class' => 'form-control select2'
                )
            )
        )
        ->add('anio',NumberType::class,array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control'
                )
            )
        )
        ->add('clasificacionActuacion',EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:ActuacionTipo',
                    'label' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipos();
                    },
                    'attr' => array(
                        'class' => 'form-control select2'
                    )
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
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\Actuacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'establecimiento_establecimientobundle_actuacion';
    }


}
