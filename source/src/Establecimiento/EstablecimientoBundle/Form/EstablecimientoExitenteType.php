<?php

namespace Establecimiento\EstablecimientoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Establecimiento\EstablecimientoBundle\Form\ResultadosUltimaInspeccionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class EstablecimientoExitenteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $usuario= $options['usuario'];
        $esSubgerente = false;
        $rzf = array();

        $rz= $options['rz'];
        
        foreach ($rz as $r) {
            array_push($rzf,$r->getRazonSocial()->getId());            
        }

        $builder       
        ->add('resultadosUltimaInspeccion', ResultadosUltimaInspeccionType::class)
        ->add('direcciones', CollectionType::class, array(
            'entry_type' => DireccionType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            'required' => true,
            )
        )        
        ->add('actuaciones', CollectionType::class, array(
            'entry_type' => ActuacionType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            )
        )
        ->add('razonesSociales', CollectionType::class, array(
            'entry_type' => RazonSocialEstablecimientoType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            'entry_options' => array('rz' => $rzf ),            
            )
        )
        ->add('estado',EntityType::class,array(
                    'empty_data' => null,
                    'placeholder' => 'Seleccione Estado',
                    'class' => 'EstablecimientoEstablecimientoBundle:Estado',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllEstados();
                    },
                    'required' => true,
                )
        )        
        ->add('rubroExtendido',TextType::Class, array('required' => false))
        ->add('email',EmailType::Class, array('required' => false))
        ->add('telefono',NumberType::Class, array('required' => false))
        ->add('bandera',TextType::Class, array('required' => false))
        ->add('exEESS',CheckboxType::Class, array('required' => false))        
        ->add('observaciones',TextareaType::Class, array(
            'required' => false,
            'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese texto aquí...',
                    'style' => 'height: 130px;'
                )
            )
        );

        
        if($usuario->getArea()->getId() == 7){
            $builder->add('rubroPrincipal',EntityType::class,array(
                        'empty_data' => null,
                        'placeholder' => 'Seleccione Rubro Principal',
                        'class' => 'EstablecimientoEstablecimientoBundle:RubroPrincipal',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->findAllRubroPrincipal();
                        },         
                        'choice_attr' => function($key, $val, $index) {                        
                            
                            if($index ==1 || $key->getHabilitado() == false){
                                $disabled = true;
                            }else{
                                $disabled = false;
                            }
                            // set disabled to true based on the value, key or index of the choice...

                            return $disabled ? ['disabled' => 'disabled'] : [];
                        },           
                        'required' => true,                        
                    )
            )->add('rubro', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:Rubro',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllRubro();
                    },
                    'multiple'=>true,
                    'required' => false,                    
                )
            );
        }else{
            $builder->add('rubroPrincipal',EntityType::class,array(                    
                    'empty_data' => null,
                    'placeholder' => 'Seleccione Rubro Principal',
                    'class' => 'EstablecimientoEstablecimientoBundle:RubroPrincipal',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllRubroPrincipal();
                    },         
                    'choice_attr' => function($key, $val, $index) {                        
                        
                        if($index ==1 || $key->getHabilitado() == false){
                            $disabled = true;
                        }else{
                            $disabled = false;
                        }
                        // set disabled to true based on the value, key or index of the choice...

                        return $disabled ? ['disabled' => 'disabled'] : [];
                    },           
                    'required' => true,
                    'disabled' => true,                       
                )
            )->add('rubro', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:Rubro',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllRubro();
                    },
                    'multiple'=>true,
                    'required' => false,
                    'disabled' => true,
                )
            );
        }

        $tipos = $usuario->getTipoUsuario();

        foreach ($tipos as $tipo) {
            if($tipo->getId() == 7 ){
                $esSubgerente = true;
            }
        }

        if($esSubgerente){
            $builder->add('favorito',CheckboxType::Class, array('required' => false));
        }else{
            $builder->add('favorito',CheckboxType::Class, array('required' => false,'disabled' => true ));
        }

        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'usuario' => array(),
            'rz' => array(),
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\Establecimiento',
            'validation_groups' => array('establecimiento', 'Default')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'establecimiento_establecimientobundle_establecimiento';
    }


}
