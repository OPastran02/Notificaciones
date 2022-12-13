<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Inspecciones\InspeccionesBundle\Form\InspeccionCierreType;
use Inspecciones\InspeccionesBundle\Form\DireccionProvisoriaType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;



class EditInspeccionCierreDesvinculadoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('checklist',NumberType::class)  
        ->add('idSap',TextType::class)  
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
        ->add('direcciones', CollectionType::class, array(
            'entry_type' => DireccionProvisoriaType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            )
        )  
        ->add('realizada',CheckboxType::class,array('required' => false))  
        ->add('inspecciones', CollectionType::class, array(
            'entry_type' => InspeccionCierreType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            )
        )
       ->add('observaciones',TextareaType::Class, array(
            'required' => false,
            'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese texto aquí...',
                    'style' => 'height: 130px;'
                )
            )
        )
       ->add('revisionObs',TextareaType::Class, array(
            'required' => false,
            'disabled' => true,
            'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese texto aquí...',
                    'style' => 'height: 130px;'
                )
            )
        )
        ->add('generaPeligrosos',CheckboxType::class,array('required' => false))
        ->add('generaPatogenicos',CheckboxType::class,array('required' => false))
        ->add('generaAvus',CheckboxType::class,array('required' => false))
        ->add('generaEfluentesLiquidos',CheckboxType::class,array('required' => false))
        ->add('generaEmisionesGaseosas',CheckboxType::class,array('required' => false))
        ->add('inscriptoRac',CheckboxType::class,array('required' => false))
        ->add('tieneTamquesCombustible',CheckboxType::class,array('required' => false))
        ->add('inscriptoRegLavanderiaTintoreria',CheckboxType::class,array('required' => false))        
        ->add('ruido',ChoiceType::class,
            array('choices' => array(
                '' => null,
                'SI' => 1,
                'NO' => 0,
                ),
            )
        )
        ->add('olores',ChoiceType::class,
            array('choices' => array(
                '' => null,
                'SI' => 1,
                'NO' => 0,
                ),
            )
        )
        ->add('ctrlCedula',ChoiceType::class,
            array('choices' => array(
                '' => null,
                'SI' => 1,
                'NO' => 0,
                ),
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
        return 'inspecciones_inspeccionesbundle_edit_inspeccion_cierre_desvinculado';
    }


}
