<?php

namespace Laboratorio\PedidoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class MuestraType extends AbstractType
{    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

        $idArea= $options['idArea'];
        $idPrograma= $options['idPrograma'];
        $supervisado= $options['supervisado'];
        $autorizado= $options['autorizado'];
        $areaUsuarioActual= $options['areaUsuarioActual'];
        $bloqueado= false;        
        $anulado = $options['anulado'];

        if($supervisado || $autorizado || $idArea != $areaUsuarioActual || $anulado)
        {
            $bloqueado= true;
        }

        if($idArea == 19){
            $builder->add('auxiliares', EntityType::class,array(
                    'class' => 'UsuarioUsuarioBundle:Usuarios',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->findAllUsuariosCampo();
                    },
                    'multiple'=>true,
                    'disabled' =>$bloqueado,
                    'required' => false
                )
            //)->add('fechaTomaMuestra',DateType::class, array('widget' => 'single_text','format' => 'dd-MM-yyyy','disabled' =>$bloqueado,'required' => true)
            )->add('lugarExtraccion',TextType::class,array(                
                    'required' => true,                
                    'disabled' =>$bloqueado,
                )
            )
            ;
        }

        $builder        
        ->add('resultados', CollectionType::class, array(
            'entry_type' => CargaResultadosType::class,
            'entry_options' => array('idArea'=>$idArea,'idPrograma'=>$idPrograma),
            'by_reference' => false,
            'allow_delete' => false,
            'disabled' =>$bloqueado,
            'required' => false,
            )
        )
        ->add('estados',CollectionType::class,
            array('entry_type' => MuestraEstadosType::class,
                'entry_options' => array('idArea'=>$idArea),
                'by_reference' => false,
                'allow_delete' => false,
                'disabled' =>$bloqueado,
                'required' => false,
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
            'data_class' => 'Laboratorio\PedidoBundle\Entity\Muestra',
            'idArea' => '',
            'idPrograma' => '',
            'supervisado' => '',
            'autorizado' => '',
            'areaUsuarioActual' => '',
            'anulado'=>''
            
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'laboratorio_pedidobundle_muestra';
    }


}
