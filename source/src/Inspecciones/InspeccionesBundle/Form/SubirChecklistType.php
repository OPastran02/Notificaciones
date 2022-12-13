<?php

namespace Inspecciones\InspeccionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\File;

class SubirChecklistType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero',NumberType::class)
            ->add('adjuntos', FileType::class,array(
                    'required' => true,
                    'mapped' => false,
                    'attr' => array(
                        'class' => 'form-control',
                        'onchange'=>'getFileName(this);'
                    ),
                    'constraints' => new File(
                        array(
                            'mimeTypes'=>['application/pdf'],
                            'mimeTypesMessage' => 'Solo se permiten pdf',
                            'maxSize' => '10000k',
                            'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). El máximo permitido es {{ limit }} {{ suffix }}.')
                    )
                )
            );
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
        return 'inspecciones_inspeccionesbundle_inspeccion';
    }


}
