<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class EditCedulaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enviadas = $options['eviadas'];
        $establecimiento =  $options['idEstablecimiento'];       
        $actuacion =  $options['actuacion'];
        $contador = 0;
        foreach ($enviadas as $enviada){
          $builder->add('direccion'.$contador,TextType::Class,array('data' => $enviada['direcion'],'required' => false,'mapped' => false ))
                  ->add('lon'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $enviada['lon'] ))
                  ->add('lat'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $enviada['lat'] ))
                  ->add('razonsocial'.$contador,TextType::Class,array('data' => $enviada['razonSocial'],'mapped' => false,'required' => false ))
                  ->add('comuna'.$contador,HiddenType::Class,array('data' => $enviada['comuna'],'mapped' => false,'required' => false ))
                  ->add('tipoDomicilio'.$contador,HiddenType::Class,array('data' => $enviada['tipoDomicilio'],'mapped' => false,'required' => false ))
                  ->add('enviar'.$contador,CheckboxType::Class,array('data' => $enviada['enviar'],'mapped' => false, 'required' => false))
                  ->add('legal'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $enviada['legal'] ))
                  ;
            $contador++;
        }
        $builder->add('cedula', CedulaType::class)
        ->add('actuacion',EntityType::class,array(
                'empty_data' => null,
                'placeholder' => 'Seleccionar Actuación',
                'required'    => false,
                'mapped' => false,
                'class' => 'EstablecimientoEstablecimientoBundle:Actuacion',
                'query_builder' => function (EntityRepository $er) use ($establecimiento) {
                    return $er->searchAllActuacion($establecimiento);
                },
                'data' => $actuacion
            )
        )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(            
            'eviadas' => array(),
            'idEstablecimiento' => 70,
            'actuacion' => array(),
        ));
    } 
    


}