<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class AsignacionFajaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('faja', FajaAsignacionType::class)
        ->add('fechaAsignacion',DateType::class,array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
                )
            )
        ->add('idUsuarioInspector',EntityType::class,array(
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
            'data_class' => 'Faltas\FaltasBundle\Entity\AsignacionFaja'
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
