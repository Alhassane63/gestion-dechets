<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Form\SignalementType;
use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/signalement')]
class SignalementController extends AbstractController
{
    #[Route('/', name: 'signalement_index', methods: ['GET'])]
    public function index(SignalementRepository $signalementRepository): Response
    {
        return $this->render('signalement/index.html.twig', [
            'signalements' => $signalementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'signalement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SignalementRepository $signalementRepository): Response
    {
        $signalement = new Signalement();
        $form = $this->createForm(SignalementType::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signalementRepository->save($signalement, true);
            return $this->redirectToRoute('app_signalement_index');
        }

        return $this->render('signalement/new.html.twig', [
            'signalement' => $signalement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'signalement_show', methods: ['GET'])]
    public function show(Signalement $signalement): Response
    {
        return $this->render('signalement/show.html.twig', [
            'signalement' => $signalement,
        ]);
    }

    #[Route('/{id}/edit', name: 'signalement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Signalement $signalement, SignalementRepository $signalementRepository): Response
    {
        $form = $this->createForm(SignalementType::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signalementRepository->save($signalement, true);
            return $this->redirectToRoute('app_signalement_index');
        }

        return $this->render('signalement/edit.html.twig', [
            'signalement' => $signalement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'signalement_delete', methods: ['POST'])]
    public function delete(Request $request, Signalement $signalement, SignalementRepository $signalementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $signalement->getId(), $request->request->get('_token'))) {
            $signalementRepository->remove($signalement, true);
        }

        return $this->redirectToRoute('app_signalement_index');
    }
}
