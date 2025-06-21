<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use App\Form\DataTransformer\RolesTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email ne peut pas être vide']),
                    new Assert\Email(['message' => 'Veuillez entrer une adresse email valide'])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Citoyen' => 'ROLE_CITOYEN',
                    'Agent' => 'ROLE_AGENT',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le rôle ne peut pas être vide'])
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom ne peut pas être vide'])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom ne peut pas être vide'])
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le téléphone ne peut pas être vide'])
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse ne peut pas être vide'])
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmation du mot de passe',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La confirmation du mot de passe ne peut pas être vide']),
                    new Assert\EqualTo([
                        'propertyPath' => 'plainPassword',
                        'message' => 'Les mots de passe ne correspondent pas'
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);

        // Ajout du transformateur pour le champ roles
        $builder->get('roles')
            ->addModelTransformer(new RolesTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
