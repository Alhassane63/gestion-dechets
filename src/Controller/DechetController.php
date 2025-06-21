<?php

namespace App\Controller;

use App\Entity\Dechet;
use App\Form\DechetType;
use App\Repository\DechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dechet')]
class DechetController extends AbstractController
{
    #[Route('/', name: 'app_dechet_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $dechets = $entityManager->getRepository(Dechet::class)->findAll();
        return $this->render('dechet/index.html.twig', [
            'dechets' => $dechets,
        ]);
    }

    #[Route('/new', name: 'app_dechet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dechet = new Dechet();
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dechet->setCitoyen($this->getUser()); // Set the current user as citoyen
            $entityManager->persist($dechet);
            $entityManager->flush();
            $this->addFlash('success', 'Déchet créé avec succès !');

            return $this->redirectToRoute('app_dechet_index');
        }

        return $this->render('dechet/new.html.twig', [
            'dechet' => $dechet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dechet_show', methods: ['GET'])]
    #[Route('/admin/dechets', name: 'app_admin_dechets')]
    #[IsGranted('ROLE_ADMIN')]
    public function listTypes(DechetRepository $dechetRepository): Response
    {
        $types = $dechetRepository->createQueryBuilder('d')
            ->select('d.type')
            ->groupBy('d.type')
            ->orderBy('d.type')
            ->getQuery()
            ->getResult();

        return $this->render('dechet/types.html.twig', [
            'types' => $types
        ]);
    }

    public function show(Dechet $dechet): Response
    {
        return $this->render('dechet/show.html.twig', [
            'dechet' => $dechet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dechet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dechet $dechet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Déchet mis à jour avec succès !');

            return $this->redirectToRoute('app_dechet_index');
        }

        return $this->render('dechet/edit.html.twig', [
            'dechet' => $dechet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dechet_delete', methods: ['POST'])]
    public function delete(Request $request, Dechet $dechet, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$dechet->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Opération non autorisée');
            return $this->redirectToRoute('app_dechet_index');
        }

        try {
            $entityManager->remove($dechet);
            $entityManager->flush();
            $this->addFlash('success', 'Déchet supprimé avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression');
        }

        return $this->redirectToRoute('app_dechet_index');
    }
}
