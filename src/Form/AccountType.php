<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accountType', ChoiceType::class, [
                'label' => 'Type de compte',
                'choices' => [
                    'Citoyen' => 'citoyen',
                    'Agent' => 'agent',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un type de compte']),
                ],
                'help' => 'Choisissez le type de compte que vous souhaitez créer.',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Continuer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}
