<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DenuncianteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('apellido',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('direccion',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('telefono',TextType::class,array(
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
            'data_class' => 'Inspecciones\InspeccionesBundle\Entity\Denunciante'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_denunciante';
    }


}
