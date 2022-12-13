<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DisposicionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('numero',NumberType::class)
        ->add('anio',NumberType::class)
        ->add('reparticion',EntityType::class,array(
                'class' => 'EstablecimientoEstablecimientoBundle:Reparticion',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllReparticionesForDispoClausura();
                }
            )
        )              
        ->add('tipo',EntityType::class,array(
                'class' => 'NotificacionesNotificacionesBundle:TipoDispo',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllTipos(1);
                }
            )
        )
        ->add('requiereInspector',CheckboxType::Class,array('required' => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Notificaciones\NotificacionesBundle\Entity\Disposicion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_disposicion';
    }


}
