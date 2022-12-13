<?php

namespace Usuario\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class contraseniaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contrasenia', RepeatedType::class,array(
                'type' => PasswordType::class,
                'invalid_message' => 'los campos de "Nueva Contraseña" y "Repetir Contraseña" deben coincidir',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'mapped'=>false,
                'first_options'  => array('label' => 'Nueva Contraseña'),
                'second_options' => array('label' => 'Repetir Contraseña')
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
           
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'usuario_usuariobundle_changePassword';
    }


}
