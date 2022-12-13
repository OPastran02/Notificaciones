<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ModalAsignacionActaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fecha',DateType::class,array(
                'label' => true,
                'required'=>true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                )
            )
        ->add('inspector',EntityType::class,array(
                    'class' => 'UsuarioUsuarioBundle:Usuarios',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllNombreApellidoInspector();
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
            'data_class' => 'Faltas\FaltasBundle\Entity\AsignacionActa'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'faltas_faltasbundle_asignacionfaja';
    }


}
