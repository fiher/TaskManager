<?php

namespace AppBundle\Form;

use AppBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project =$options['data'];
        $data = new \DateTime();
        if($project->getTerm()){
            $data = $project->getTerm();
        }
        /** @var  $project Project */
        $builder->
        add('typeTask',TextType::class,array(
            'label'=>"Вид заявка",
            'data'=>$project->getTypeTask()
            ))->
        add('description',TextareaType::class,array(
            'label'=>"Описание",
            'data'=>$project->getDescription()
            ))->
        add('term',DateType::class, array(
            'widget' => 'choice',
            'label'=>"Краен срок",
            'data'=>$data
        ))->add('noTerm',CheckboxType::class,array(
            "mapped"=>false,
            'label'=>"Без Срок",
            'required'=> false
        ))->
        add('designer',ChoiceType::class,array('label'=>"Дизайнер",
            "required"=>false,
                'choices'=>array(
                    "Няма дизайнер"=>"Няма дизайнер",
                    "Александра Вали" => "Александра Вали",
                    "Йоана Борисова" => "Йоана Борисова",
                    "Рената Дудлей" => "Рената Дудлей",
                ),
            'data'=>$project->getDesigner()
            ))->
        add('executioner',ChoiceType::class,array('label'=>"Подизпълнител",
            "required"=>false,
                'choices'=> array(
                    "Скай Строй" => "Скай Строй",
                    "Митко"=> "Митко",
                    "Димитринка"=>"Димитринка"
                ),
                'data'=>$project->getExecutioner()
            ))->
        add("file",TextType::class,array('label'=>"Линк: ",
            "required"=>false,
            "data"=>$project->getFile()))->
        add("urgent",CheckboxType::class,array('label'=>"Спешно",
            "required"=>false,
            "data"=>$project->isUrgent()
                ))->
        add("approved",HiddenType::class,array(
            "required"=>false,
            "data"=>$project->isApproved()
        ))->
        add("rejected",HiddenType::class,array(
            'required'=>false,
            "data"=>$project->isRejected()
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_project';
    }


}
