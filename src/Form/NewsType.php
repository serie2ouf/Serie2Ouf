<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr'  => [
                    'class'     => 'form-control border border-primary bg-light',
                    'minlength' => '3',
                    'maxlength' => '200',
                ],
            ])
            ->add('datenews', DateType::class, [
                'label'  => 'Date',
                'widget' => 'single_text',   // rendu <input type="date">
                'data'   => new \DateTime(), // date du jour par défaut
                'attr'   => [
                    'class' => 'form-control border border-primary bg-light',
                    'max'   => (new \DateTime('+1 year'))->format('Y-m-d'),
                ],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'attr'  => [
                    'class'     => 'form-control border border-primary bg-light',
                    'rows'      => 4,
                    'minlength' => '10',
                    'maxlength' => '5000',
                ],
            ])
            ->add('image', TextType::class, [
                'label' => "Nom de l'image",
                'attr'  => [
                    'class'       => 'form-control border border-primary bg-light',
                    'placeholder' => 'ex: photo-news.jpg',
                    'pattern'     => '[a-zA-Z0-9_\-\.]+\.(jpg|jpeg|png|webp)',
                ],
                'help' => 'Lettres, chiffres, tirets et underscore uniquement. Extensions: jpg, png, webp',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
