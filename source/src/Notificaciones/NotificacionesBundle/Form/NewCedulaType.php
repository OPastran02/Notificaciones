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

class NewCedulaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $razonesSociales = $options['razonesSociales'];
        $establecimiento = $options['establecimiento'];
        $contador = 0;
        if(isset($razonesSociales[0])){
          if($razonesSociales[0]->getTipo() == "F"){
            $razonSocialEstablecimiento = $razonesSociales[0]->getNombre1()." ". $razonesSociales[0]->getNombre2();
          }else{
            $razonSocialEstablecimiento = $razonesSociales[0]->getNombre1();
          }
        }else{
            $razonSocialEstablecimiento = '';
        }
        

        foreach ($razonesSociales as $razonSocial) {                
            foreach ($razonSocial->getDirecciones() as $direccion) {                    
                $builder->add('direccion'.$contador,TextType::Class,array('data' => $direccion->getCalle()->getCalle().' '.$direccion->getAltura().' '.$direccion->getPiso().' '.$direccion->getDpto().' '.$direccion->getLocal(),'mapped' => false ,'required' => true))
                  
                  ->add('lon'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $direccion->getLon() ))
                  ->add('lat'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $direccion->getLat() ))
                  ->add('comuna'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $direccion->getComuna() ))
                  ->add('tipoDomicilio'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => 'r' ));


                  if($razonSocial->getTipo() == "F" ){
                    $builder->add('razonsocial'.$contador,TextType::Class,array('data' => $razonSocial->getNombre1().' '.$razonSocial->getNombre2(),'mapped' => false,'required' => false ));
                  }else{
                    $builder->add('razonsocial'.$contador,TextType::Class,array('data' => $razonSocial->getNombre1(),'mapped' => false,'required' => false ));
                  }

                  
                  $builder->add('enviar'.$contador,CheckboxType::Class,array('mapped' => false,'required' => false))
                  ->add('legal'.$contador,HiddenType::Class,array('mapped' => false ,'required' => false , 'data' => 'true' ))                  
                  ;
                $contador++;
            }
        }

        foreach ($establecimiento->getDirecciones() as $direccion) {
            $builder->add('direccion'.$contador,TextType::Class,array('data' => $direccion->getCalle()->getCalle().' '.$direccion->getAltura().' '.$direccion->getPiso().' '.$direccion->getDpto().' '.$direccion->getLocal(),'mapped' => false ,'required' => true))
                  ->add('lon'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $direccion->getLon() ))
                  ->add('lat'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $direccion->getLat() ))
                  ->add('comuna'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => $direccion->getComuna() ))
                  ->add('tipoDomicilio'.$contador,HiddenType::Class,array('mapped' => false ,'required' => true , 'data' => 'e' ))
                  ->add('razonsocial'.$contador,TextType::Class,array('data' => $razonSocialEstablecimiento,'mapped' => false ,'required' => false))
                  ->add('enviar'.$contador,CheckboxType::Class,array('mapped' => false,'required' => false))
                  ->add('legal'.$contador,HiddenType::Class,array('mapped' => false ,'required' => false , 'data' => 'false' ))
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
                    return $er->searchAllActuacion($establecimiento->getId());
                }
            )
        )
        ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'razonesSociales' => array(),
            'establecimiento' => array(),
        ));
    } 
    


}