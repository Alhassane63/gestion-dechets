<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Agent de collecte' => 'ROLE_AGENT',
                    'Citoyen' => 'ROLE_CITOYEN'
                ],
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner votre rôle'])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Continuer',
                'attr' => ['class' => 'btn btn-primary w-100']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false
        ]);
    }
}
