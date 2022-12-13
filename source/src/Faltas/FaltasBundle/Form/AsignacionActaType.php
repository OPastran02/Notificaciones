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
class AsignacionActaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('Serie',TextType::class)        
        ->add('fecha',DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy'))
        ->add('usuario',EntityType::class,array(
                'class'=>'UsuarioUsuarioBundle:Usuarios',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllNombreApellidoInspector();
                }
            )
        )
        ->add('NumeroUno',NumberType::class)
        ->add('NumeroDos',NumberType::class)       
        ;
    }
    
    /**
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
        return 'faltas_faltasbundle_asignacionacta';
    }


}
