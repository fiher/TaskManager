<?php

namespace AppBundle\Form;

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
class ZadanieRejectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->
        add('typeTask',HiddenType::class,array('label'=>"Вид заявка",
            "required"=> false
        ))->
        add('description',HiddenType::class,array('label'=>"Описание",
            "required" =>false))->
        add('term',HiddenType::class, array(
            'widget' => 'choice',
            'label'=>"Краен срок",
            "required"=>false
        ))->
        add('designer',HiddenType::class,array('label'=>"Дизайнер",
            "required"=>false,
            'choices'=>array(
                "Александра Вали" => "a.vali",
                "Йоана Борисова" => "yoana",
                "Ваня Иванова" => "v.ivanova",
            )))->
        add('executioner',HiddenType::class,array('label'=>"Под изпълнител",
            "required"=>false,
            'choices'=> array(
                "Скай Строй" => "sky.stroy",
                "Митко"=> "map.design",
                "Димитринка"=>"dimitrinka"
            )))->
        add("file",HiddenType::class,array('label'=>"Файл",
            "required"=>false))->
        add("ergent",HiddenType::class,array('label'=>"Спешно",
            "required"=>false))->
        add("rejected",HiddenType::class,array(
            "required"=>false,
            "data"=>true
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
