<?php

namespace App\Form;

use App\Entity\Tournee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class TourneeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom ne peut pas être vide'])
                ]
            ])
            ->add('zone', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La zone ne peut pas être vide'])
                ]
            ])
            ->add('date', DateTimeType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'La date ne peut pas être vide'])
                ]
            ])
            ->add('heureDebut', TimeType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'L\'heure de début ne peut pas être vide'])
                ]
            ])
            ->add('heureFin', TimeType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'L\'heure de fin ne peut pas être vide'])
                ]
            ])
            ->add('active', null, [
                'required' => false
            ])
        ;
    }
}
