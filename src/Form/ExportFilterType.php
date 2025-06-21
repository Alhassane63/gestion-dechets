<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ExportFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('periode', ChoiceType::class, [
                'label' => 'Période prédéfinie',
                'choices' => [
                    'Aujourd\'hui' => 'today',
                    'Hier' => 'yesterday',
                    'Cette semaine' => 'this_week',
                    'Semaine dernière' => 'last_week',
                    'Ce mois' => 'this_month',
                    'Mois dernier' => 'last_month',
                    'Personnalisée' => 'custom'
                ],
                'expanded' => true,
                'data' => 'this_month',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une période'])
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false,
                'constraints' => [
                    new Date(['message' => 'Date invalide']),
                    new GreaterThanOrEqual([
                        'value' => '1900-01-01',
                        'message' => 'La date ne peut pas être avant 1900'
                    ])
                ]
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false,
                'constraints' => [
                    new Date(['message' => 'Date invalide']),
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne peut pas être dans le futur'
                    ])
                ]
            ])
            ->add('exporter', SubmitType::class, [
                'label' => 'Exporter',
                'attr' => ['class' => 'btn-primary']
            ]);
    }
}
