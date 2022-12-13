<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DireccionProvisoriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('calle',EntityType::class,array(
            'class' => 'EstablecimientoEstablecimientoBundle:Calles',
            'query_builder' => function (EntityRepository $er) {
                return $er->findAllCalles();
            },
            'label' => false,
            'attr' => array(
                'class' => 'form-control select2'
                )
            )
        )
        ->add('altura',NumberType::class,array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('piso',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('dpto',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('local',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
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
            'data_class' => 'Inspecciones\InspeccionesBundle\Entity\DireccionProvisoria'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_direccionprovisoria';
    }


}
