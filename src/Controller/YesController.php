<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Form\SignalementForm;
use App\Repository\SignalementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/yes')]
final class YesController extends AbstractController
{
    #[Route(name: 'app_yes_index', methods: ['GET'])]
    public function index(SignalementRepository $signalementRepository): Response
    {
        return $this->render('yes/index.html.twig', [
            'signalements' => $signalementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_yes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $signalement = new Signalement();
        $form = $this->createForm(SignalementForm::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($signalement);
            $entityManager->flush();

            return $this->redirectToRoute('app_yes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('yes/new.html.twig', [
            'signalement' => $signalement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_yes_show', methods: ['GET'])]
    public function show(Signalement $signalement): Response
    {
        return $this->render('yes/show.html.twig', [
            'signalement' => $signalement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_yes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Signalement $signalement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SignalementForm::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_yes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('yes/edit.html.twig', [
            'signalement' => $signalement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_yes_delete', methods: ['POST'])]
    public function delete(Request $request, Signalement $signalement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$signalement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($signalement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_yes_index', [], Response::HTTP_SEE_OTHER);
    }
}
