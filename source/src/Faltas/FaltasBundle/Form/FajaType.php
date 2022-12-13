<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class FajaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('checklist',NumberType::class,array(
                'label' => true,
                'required'=>false
                )
            )
            ->add('idSap',NumberType::class,array(
                'label' => true,
                'required'=>false
                )
            )
            ->add('numero',NumberType::class,array(
                'label' => true
                )
            )
            ->add('area',EntityType::class,array(
                    'empty_data' => null,
                    'placeholder' => '',
                    'class' => 'UsuarioUsuarioBundle:Area',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllAreas();
                    },
                    'required' => true
                )
            )
            ->add('estado',EntityType::class,array(
                    'class' => 'FaltasFaltasBundle:EstadoFaja',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllEstados();
                    },
                    'label' => false,
                )
            )
            ->add('fechaRecepcion',DateType::class,array(
                'label' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                )
            )
            ->add('fechaInspeccion',DateType::class,array(
                'label' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                )
            )
            ->add('tipoClausura',EntityType::class,array(
                    'mapped'=>false,
                    'class' => 'FaltasFaltasBundle:tipoClausura',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipoClausura();
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
            'data_class' => 'Faltas\FaltasBundle\Entity\Faja'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'faltas_faltasbundle_faja';
    }


}
