<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accountType', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Je suis un citoyen' => 'ROLE_CITOYEN',
                    'Je suis un agent de collecte' => 'ROLE_AGENT',
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'btn-group btn-group-lg',
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'btn btn-outline-primary btn-lg'];
                },
                'help' => 'Choisissez le type de compte que vous souhaitez créer'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Continuer vers l\'inscription',
                'attr' => [
                    'class' => 'btn btn-primary w-100 mt-4'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
