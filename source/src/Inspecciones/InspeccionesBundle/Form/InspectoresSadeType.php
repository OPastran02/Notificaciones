<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class InspectoresSadeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('ifGra',TextType::Class, array(
            'required' => true,
            'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'IF-201_-________-______',
                    'data-mask' => 'IF-2018-000000000-LLLLLL'
                )
            )
        )->add('adjunto', FileType::class,array(
            'required' => false,
            'mapped' => false,
            'multiple' => false,
            'attr' => array(
                'class' => 'form-control',                                           
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
        return 'inspecciones_inspeccionesbundle_ordeninspeccion';
    }


}