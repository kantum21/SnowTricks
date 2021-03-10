<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mainPicture', EntityType::class, [
                'class' => Picture::class,
                'choice_label' => 'picture',
                'label' => 'Main picture',
                'expanded' => true,
                'multiple' => false,
                'required' => false,

            ])
            ->add('pictures', EntityType::class, [
                'class' => Picture::class,
                'choice_label' => 'picture',
                'label' => 'Pictures',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('videos', EntityType::class, [
                'class' => Video::class,
                'choice_label' => 'link',
                'label' => 'Videos',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'Name *',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Category *',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
