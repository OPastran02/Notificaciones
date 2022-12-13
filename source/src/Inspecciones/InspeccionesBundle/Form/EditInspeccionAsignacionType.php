<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Inspecciones\InspeccionesBundle\Form\InspeccionType;
use Inspecciones\InspeccionesBundle\Form\DireccionProvisoriaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;



class EditInspeccionAsignacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('checklist',NumberType::class, array(
                'disabled' => true,
            )
        )  
        ->add('idSap',NumberType::class, array(
                'required' => true,
            )
        )
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
        ->add('inspecciones', CollectionType::class, array(
            'entry_type' => InspeccionType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            )
        )
        ->add('direcciones', CollectionType::class, array(
            'entry_type' => DireccionProvisoriaType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
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
        return 'inspecciones_inspeccionesbundle_edit_inspeccion_asignacion';
    }


}
