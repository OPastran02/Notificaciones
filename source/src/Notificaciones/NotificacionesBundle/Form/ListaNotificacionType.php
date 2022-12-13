<?php

namespace Notificaciones\NotificacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ListaNotificacionType extends AbstractType
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
             	->add('Id'.$contador,TextType::Class,array('data' => $rows[$contador]->getId() ) )
                ->add('Numero'.$contador,TextType::Class,array('data' => $rows[$contador]->getCedula()->getNumero() ) )
                ->add('Tipo'.$contador,TextType::Class,array('data' => $rows[$contador]->getCedula()->getTipo() ) )
                ->add('Direccion'.$contador,TextType::Class,array('data' => $rows[$contador]->getDireccionNotificada() ) )
                ->add('RazonSocual'.$contador,TextType::Class,array('data' => $rows[$contador]->getCedula()->getNombreDestinatario() ) )
                ->add('FechaDevolucionFirma'.$contador,DateType::Class,array('widget' => 'single_text','format' => 'dd-MM-yyyy','data' => $rows[$contador]->getFechaVueltaFirma() ) )
            ;
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
        return 'notificaciones_notificacionesbundle_cedula';
    }


}
