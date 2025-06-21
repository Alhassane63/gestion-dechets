<?php

namespace App\Form;

use App\Entity\Collecte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\PointCollecte;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CollecteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeDechets', TextType::class, [
                'label' => 'Type de déchets',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de déchets est requis']),
                    new Assert\Length(['min' => 3, 'minMessage' => 'Le type doit contenir au moins 3 caractères'])
                ]
            ])
            ->add('pointCollecte', EntityType::class, [
                'class' => PointCollecte::class,
                'choice_label' => 'nom',
                'label' => 'Point de collecte',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le point de collecte est requis'])
                ]
            ])
            ->add('agent', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_AGENT%');
                },
                'label' => 'Agent de collecte'
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'En cours' => 'en_cours',
                    'Effectuée' => 'effectuee',
                    'Annulée' => 'annulee'
                ],
                'label' => 'Statut'
            ])
            ->add('collecteEffectuee', ChoiceType::class, [
                'choices' => [
                    'Non' => false,
                    'Oui' => true
                ],
                'label' => 'Collecte effectuée',
                'expanded' => true
            ])
            ->add('commentaire', TextType::class, [
                'label' => 'Commentaire',
                'required' => false
            ])
            ->add('zone', EntityType::class, [
                'class' => Zone::class,
                'choice_label' => 'nom',
                'label' => 'Zone',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La zone est requise'])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer la collecte',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collecte::class,
        ]);
    }
}
