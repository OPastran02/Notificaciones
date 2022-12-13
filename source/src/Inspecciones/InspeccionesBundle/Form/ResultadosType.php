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

class ResultadosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder        
        ->add('grupo',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('pregunta',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('respuestaLibre',TextType::class,array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'form-control input-sm',
                'style' => 'text-align: center;'
                )
            )
        )
        ->add('respuestas',EntityType::class,array(
                'label' => false,
                'required' => false,
                'multiple' => true,
                'class' => 'EncuestaEncuestaBundle:Respuestas',                
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
            'data_class' => 'Inspecciones\InspeccionesBundle\Entity\Resultados'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_inspeccion_resultados';
    }


}
