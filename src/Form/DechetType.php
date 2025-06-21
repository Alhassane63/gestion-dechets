<?php

namespace App\Form;

use App\Entity\Dechet;
use App\Entity\Zone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de déchet',
                'choices' => [
                    'Plastique' => 'PLASTIQUE',
                    'Verre' => 'VERRE',
                    'Papier' => 'PAPIER',
                    'Métal' => 'METAL',
                    'Organique' => 'ORGANIQUE',
                    'Autre' => 'AUTRE'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type est obligatoire']),
                ]
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité (en kg)',
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire']),
                    new Positive(['message' => 'La quantité doit être positive'])
                ]
            ])
            ->add('zone', EntityType::class, [
                'label' => 'Zone de collecte',
                'constraints' => [
                    new NotBlank(['message' => 'La zone est obligatoire'])
                ],
                'class' => Zone::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une zone'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dechet::class,
        ]);
    }
}
