<?php

namespace App\Form;

use App\Entity\PointCollecte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Tournee;
use Symfony\Component\Form\Event\FormEvents;
use Symfony\Component\Form\Event\FormEvent;
use App\Form\EventListener\TourneeSubscriber;

class PointCollecteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse ne peut pas être vide'])
                ]
            ])
            ->add('typeDechets', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le type de déchets ne peut pas être vide'])
                ]
            ])
            ->add('reference', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('latitude', null, [
                'required' => false
            ])
            ->add('longitude', null, [
                'required' => false
            ])
            ->add('tournee', ChoiceType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La tournée ne peut pas être vide'])
                ]
            ])
            ->addEventSubscriber(new TourneeSubscriber())
        ;
    }
}
