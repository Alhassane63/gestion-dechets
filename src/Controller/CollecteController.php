<?php

namespace App\Controller;

use App\Entity\Collecte;
use App\Form\CollecteType;
use App\Repository\CollecteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CollecteController extends AbstractController
{
    #[Route('/collecte', name: 'app_collecte_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $collectes = $entityManager->getRepository(Collecte::class)->findAll();
        
        // Si aucune collecte n'existe, afficher un message approprié
        if (empty($collectes)) {
            return $this->render('collecte/index.html.twig', [
                'collectes' => [],
                'noCollectes' => true
            ]);
        }
        
        return $this->render('collecte/index.html.twig', [
            'collectes' => $collectes,
            'noCollectes' => false
        ]);
    }

    #[Route('/collecte/list', name: 'app_collecte_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $collectes = $entityManager->getRepository(Collecte::class)->findAll();
        
        return $this->render('collecte/_list.html.twig', [
            'collectes' => $collectes,
        ]);
    }

    #[Route('/collecte/new', name: 'app_collecte_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collecte = new Collecte();
        $form = $this->createForm(CollecteType::class, $collecte);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collecte);
            $entityManager->flush();
            $this->addFlash('success', 'Collecte créée avec succès !');
            return $this->redirectToRoute('app_collecte_dashboard');
        }

        return $this->render('collecte/new.html.twig', [
            'collecte' => $collecte,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/collecte/dashboard', name: 'app_collecte_dashboard')]
    public function dashboard(CollecteRepository $collecteRepository, PointCollecteRepository $pointCollecteRepository, UserRepository $userRepository): Response
    {
        // Statistiques
        $collectesPlanifiees = $collecteRepository->count(['estEffectuee' => false]);
        $collectesEffectuees = $collecteRepository->count(['estEffectuee' => true]);
        $pointsCollecte = $pointCollecteRepository->count([]);
        $agents = $userRepository->count(['roles' => 'ROLE_AGENT']);

        // Collectes récentes (dernières 10)
        $collectesRecentes = $collecteRepository->findBy([], ['date' => 'DESC'], 10);

        return $this->render('collecte/dashboard.html.twig', [
            'collectesPlanifiees' => $collectesPlanifiees,
            'collectesEffectuees' => $collectesEffectuees,
            'pointsCollecte' => $pointsCollecte,
            'agents' => $agents,
            'collectesRecentes' => $collectesRecentes
        ]);
    }

    #[Route('/collecte/{id}/marquer-comme-effectuee', name: 'app_collecte_marquer_comme_effectuee')]
    #[IsGranted('ROLE_AGENT')]
    public function marquerCommeEffectuee(Collecte $collecte, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'agent est bien assigné à cette collecte
        $user = $this->getUser();
        if ($collecte->getAgent() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette collecte');
        }

        // Vérifier que la collecte n'est pas déjà effectuée
        if ($collecte->getStatut() === Collecte::STATUTS['effectuee']) {
            throw $this->createNotFoundException('Cette collecte est déjà marquée comme effectuée');
        }

        // Vérifier que le statut est valide
        if (!$collecte->isValidStatut('effectuee')) {
            throw $this->createNotFoundException('Statut invalide');
        }

        // Marquer la collecte comme effectuée
        $collecte->setStatut('effectuee');
        $collecte->setCollecteEffectuee(true);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/collecte/{id}', name: 'app_collecte_show')]
    public function show(string $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $collecte = $entityManager->getRepository(Collecte::class)->find((int)$id);
            
            if (!$collecte) {
                $this->addFlash('error', 'La collecte demandée n\'existe pas ou a été supprimée.');
                return $this->redirectToRoute('app_collecte_index');
            }

            // Vérifier que toutes les relations sont correctement chargées
            if (!$collecte->getZone() || !$collecte->getAgent() || !$collecte->getPointCollecte()) {
                $this->addFlash('error', 'La collecte est corrompue (relations manquantes).');
                return $this->redirectToRoute('app_collecte_index');
            }

            return $this->render('collecte/show.html.twig', [
                'collecte' => $collecte,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors du chargement de la collecte.');
            $this->getLogger()->error('Erreur lors du chargement de la collecte : ' . $e->getMessage());
            return $this->redirectToRoute('app_collecte_index');
        }
    }

    #[Route('/collecte/{id}/edit', name: 'app_collecte_edit')]
    public function edit(Request $request, Collecte $collecte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Collecte mise à jour avec succès !');
            return $this->redirectToRoute('app_collecte_index');
        }

        return $this->render('collecte/edit.html.twig', [
            'collecte' => $collecte,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/collecte/{id}', name: 'app_collecte_delete', methods: ['POST'])]
    public function delete(Request $request, Collecte $collecte, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$collecte->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Opération non autorisée');
            return $this->redirectToRoute('app_collecte_index');
        }

        try {
            $entityManager->remove($collecte);
            $entityManager->flush();
            $this->addFlash('success', 'Collecte supprimée avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression');
        }

        return $this->redirectToRoute('app_collecte_index');
    }
}
