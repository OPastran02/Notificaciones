<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Establecimiento\EstablecimientoBundle\Form\DireccionType;
use Establecimiento\EstablecimientoBundle\Form\RazonSocialEstablecimientoType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class InspeccionEstablecimientoCierreType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder               
        ->add('direcciones', CollectionType::class, array(
            'entry_type' => DireccionType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => false,
            )
        )                
        ->add('razonesSociales', CollectionType::class, array(
            'disabled' => true,
            'entry_type' => RazonSocialEstablecimientoType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => false,
            )
        )
        ->add('estado',EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:Estado',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllEstados();
                    }
                )
        )        
        ->add('rubro', EntityType::class,array(
                'class' => 'EstablecimientoEstablecimientoBundle:Rubro',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllRubro();
                },
                'multiple'=>true,
                'required' => false
            )
        )
        ->add('rubroPrincipal',EntityType::class,array(                    
                'empty_data' => null,
                'placeholder' => 'Seleccione Rubro Principal',
                'class' => 'EstablecimientoEstablecimientoBundle:RubroPrincipal',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllRubroPrincipal();
                },
                'choice_attr' => function($key, $val, $index) {                        
                    
                    if($key->getHabilitado() == false){
                        $disabled = true;
                    }else{
                        $disabled = false;
                    }
                    // set disabled to true based on the value, key or index of the choice...

                    return $disabled ? ['disabled' => 'disabled'] : [];
                },
                'required' => true,                    
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
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\Establecimiento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inspecciones_inspeccionesbundle_establecimiento_cierre';
    }


}
