<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CargaMasivaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {           
        $builder
        ->add('modelo',EntityType::class,array(
                    'class' => 'NotificacionesNotificacionesBundle:Cuerpo',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllCuerposForm();
                    }
                )
        )
        ->add('tipo',EntityType::class,array(
                    'class' => 'NotificacionesNotificacionesBundle:TipoCedula',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipos();
                    }
                )
        )        
        ->add('plazo1',NumberType::class)
        ->add('plazo2',NumberType::class)
        ->add('fojas',NumberType::class)        
        ->add('citacion',CheckboxType::Class,array('required' => false))
        ->add('vencer',CheckboxType::Class,array('required' => false))
        ->add('nocturnidad',CheckboxType::Class,array('required' => false))
        ->add('archivo',FileType::Class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {        
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_carga_masiva';
    }


}