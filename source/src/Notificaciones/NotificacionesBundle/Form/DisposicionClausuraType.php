<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DisposicionClausuraType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fechaClausura',DateType::Class,array('widget' => 'single_text','format' => 'dd-MM-yyyy'))
        ->add('levantada',CheckboxType::Class,array('required' => false))
        ->add('fechaLevantamiento',DateType::Class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false))
        ->add('numeroNotaDgai',NumberType::Class,array('required' => false))
        ->add('anioNotaDgai',NumberType::Class,array('required' => false))
        ->add('numeroGiroDocumental',NumberType::Class,array('required' => false))
        ->add('anioGiroDocumental',NumberType::Class,array('required' => false))
        ->add('numeroActuacionRemicion',NumberType::Class,array('required' => false))
        ->add('anioActuacionRemicion',NumberType::Class,array('required' => false))
        ->add('alcance',EntityType::class,array('class' => 'FaltasFaltasBundle:TipoClausura',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllTipoClausura();
                })
            )
        ->add('controlador',EntityType::class,array('required' => false,
            'class' => 'NotificacionesNotificacionesBundle:Controlador',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllControladores();
                })
            )
        ->add('tipoActuacionRemicion',EntityType::class,array('required' => false,
            'class' => 'EstablecimientoEstablecimientoBundle:TipoActuacion',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllTipos();
                })
            )
        ->add('leyes', EntityType::class,array(
                    'class' => 'NotificacionesNotificacionesBundle:LeyesClausura',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllLeyes();
                    },
                    'multiple'=>true,
                    'required' => true
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
            'data_class' => 'Notificaciones\NotificacionesBundle\Entity\DisposicionClausura'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_disposicionclausura';
    }


}
