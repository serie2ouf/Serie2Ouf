<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère le mode : 'create' ou 'edit' (passé depuis le contrôleur)
        $isEdit = $options['is_edit'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(message: 'Merci de saisir un email'),
                    new Email(message: 'Email invalide'),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label'    => 'Rôles',
                'choices'  => [
                    'Utilisateur'    => 'ROLE_USER',
                    'Editeur'        => 'ROLE_EDITOR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,  // affichage en cases à cocher
                'multiple' => true,  // plusieurs rôles possibles
            ])

            ->add('password', PasswordType::class, [
                'label'    => 'Mot de passe',
                'mapped'   => false,
                'required' => !$isEdit,
                'attr'     => [
                    'class'       => 'form-control',
                    'placeholder' => $isEdit ? 'Laisser vide pour ne pas modifier' : '',
                ],
                'constraints' => $isEdit ? [] : [
                    new NotBlank(message: 'Merci de saisir un mot de passe'),
                    new Length(
                        min: 10,
                        minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères',
                    ),
                ],
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(message: 'Merci de saisir un nom'),
                ],
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr'  => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(message: 'Merci de saisir un prénom'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'is_edit'    => false, // false par défaut = mode création
        ]);
    }
}
