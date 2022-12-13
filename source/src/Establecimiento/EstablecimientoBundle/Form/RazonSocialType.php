<?php

namespace Establecimiento\EstablecimientoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

//Agregado por Italo ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
//↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑

class RazonSocialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('cuit', NumberType::Class, array(
            'constraints' => new Length(array('min' => 11, 'max' => 11)),
            )
        )
        ->add('direcciones', CollectionType::class, array(
            'entry_type' => DireccionRSType::class,
            'allow_add'    => true,
            'by_reference' => false,
            'allow_delete' => true,
            )
        )
        ->add('tipo', ChoiceType::Class, array(            
            'choices' => array(
                'Física' => 'F',
                'Jurídica' => 'J'
                )
            )
        )
        ->add('telefono', NumberType::Class, array('required' => false))
        ->add('mail', EmailType::Class, array('required' => false))
        ->add('nombre1', TextType::Class, array('required' => false))
        ->add('nombre2', TextType::Class, array('required' => false))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Establecimiento\EstablecimientoBundle\Entity\RazonSocial'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'establecimiento_establecimientobundle_razonsocial';
    }


}
