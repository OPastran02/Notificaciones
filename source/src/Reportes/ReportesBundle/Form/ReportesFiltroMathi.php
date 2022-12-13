<?php

namespace Reportes\ReportesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Reportes\ReportesBundle\Form\Listener\AddOptionFieldSubscriber;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ReportesFiltroType extends AbstractType
{
  /**
  * {@inheritdoc}
  */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $gruposPregunta= $options['gruposPregunta'];

    $choices;

    $choices['Establecimiento']['Calle'] = 's|calle';
    $choices['Establecimiento']['Altura'] = 'i|altura';
    $choices['Establecimiento']['Rubro'] = 'm|rubro';
    $choices['Establecimiento']['Rubro Principal'] = 'c|rubroprincipal';
    $choices['Establecimiento']['Estado'] = 'c|estado';
    $choices['Establecimiento']['Ex EESS'] = 'c|exeess';
    $choices['Establecimiento']['Bandera'] = 's|bandera';
    $choices['Establecimiento']['Comuna'] = 'i|comuna';

    $choices['Inspeccion']['N°CheckList'] = 'i|checklist';
    $choices['Inspeccion']['Id Sap'] = 'i|sap';
    $choices['Inspeccion']['Fecha Inspeccion'] = 'f|fechainspeccion';
    $choices['Inspeccion']['Inspector'] = 'c|inspector';
    $choices['Inspeccion']['Area'] = 'c|area';
    $choices['Inspeccion']['Actas'] = 'i|actas';
    $choices['Inspeccion']['Fajas'] = 'i|fajas';

    foreach ($gruposPregunta as $grupoPregunta) {
      $requisitos = $grupoPregunta->getRequisitos();
      foreach ($requisitos as $requisito) {
        $choices['Inspeccion']['Preguntas'][$grupoPregunta->getNombreGrupo()][$requisito->getPregunta()->getPregunta()]="p|pregunta-".$requisito->getPregunta()->getId();
      }
    }

    $builder->add('filtro', ChoiceType::class, array(
      'choices'  => array(
        $choices
      ),
      'choices_as_values' => true,
      'choice_value' => function ($choice) {
        return $choice;
        },
      ))
      ->add('option',ChoiceType::class, array(
        'choices' => array(
        )
      )
      )
      ;

      $builder->addEventSubscriber(new AddOptionFieldSubscriber());

    }

    /**
    * {@inheritdoc}
    */
    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array(
        'gruposPregunta' => array()
      ));
    }

    /**
    * {@inheritdoc}
    */
    public function getBlockPrefix()
    {
      return 'establecimiento_establecimientobundle_actuacion';
    }


  }
