<?php

namespace Laboratorio\PedidoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Doctrine\ORM\EntityRepository;

class CargaResultadosType extends AbstractType
{   
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idArea= $options['idArea'];
        $idPrograma= $options['idPrograma'];        

        $builder->addEventListener(FormEvents::POST_SET_DATA, function ($event) use ($idPrograma,$idArea) {
            $idDeterminacion = $event->getData()->getDeterminacion()->getId();
            $form = $event->getForm();
            $bloqueado = $event->getData()->getBloqueado();

            if($idArea == $event->getData()->getDeterminacion()->getArea()->getId()){
                $form
                ->add('legislacion',EntityType::class,array(
                        'disabled' =>$bloqueado,
                        'class' => 'LaboratorioPedidoBundle:Legislacion',
                        'query_builder' => function (EntityRepository $er) use ($idPrograma,$idDeterminacion) {
                            return $er->getLegislacionToDeterminacion($idPrograma,$idDeterminacion,1);
                        }                    
                    )
                )
                ->add('legislacionSinContacto',EntityType::class,array(
                        'disabled' =>$bloqueado,
                        'class' => 'LaboratorioPedidoBundle:Legislacion',
                        'query_builder' => function (EntityRepository $er) use ($idPrograma,$idDeterminacion) {
                            return $er->getLegislacionToDeterminacion($idPrograma,$idDeterminacion,2);
                        }
                    )
                )
                ->add('legislacionPasivo',EntityType::class,array(
                        'disabled' =>$bloqueado,
                        'class' => 'LaboratorioPedidoBundle:Legislacion',
                        'query_builder' => function (EntityRepository $er) use ($idPrograma,$idDeterminacion) {
                            return $er->getLegislacionToDeterminacion($idPrograma,$idDeterminacion,3);
                        }                    
                    )
                );
                if($event->getData()->getDeterminacion()->getTipoDato() == 'String')
                {
                    $form->add('resultado',TextType::class,array(
                            'disabled' =>$bloqueado,
                        )
                    );
                }
                if($event->getData()->getDeterminacion()->getTipoDato() == 'Integer')
                {
                    /*if ($event->getData()->getResultado()%1 == 0)
                        $form->add('resultado',NumberType::class,array('disabled' =>$bloqueado));
                    else
                    {
                        $form->add('resultado',NumberType::class,array(
                                'disabled' =>$bloqueado,
                                'scale' => 10
                            )
                        );
                    }*/

                    $form->add('resultado',NumberType::class,array(
                            'attr' => array(
                                'value' => $event->getData()->getResultado()
                            ),
                            'disabled' =>$bloqueado/*,
                            'scale' => 10*/
                        )
                    );
                }
                
                /*->add('determinacion',EntityType::class,array(
                        'disabled' =>$bloqueado,
                        'class' => 'LaboratorioPedidoBundle:Determinacion'                        
                    )
                ) */       
                /*$form->add('usuario',EntityType::class,array(
                        'disabled' =>$bloqueado,
                        'class' => 'UsuarioUsuarioBundle:Usuarios',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->findAllUsuariosLaboratorio();
                        }
                    )
                )*/
                $form->add('fechaFinAnalisis',DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'disabled' =>$bloqueado
                ));
                
                /*
                if($idArea == 22){
                    $form->add('fechaInicioAnalisis',DateType::class, array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false,'disabled' =>$bloqueado));
                }
                if($idArea == 20 || $idArea == 21){
                    $form->add('fechaInicioAnalisis',DateType::class, array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false,'disabled' =>$bloqueado));
                    $form->add('fechaFinAnalisis',DateType::class, array('widget' => 'single_text','format' => 'dd-MM-yyyy','required' => false,'disabled' =>$bloqueado));
                }
                */
            }
        });        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Laboratorio\PedidoBundle\Entity\CargaResultados',
            'idArea' => '',
            'idPrograma' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'laboratorio_pedidobundle_cargaresultados';
    }


}
