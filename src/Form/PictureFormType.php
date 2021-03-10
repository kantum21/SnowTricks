<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class PictureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('picture', FileType::class, [
                'label' => 'Picture (JPG) *',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                            'maxSize' => '2048k',
                            'mimeTypes' => [
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Please Upload a valid JPG',
                        ]),
                    new NotBlank(),
                ],
                'invalid_message' => 'Please load a JPG picture (max size 2018k)'
            ])
            ->add('alt', TextType::class, [
                'label' => 'Alt *',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
