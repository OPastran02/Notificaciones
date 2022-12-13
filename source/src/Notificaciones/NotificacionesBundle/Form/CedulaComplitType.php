<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Notificaciones\NotificacionesBundle\Form\NotificacionCedulaComplitType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\File;

class CedulaComplitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $vencimientos= $options['vencimientos'];
        $adjunto= $options['adjunto'];

        $builder        
        ->add('numero',NumberType::class)
        ->add('tipo',EntityType::class,array(
                'class' => 'NotificacionesNotificacionesBundle:TipoCedula',
            )
        )
        ->add('notificacion', NotificacionCedulaComplitType::class)        
        ->add('vencimiento1',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','mapped' => false, 'data' =>$vencimientos['primer']))
        ->add('vencimiento2',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','mapped' => false, 'data' =>$vencimientos['segundo']))
        ->add('cuerpo',TextareaType::class,array('disabled' => true))
        ;
        if($adjunto==1){
            $builder->add('adjuntos', FileType::class,array(
                    'required' => false,
                    'mapped' => false,
                    'attr' => array(
                        'class' => 'form-control',
                    ),
                    'constraints' => new File(
                        array(
                            'maxSize' => '10000k',
                            'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). El máximo permitido es {{ limit }} {{ suffix }}.')
                    )
                )
            );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'adjunto' => 0,
            'data_class' => 'Notificaciones\NotificacionesBundle\Entity\Cedula',
            'vencimientos' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_cedula_complit';
    }


}
