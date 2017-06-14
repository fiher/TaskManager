<?php

namespace AppBundle\Form;

use AppBundle\Entity\Zadanie;
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
class ZadanieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $zadanie =$options['data'];
        /** @var  $zadanie Zadanie */
        $builder->add('fromUser',TextType::class,array(
            "required"=>true,
            "label"=>"От"
        ))->add('department',TextType::class,array(
            "required"=>true,
            "label"=>"Отдел"
        ))->
        add('typeTask',TextType::class,array(
            'label'=>"Вид заявка",
            'data'=>$zadanie->getTypeTask()
            ))->
        add('description',TextareaType::class,array(
            'label'=>"Описание",
            'data'=>$zadanie->getDescription()
            ))->
        add('term',DateType::class, array(
            'widget' => 'choice',
            'label'=>"Краен срок",
            'data'=>$zadanie->getTerm()
        ))->
        add('designer',ChoiceType::class,array('label'=>"Дизайнер",
            "required"=>false,
                'choices'=>array(
                    "Александра Вали" => "a.vali",
                    "Йоана Борисова" => "yoana",
                    "Ваня Иванова" => "v.ivanova",
                ),
            'data'=>$zadanie->getDesigner()
            ))->
        add('executioner',ChoiceType::class,array('label'=>"Под изпълнител",
            "required"=>false,
                'choices'=> array(
                    "Скай Строй" => "sky.stroy",
                    "Митко"=> "map.design",
                    "Димитринка"=>"dimitrinka"
                ),
                'data'=>$zadanie->getExecutioner()
            ))->
        add("file",TextType::class,array('label'=>"Файл",
            "required"=>false,
            "data"=>$zadanie->getFile()))->
        add("ergent",CheckboxType::class,array('label'=>"Спешно",
            "required"=>false,
            "data"=>$zadanie->isErgent()
                ))->
        add("approved",HiddenType::class,array(
            "required"=>false,
            "data"=>$zadanie->isApproved()
        ))->
        add("rejected",HiddenType::class,array(
            'required'=>false,
            "data"=>$zadanie->isRejected()
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Zadanie'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_zadanie';
    }


}
