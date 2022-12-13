<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotNull;

class InspeccionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fechaProgramado',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy',
            'constraints' => new NotNull(array('message'=>'Fecha obligatoria')))
        )
        ->add('inspectores',EntityType::class,array(
                    'required' => false,
                    'multiple' => true,
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
            'data_class' => 'Inspecciones\InspeccionesBundle\Entity\Inspeccion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_inspeccion';
    }


}
