<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ActaActaUtilizadaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('checklist',NumberType::class,array(
                    'required' => false,
                )
            )
            ->add('sap',NumberType::class,array(
                    'required' => false,
                )
            )
            ->add('areas',EntityType::class,array(                    
                    'empty_data' => null,
                    'placeholder' => '',
                    'class' => 'UsuarioUsuarioBundle:Area',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllAreas();
                    },
                )
            )  
            ->add('comprobado',CheckboxType::class,array(
                    'required' => false,
                ))
            ->add('fechaRecepcion',DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                )
            )
            ->add('fechaInspeccion',DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                )
            )
            ->add('actaMotivo',EntityType::class,array(
                    'multiple'=>true,
                    'class' => 'FaltasFaltasBundle:ActaMotivo',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllMotivos();
                    },
                    'label' => false,
                )
            )
            ->add('dominioL',TextType::class,array(
                    'required' => false,
                )
            )
            ->add('dominioR',NumberType::class,array(
                    'required' => false,                    
                )
            )
            ->add('interno',NumberType::class,array(
                    'required' => false,
                )
            )
            ->add('marca',TextType::class,array(
                    'required' => false,
                )
            )
            ->add('modelo',TextType::class,array(
                    'required' => false,
                )
            )
            ->add('ruido',NumberType::class,array(
                    'required' => false,
                )
            )
            ->add('humo',NumberType::class,array(
                    'required' => false,
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
            'data_class' => 'Faltas\FaltasBundle\Entity\ActaUtilizada'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'faltas_faltasbundle_fajaasignacion';
    }


}
?>