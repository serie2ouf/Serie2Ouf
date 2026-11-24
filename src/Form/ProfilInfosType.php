<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilInfosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'exemple@domaine.fr',
                    'autocomplete' => 'email',
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir une adresse e-mail.',
                    ),
                    new Email(
                        message: 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                    ),
                    new Length(
                        max: 180,
                        maxMessage: 'L\'adresse e-mail ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Votre nom',
                    'autocomplete' => 'family-name',
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre nom.',
                    ),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: 'Le nom doit comporter au moins {{ limit }} caractères.',
                        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Votre prénom',
                    'autocomplete' => 'given-name',
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre prénom.',
                    ),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: 'Le prénom doit comporter au moins {{ limit }} caractères.',
                        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ),
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
