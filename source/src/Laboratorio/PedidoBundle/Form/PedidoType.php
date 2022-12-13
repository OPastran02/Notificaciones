<?php

namespace Laboratorio\PedidoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PedidoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaProgramacion', DateType::class, array(
                    'label' => true,
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy'
                )
            )
            ->add('programa') //estaba abajo de todo
            ->add('tipoPedido');
        //->add('prioridad')
        /*->add('areas', EntityType::class, array(
                'class' => 'UsuarioUsuarioBundle:Area',
                'query_builder' => function (EntityRepository $er) {
                    return $er->findAllAreasLaboratorio();
                },
                'multiple'=>true,
                'required' => true
            )
        )*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Laboratorio\PedidoBundle\Entity\Pedido'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'laboratorio_pedidobundle_pedido';
    }


}
