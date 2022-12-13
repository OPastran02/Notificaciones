<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


class RemitoActaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fechaInicial',DateType::class,array(
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                 )
             )
        ->add('fechaFinal',DateType::class,array(
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                 )
             )
        ->add('areas',EntityType::class,array(
                    'mapped' => false,
                    'required'    => false,
                    'placeholder' => 'Seleccionar Area',
                    'data'   => null,             
                    'empty_data' => null,
                    'class' => 'UsuarioUsuarioBundle:Area',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllAreas();
                    },
                )
            )
        ;
    }
    
    /*
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'faltas_faltasbundle_remitoacta';
    }


}
