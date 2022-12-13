<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class NotificacionCedulaComplitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder        
        ->add('fechaEntrega',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false))
        ->add('fechaNotificacion',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false))
        ->add('fechaDevolucion',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false))        
        
        ->add('notificador',EntityType::class,array(
                'required'    => false,                
                'disabled' => true,
                'class' => 'UsuarioUsuarioBundle:Usuarios',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllNombreApellidoInspectorNotificador();
                }
            )
        )

        ->add('estado',EntityType::class,array(
                'class' => 'NotificacionesNotificacionesBundle:Estado',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findEstadosForCedula();
                }
            )
        )


        ->add('presentacionAgregar',CheckboxType::class,array('required' => false))
        ->add('prorroga',CheckboxType::class,array('required' => false))
        ->add('art61',CheckboxType::class,array('required' => false))        
        ->add('citacion',CheckboxType::class,array('required' => false))        
        ->add('nocturnidad',CheckboxType::class,array('required' => false))
        ->add('observaciones',TextareaType::class,array('required' => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Notificaciones\NotificacionesBundle\Entity\Notificacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_notificacion_complit_cedula';
    }


}
