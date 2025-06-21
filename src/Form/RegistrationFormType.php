<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $role = $options['role'] ?? null;

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est requis']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide'])
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est requis']),
                    new Assert\Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est requis']),
                    new Assert\Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le téléphone est requis']),
                    new Assert\Length(['min' => 10, 'max' => 20])
                ]
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse est requise']),
                    new Assert\Length(['max' => 255])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ]);

        // Champs spécifiques pour les citoyens
        if ($role === 'ROLE_CITOYEN') {
            $builder
                ->add('codePostal', TextType::class, [
                    'label' => 'Code Postal',
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le code postal est requis']),
                        new Assert\Length(['min' => 5, 'max' => 5, 'exactMessage' => 'Le code postal doit contenir exactement 5 chiffres'])
                    ]
                ])
                ->add('ville', TextType::class, [
                    'label' => 'Ville',
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'La ville est requise']),
                        new Assert\Length(['min' => 2, 'max' => 100])
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'role' => null, // Déclaration de l'option personnalisée
        ]);

        $resolver->setAllowedTypes('role', ['null', 'string']);
    }
}
