<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('photo', FileType::class, [
            'label' => 'Choisissez votre fichier JPG ou PNG',
            'required' => true,
            'mapped' => false,
            'help' => 'Formats acceptés : JPG et PNG. Taille maximale : 5 Mo.',

            'constraints' => [
                new NotBlank(
                    message: 'Veuillez sélectionner une image.'
                ),

                new File(
                    maxSize: '5M',
                    mimeTypes: [
                        'image/jpeg',
                        'image/png',
                    ],
                    maxSizeMessage: 'La taille du fichier ne doit pas dépasser 5 Mo.',
                    mimeTypesMessage: 'Merci de choisir une image JPG ou PNG valide.',
                ),
            ],

            'attr' => [
                'class' => 'form-control',
                'accept' => 'image/jpeg,image/png',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
