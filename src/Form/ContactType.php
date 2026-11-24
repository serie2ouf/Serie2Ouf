<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', ChoiceType::class, [
                'label' => 'Objet *',
                'placeholder' => 'Sélectionnez un objet',
                'choices' => [
                    'Demande de renseignements' => 'Demande de renseignements',
                    'Service après-vente' => 'Service après-vente',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner un objet'),
                ],
            ])
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civilité *',
                'choices' => [
                    'Madame' => 'Mme',
                    'Monsieur' => 'M.',
                ],
                'attr' => ['autocomplete' => 'honorific-prefix'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner une civilité'),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'attr' => [
                    'autocomplete' => 'family-name',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'pattern' => "[A-Za-zÀ-ÖØ-öø-ÿ\\s\\-']+",
                    'title' => 'Lettres, espaces et tirets uniquement',
                    'placeholder' => 'Dupont',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire'),
                    new Length(min: 2, max: 50, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères'),
                    new Regex(pattern: "/^[A-Za-zÀ-ÖØ-öø-ÿ\\s\\-']+$/", message: 'Lettres, espaces et tirets uniquement'),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'attr' => [
                    'autocomplete' => 'given-name',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'pattern' => "[A-Za-zÀ-ÖØ-öø-ÿ\\s\\-']+",
                    'title' => 'Lettres, espaces et tirets uniquement',
                    'placeholder' => 'Jean',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire'),
                    new Length(min: 2, max: 50, minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères'),
                    new Regex(pattern: "/^[A-Za-zÀ-ÖØ-öø-ÿ\\s\\-']+$/", message: 'Lettres, espaces et tirets uniquement'),
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => false,
                // libellé affiché => valeur enregistrée
                'choices' => [
                    'Masculin' => 'M',
                    'Féminin' => 'F',
                ],
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner un sexe'),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Particulier' => 'Particulier',
                    'Professionnel' => 'Professionnel',
                    'Association' => 'Association',
                ],
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner un type'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'attr' => [
                    'autocomplete' => 'email',
                    'maxlength' => '100',
                    'placeholder' => 'jean.dupont@email.com',
                    'inputmode' => 'email',
                ],
                'constraints' => [
                    new NotBlank(message: "L'email est obligatoire"),
                    new Email(message: "L'adresse email {{ value }} n'est pas valide"),
                    new Length(max: 100),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'pattern' => '^(?:0[1-9](?:[\s.]?\d{2}){4})$',
                    'minlength' => '10',
                    'maxlength' => '17',
                    'placeholder' => '06 07 08 09 10',
                    'autocomplete' => 'tel',
                    'inputmode' => 'tel',
                    'title' => 'Format : 06 07 08 09 10 ou 06.07.08.09.10 ou 0607080910',
                ],
                'constraints' => [
                    new Regex(
                        pattern: '/^(?:0[1-9](?:[\s.]?\d{2}){4})$/',
                        message: 'Format invalide. Ex : 06 07 08 09 10'
                    ),
                ],
            ])
            ->add('demande', TextareaType::class, [
                'label' => 'Demande *',
                'attr' => [
                    'rows' => 6,
                    'minlength' => '10',
                    'maxlength' => '1000',
                    'placeholder' => 'Décrivez votre demande en détail (minimum 10 caractères)...',
                ],
                'constraints' => [
                    new NotBlank(message: 'La demande est obligatoire'),
                    new Length(min: 10, max: 1000, minMessage: 'La demande doit contenir au moins {{ limit }} caractères'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
