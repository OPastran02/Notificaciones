<?php

namespace Faltas\FaltasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class RemitoActaTablaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $contador=0;
        $rows = $options["rows"];

      

        if(count($rows) > 0 ){
            foreach ($rows as $data) {  

              if(!$data->getId()){
                    $id=0;
                }else{
                    $id=$data->getId();
                }

                if(!$data->getNumero()){
                    $numero="";
                }else{
                    $numero=$data->getNumero();
                }

                if(!$data->getActaUtilizada()->getChecklist()){
                    $checklist=0;
                }else{
                    $checklist=$data->getActaUtilizada()->getChecklist();
                }

                if(!$data->getActaUtilizada()->getSap()){
                    $sap=0;
                }else{
                    $sap=$data->getActaUtilizada()->getSap();
                }

                if(!$data->getActaUtilizada()->getFechaInspeccion() ){
                    $fechainspeccion=new \DateTime();
                }else{
                    $fechainspeccion=$data->getActaUtilizada()->getFechaInspeccion();
                }

                if(!$data->getActaUtilizada()->getFechaRecepcion()){
                    $FechaRecepcion=new \DateTime();
                }else{
                    $FechaRecepcion=$data->getActaUtilizada()->getFechaRecepcion();
                }

                if(!$data->getActaUtilizada()->getAreas()){
                    $area="";
                }else{
                    $area=$data->getActaUtilizada()->getAreas()->getArea();
                }

                $builder
                    ->add('Id'.$contador,numberType::class,array('data' => $id ) )
                    ->add('estado'.$contador,EntityType::class,array(
                            'class' => 'FaltasFaltasBundle:EstadoActa',
                            'query_builder' => function (EntityRepository $er) {
                                return $er->findAllMotivos();
                            },
                            'data' => $data->getEstado(),
                        )
                    )
                    ->add('numeroActa'.$contador,TextType::class,array('data' => $numero ) )
                    ->add('Checklist'.$contador,NumberType::class,array('data' => $checklist ) )
                    ->add('IdSap'.$contador,NumberType::class,array('data' => $sap ) )
                    ->add('FechaInspeccion'.$contador,DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy', 'data' => $fechainspeccion ) )
                    ->add('FechaRecepcion'.$contador,DateType::class,array('widget' => 'single_text','format' => 'dd-MM-yyyy', 'data' => $FechaRecepcion ) )
                    ->add('Area'.$contador,TextType::class,array('data' => $area ) )
                    ;

                $contador++;
            }
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
        return 'remito_acta';
    }


}
