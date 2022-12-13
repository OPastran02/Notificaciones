<?php

namespace Usuario\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Length;

class UsuariosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario',NumberType::Class, array(
                    'constraints' => new Length(array('min' => 11, 'max' => 11)),
                )
            )
            ->add('habilitado', CheckboxType::class, array(
                'required' => false,
                 )
            )
            ->add('nombre',TextType::class)
            ->add('apellido',TextType::class)
            ->add('sistemaNotificaciones', ChoiceType::class,array(
                    'choices'=>array(
                        'Consulta'=>1,
                        'Normal'=>2,
                        'Admin'=>3,
                    )
                ))
            ->add('pedidos', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('establecimientos',ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('inbox', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('antecedentes', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('programacion', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('documentacion', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('actasYFajas', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('cargaMasivaCedulas', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Consulta'=>1,
                        'Editar'=>2,
                        'Total'=>3,
                    )
                ))
            ->add('nivelTablet', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Autorizado'=>1,
                    )
                ))
            ->add('nivelPatrulla', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Autorizado'=>1,
                    )
                ))
            ->add('rni', ChoiceType::class,array(
                    'choices'=>array(
                        'No Autorizado'=>0,
                        'Autorizado'=>1,
                    )
                ))
            ->add('area',EntityType::class,array(
                    'class' => 'UsuarioUsuarioBundle:Area',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllAreas();
                    },
                )
            )
            ->add('laboratorio',ChoiceType::class,array(
                'choices'=>array(
                    'No Autorizado'=>0,
                    'Consulta'=>1,
                    'Editar'=>2,
                    'Encargado'=>3,
                    'Supervisor'=>4,
                    'Manager'=>5,
                    'Director'=>6,
                    'Total'=>7,
                )
            ))
            ->add('tipoUsuario', EntityType::class,array(
                    'class' => 'UsuarioUsuarioBundle:TipoUsuario',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllTipoUsuarios();
                    },
                    'multiple'=>true,
                    'required' => false
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
            'data_class' => 'Usuario\UsuarioBundle\Entity\Usuarios'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'usuario_usuariobundle_usuarios';
    }


}
