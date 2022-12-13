<?php

namespace Reportes\ReportesBundle\Form\Listener;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;



class AddOptionFieldSubscriber implements EventSubscriberInterface
{   
    /*protected $em;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }*/

    public static function getSubscribedEvents()
    {        
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
    
    public function preSetData(FormEvent $event)
    {        
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }
        
        $this->addField($form, $data);
    }

    public function preSubmit(FormEvent $event)
    {        
        $data = $event->getData();
        $form = $event->getForm();
        
        if (null === $data) {
            return;
        }        
        $this->addField($form, $data);
    }

    protected function addField(Form $form, $data)
    {        

        if (null != $data) {
            $filtro = explode("|",$data['filtro']);
            switch ($filtro[0]) {
                case 's':
                    $form->add('option',ChoiceType::class, array('choices' => array('=' => '=','!=' => '!=','Contiene'=>'%','Empieza con' => '*%')));
                    $form->add('completar',TextType::Class,array('label' => false ,'attr' => array('class' => 'form-control')));
                    break;
                case 'i':
                    $form->add('option',ChoiceType::class, array('choices' => array('=' => '=','!=' => '!=','>'=>'>','<' => '<','Entre' => 'between')));
                    $form->add('completar',NumberType::class,array('label' => false,'attr' => array('class' => 'form-control')));
                    break;

                case 'c':
                    $form->add('option',ChoiceType::class, array('choices' => array('=' => '=','!=' => '!=')));
                    switch ($filtro[1]) {

                        case 'estado':
                            $form->add('completar',EntityType::class,array(
                                        'label' => false,
                                        'attr' => array(
                                            'class' => 'form-control select2'
                                        ),
                                        'class' => 'EstablecimientoEstablecimientoBundle:Estado',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->findAllEstados();
                                        },
                                        'required' => true,
                                    )
                            );
                            break;

                        case 'exeess':
                            $form->add('completar',ChoiceType::class, array('label' => false,'attr' => array('class' => 'form-control select2'),'choices' => array('SI' => '1','NO' => '0')));
                            break;

                        case 'inspector':
                            $form->add('completar',EntityType::class,array(
                                        'label' => false,
                                        'attr' => array(
                                            'class' => 'form-control select2'
                                        ),                                        
                                        'class' => 'UsuarioUsuarioBundle:Usuarios',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->findAllNombreApellidoInspector();
                                        }
                                    )
                            );
                            break;

                        case 'area':
                            $form->add('completar',EntityType::class,array(
                                        'label' => false,
                                        'attr' => array(
                                            'class' => 'form-control select2'
                                        ),
                                        'class' => 'UsuarioUsuarioBundle:Area',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->findAllAreas();
                                        },
                                    )
                                );
                            break;

                        case 'rubroprincipal':
                                $form->add('completar',EntityType::class,array(
                                            'label' => false,
                                            'attr' => array(
                                                'class' => 'form-control select2'
                                            ),
                                            'class' => 'EstablecimientoEstablecimientoBundle:RubroPrincipal',
                                            'query_builder' => function (EntityRepository $er) {
                                                return $er->findAllRubroPrincipal();
                                            },
                                            'required' => true,
                                        )
                                );
                            break;
                        
                        default:
                            throw new Exception("Error Processing Request", 1);                            
                            break;
                    }

                    break;                

                case 'm':
                    $form->add('option',ChoiceType::class, array('choices' => array('=' => '=','!=' => '!=')));
                    switch ($filtro[1]) {
                        case 'rubro':
                            $form->add('rubro', EntityType::class,array(
                                    'label' => false,
                                    'attr' => array(
                                        'class' => 'form-control select2'
                                    ),
                                    'class' => 'EstablecimientoEstablecimientoBundle:Rubro',
                                    'query_builder' => function (EntityRepository $er) {
                                        return $er->findAllRubro();
                                    },
                                    'multiple'=>true,
                                    'required' => false
                                )
                            );
                            break;
                        
                        default:
                            throw new Exception("Error Processing Request", 1);
                            break;
                    }
                    break;

                case 'p':
                    $pregunta = explode("-",$filtro[1]);
                    $idPregunta = $pregunta[1];
                    var_dump($idPregunta);

                    $this->getDoctrine()->getRepository('EncuestaEncuestaBundle:Pregunta')->findOneById($idPregunta)();

                    $asd = function (EntityRepository $er) {
                        $er->getRepository('EncuestaEncuestaBundle:Pregunta');
                        return $er->findOneById($idPregunta);

                    };

                    var_dump($asd);
                    exit();


                    break;
                
                default:
                    throw new Exception("Error Processing Request", 1);
                    break;
            }
        }else{
            $form->add('option',ChoiceType::class, array('choices' => array('=' => '=','!=' => '!=')));
        }
    }
    
}