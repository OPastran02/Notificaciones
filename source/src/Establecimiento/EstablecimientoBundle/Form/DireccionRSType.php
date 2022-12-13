<?php

namespace Establecimiento\EstablecimientoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DireccionRSType extends AbstractType
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
                    'attr' => array('class' => 'form-control select2' )
                )
        )
        ->add('altura',NumberType::class,array('label' => false, 'attr' => array('class' => 'form-control')))
        ->add('piso',TextType::class,array('label' => false,'required' => false, 'attr' => array('class' => 'form-control','required' => false)))
        ->add('dpto',TextType::class,array('label' => false,'required' => false, 'attr' => array('class' => 'form-control','required' => false)))
        ->add('local',TextType::class,array('label' => false,'required' => false, 'attr' => array('class' => 'form-control','required' => false)))        
        ->add('comuna',TextType::class,array('label' => false, 'required' => false, 'attr' => array('class' => 'form-control')))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\DireccionRS'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'establecimiento_establecimientobundle_direccionrs';
    }


}
