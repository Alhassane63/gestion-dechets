<?php

namespace App\Form\EventListener;

use App\Entity\Tournee;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;

class TourneeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(PreSetDataEvent $event): void
    {
        $form = $event->getForm();
        $point = $event->getData();
        if (!$point) {
            return;
        }

        if ($point && $point->getZone()) {
            $choices = $point->getZone()->getTournees()->map(function (Tournee $tournee) {
                return [$tournee->getNom() => $tournee->getId()];
            })->toArray();

            $form->add('tournee', ChoiceType::class, [
                'choices' => $choices,
                'choice_label' => function (Tournee $tournee) {
                    return $tournee->getNom();
                },
                'constraints' => [
                    new NotBlank(['message' => 'La tournée ne peut pas être vide'])
                ]
            ]);
        }
    }
}
