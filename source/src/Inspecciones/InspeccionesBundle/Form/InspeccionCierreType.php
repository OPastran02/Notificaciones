<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class InspeccionCierreType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fechaProgramado',DateType::class,
            array('label' => false,'widget' => 'single_text','format' => 'dd-MM-yyyy','attr' => array(
                'class' => 'form-control input-sm fecha-picker',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('fechaInspeccion',DateType::class,
            array('label' => false,'widget' => 'single_text','format' => 'dd-MM-yyyy','attr' => array(
                'class' => 'form-control input-sm fecha-picker',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('fechaRecepcion',DateType::class,
            array('label' => false,'widget' => 'single_text','format' => 'dd-MM-yyyy','attr' => array(
                'class' => 'form-control input-sm fecha-picker',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('inspectores',EntityType::class,array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'form-control select2'
                    ),
                    'multiple' => true,
                    'class' => 'UsuarioUsuarioBundle:Usuarios',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllNombreApellidoInspector();
                    }
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
            'data_class' => 'Inspecciones\InspeccionesBundle\Entity\Inspeccion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_inspeccion_cierre';
    }


}
