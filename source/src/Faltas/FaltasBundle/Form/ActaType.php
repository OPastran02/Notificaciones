<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ActaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('serie') 
            ->add('numero',NumberType::class) 
            ->add('estado',EntityType::class,array(                                    
                    'class' => 'FaltasFaltasBundle:EstadoActa',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllMotivos();
                    },
                    'label' => false,                    
                ))
            ->add('actautilizada',ActaActaUtilizadaType::class)
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Faltas\FaltasBundle\Entity\Acta'
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
