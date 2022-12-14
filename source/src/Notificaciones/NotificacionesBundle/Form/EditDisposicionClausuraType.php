<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\File;

class EditDisposicionClausuraType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $adjunto= $options['adjunto'];

        $builder
        ->add('numero',NumberType::class)
        ->add('anio',NumberType::class)
        ->add('reparticion',EntityType::class,array(
                'class' => 'EstablecimientoEstablecimientoBundle:Reparticion',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllReparticiones();
                }
            )
        )              
        ->add('tipo',EntityType::class,array(
                'class' => 'NotificacionesNotificacionesBundle:TipoDispo',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllTipos(2);
                }
            )
        )        
        ->add('clausura', DisposicionClausuraType::class)
        ->add('notificacion', NotificacionCedulaComplitType::class)        
        
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
                            'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). El m??ximo permitido es {{ limit }} {{ suffix }}.')
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
            'data_class' => 'Notificaciones\NotificacionesBundle\Entity\Disposicion',            
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_EditDisposicionClausura';
    }
    


}