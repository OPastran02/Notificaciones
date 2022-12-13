<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DistanciaNotificacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $contador = 0;
        $rows = $options['rows'];       

        foreach ($rows as $data) {
             $builder
                ->add('Id'.$contador,TextType::Class,array('mapped' => false, 'data' => $rows[$contador]->getId() ) )                
                ->add('Direccion'.$contador,TextType::Class,array('mapped' => false, 'data' => $rows[$contador]->getDireccionNotificada() ) )
                ->add('Lon'.$contador,TextType::Class,array('mapped' => false, 'data' => $rows[$contador]->getLon() ) )
                ->add('Lat'.$contador,TextType::Class,array('mapped' => false, 'data' => $rows[$contador]->getLat() ) );

            $disposicion =  $rows[$contador]->getDisposicion();
            if($disposicion){
                $requiereInspector = $disposicion->getRequiereInspector();
                if($requiereInspector == true){
                     $builder
                        ->add('Notificador'.$contador,EntityType::class,array(  
                                'mapped' => false,
                                'required'    => false,
                                'placeholder' => 'Seleccionar Notificador',
                                'data'   => null,             
                                'empty_data' => null,      
                                'class' => 'UsuarioUsuarioBundle:Usuarios',
                                'query_builder' => function (EntityRepository $er) {
                                    return $er->findAllNombreApellidoInspectorNotificador(1);
                                }
                            )
                        )
                    ;
                }else{
                     $builder
                        ->add('Notificador'.$contador,EntityType::class,array(  
                                'mapped' => false,
                                'required'    => false,
                                'placeholder' => 'Seleccionar Notificador',
                                'data'   => null,             
                                'empty_data' => null,      
                                'class' => 'UsuarioUsuarioBundle:Usuarios',
                                'query_builder' => function (EntityRepository $er) {
                                    return $er->findAllNombreApellidoInspectorNotificador(2);
                                }
                            )
                        )
                    ;
                }

            }else{
                 $builder
                    ->add('Notificador'.$contador,EntityType::class,array(  
                            'mapped' => false,
                            'required'    => false,
                            'placeholder' => 'Seleccionar Notificador',
                            'data'   => null,             
                            'empty_data' => null,      
                            'class' => 'UsuarioUsuarioBundle:Usuarios',
                            'query_builder' => function (EntityRepository $er) {
                                return $er->findAllNombreApellidoInspectorNotificador(2);
                            }
                        )
                    )
                ;
            }

                   

            if( !empty( $rows[$contador]->getCedula() ) ){
                $builder->add('Numero'.$contador,TextType::Class,array('mapped' => false, 'data' => $rows[$contador]->getCedula()->getNumero() ) );
            }elseif( !empty( $rows[$contador]->getDisposicion() ) ){
                $builder->add('Numero'.$contador,TextType::Class,array('mapped' => false, 'data' => $rows[$contador]->getDisposicion()->__toString() ) );
            }else{
                $builder->add('Numero'.$contador,TextType::Class,array('mapped' => false, 'data' => null ) );
            }



            $contador++;       
        }        
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'rows' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'notificaciones_notificacionesbundle_distancia';
    }


}
