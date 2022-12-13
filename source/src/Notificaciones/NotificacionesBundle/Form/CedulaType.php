<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CedulaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {           
        $builder
        ->add('vencer',CheckboxType::Class,array('required' => false))
        ->add('fojas',NumberType::class)        
        ->add('tipo',EntityType::class,array(
                    'class' => 'NotificacionesNotificacionesBundle:TipoCedula',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipos();
                    }
                )
            )

        ->add('notificacion', NotificacionType::class) 
        ->add('cuerpo',TextareaType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Notificaciones\NotificacionesBundle\Entity\Cedula',                        
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_cedula';
    }


}
