<?php

namespace App\Form;

use App\Entity\Signalement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as DoctrineEntityType;
use App\Entity\Zone;

use Symfony\Component\Validator\Constraints as Assert;

class SignalementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description du problème',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est requise']),
                    new Assert\Length(['min' => 10, 'minMessage' => 'La description doit avoir au moins {{ limit }} caractères'])
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu du signalement',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le lieu est requis']),
                    new Assert\Length(['max' => 255, 'maxMessage' => 'Le lieu ne doit pas dépasser {{ limit }} caractères'])
                ]
            ])
            ->add('dateSignalement', DateTimeType::class, [
                'label' => 'Date du signalement',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\DateTime(['message' => 'Format de date invalide'])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de problème',
                'choices' => [
                    'Retard de collecte' => 'retard',
                    'Bac cassé' => 'casse',
                    'Bac manquant' => 'manquant',
                    'Déchets non collectés' => 'non_collecte',
                    'Autre' => 'autre'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de problème est requis'])
                ]
            ])
            ->add('photo', TextType::class, [
                'label' => 'Photo (URL)',
                'required' => false,
                'constraints' => [
                    new Assert\Url(['message' => 'Veuillez entrer une URL valide'])
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État du signalement',
                'choices' => [
                    'Nouveau' => 'nouveau',
                    'En cours' => 'en_cours',
                    'Traité' => 'traite'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'état est requis'])
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut du traitement',
                'choices' => [
                    'Nouveau' => 'nouveau',
                    'En cours' => 'en_cours',
                    'Traité' => 'traite'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est requis'])
                ]
            ])
            ->add('dateTraitement', DateTimeType::class, [
                'label' => 'Date de traitement',
                'required' => false,
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\DateTime(['message' => 'Format de date invalide'])
                ]
            ])
            ->add('commentaireTraitement', TextareaType::class, [
                'label' => 'Commentaire de traitement',
                'required' => false,
                'attr' => ['rows' => 4]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->add('zone', DoctrineEntityType::class, [
                'class' => Zone::class,
                'choice_label' => 'nom',
                'label' => 'Zone concernée'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signalement::class,
        ]);
    }
}
