<?php

namespace Laboratorio\PedidoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Doctrine\ORM\EntityRepository;

class MuestraEstadosType extends AbstractType
{   
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idArea= $options['idArea'];

        $builder->addEventListener(FormEvents::POST_SET_DATA, function ($event) use ($idArea) {            
            $form = $event->getForm();            

            if($idArea == $event->getData()->getArea()->getId()){
                $form->add('observacion',TextareaType::Class, array(
                    'required' => false
                    )
                );
                $form->add('conclusion',TextareaType::Class, array(
                    'required' => false
                    )
                );
                
            }
        });
                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Laboratorio\PedidoBundle\Entity\MuestraEstados',
            'idArea' => ''            
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'laboratorio_pedidobundle_muestraestados';
    }


}
