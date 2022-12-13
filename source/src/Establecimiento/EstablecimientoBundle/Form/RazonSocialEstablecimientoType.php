<?php

namespace Establecimiento\EstablecimientoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RazonSocialEstablecimientoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rz= $options['rz'];
        $limit = null;
        if(!count($rz) > 0){
            $limit = 1;
        }

        $builder
        ->add('razonSocial', EntityType::class,array(
                    'class' => 'EstablecimientoEstablecimientoBundle:RazonSocial',
                    'query_builder' => function (EntityRepository $er) use ( $rz,$limit )  {
                        return $er->findAllRazonesSociales($rz,$limit);
                    },
                    'label' => false,
                    'attr' => array('class' => 'form-control select2' )
                )
        )    
        ->add('fechaInicio',DateType::class, array('widget' => 'single_text' , 'format' => 'dd-MM-yyyy', 'label' => false, 'attr' => array('class' => 'form-control fecha-picker')))
        ->add('fechaFin',DateType::class, array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'label' => false, 'attr' => array('class' => 'form-control fecha-picker')))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();                

                $form->add('razonSocial', EntityType::class,array(
                        'class' => 'EstablecimientoEstablecimientoBundle:RazonSocial',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->findAllRazonesSociales();
                        },
                        'label' => false,
                        'attr' => array('class' => 'form-control select2' )
                    )
                );
            }
        );
 
    }

    ///HAY QUE HACER UN POST SUBMIT Y PERMITIR Q ENTRE TODO... O NO SE
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'rz' => array(),
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\EstablecimientoRazonSocial'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'establecimiento_establecimientobundle_razonsocialestablecimiento';
    }


}
