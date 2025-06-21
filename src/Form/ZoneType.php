<?php

namespace App\Form;

use App\Entity\Zone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la zone',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis']),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères'])
                ]
            ])
            ->add('Quartier', TextType::class, [
                'label' => 'Quartier',
                'constraints' => [
                    new NotBlank(['message' => 'Le quartier est requis']),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'Le quartier doit contenir au moins {{ limit }} caractères'])
                ]
            ])
            ->add('Classement', IntegerType::class, [
                'label' => 'Classement',
                'constraints' => [
                    new NotBlank(['message' => 'Le classement est requis'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zone::class,
        ]);
    }
}
