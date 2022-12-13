<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Inspecciones\InspeccionesBundle\Form\InspeccionType;
use Inspecciones\InspeccionesBundle\Form\DenuncianteType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;

class ProgramarInspeccionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('area',EntityType::class,array(
                    'class' => 'UsuarioUsuarioBundle:Area',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllAreas();
                    }
                )
        )
        ->add('circuito',EntityType::class,array(
                    'class' => 'InspeccionesInspeccionesBundle:Circuito',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllCircuitos();
                    }
                )
        )
        ->add('motivoInspeccion',EntityType::class,array(
                    'class' => 'InspeccionesInspeccionesBundle:MotivoInspeccion',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllMotivos();
                    }
                )
        )
        ->add('inspecciones', CollectionType::class, array(
            'entry_type' => InspeccionType::class,
            'allow_add'    => false,
            'by_reference' => false,
            'allow_delete' => false,
            )
        )
        ->add('observaciones',TextareaType::Class, array(
            'required' => false,
            'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese las observaciones del área aquí...',
                    'style' => 'height: 50px;'
                )
            )
        )
        ->add('observacionesMotivoInspeccion',TextareaType::Class, array(            
            'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el motivo de inspección detallado aquí...',
                    'style' => 'height: 80px;'
                )
            )
        )
        ->add('denunciantes', CollectionType::class, array(
            'entry_type' => DenuncianteType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            )
        )
        ->add('adjuntos', FileType::class,array(
            'required' => false,
            'mapped' => false,
            'multiple' => true,
            'attr' => array(
                'class' => 'form-control',                                           
                ),
            'constraints' => new All(
                new File(
                    array(
                        'maxSize' => '10000k',
                        'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). El máximo permitido es {{ limit }} {{ suffix }}.')
                    )
                )
            )

        )
        ->add('inspeccionPorTablet',ChoiceType::class,            
            array(
                'choices' => array(                
                'SI' => true,
                'NO' => false,
                ),
            )
        )
        ->add('suaci', TextType::class,
            array(
                'constraints' => new Length(array('min' => 0, 'max' => 11)),
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'EJ: 00985265/21',
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
            'data_class' => 'Inspecciones\InspeccionesBundle\Entity\OrdenInspeccion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_ordeninspeccion';
    }


}
