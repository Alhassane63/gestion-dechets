<?php

namespace App\Controller;

use App\Entity\PointCollecte;
use App\Repository\PointCollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PointCollecteType;

class PointCollecteController extends AbstractController
{
    #[Route('/admin/points', name: 'app_admin_points')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(PointCollecteRepository $pointCollecteRepository): Response
    {
        $points = $pointCollecteRepository->findAll();
        
        return $this->render('admin/points/index.html.twig', [
            'points' => $points
        ]);
    }

    #[Route('/admin/points/new', name: 'app_point_collecte_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $point = new PointCollecte();
        $form = $this->createForm(PointCollecteType::class, $point);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($point);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_points');
        }

        return $this->render('admin/points/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/points/{id}/edit', name: 'app_point_collecte_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, PointCollecte $point, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PointCollecteType::class, $point);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_points');
        }

        return $this->render('admin/points/edit.html.twig', [
            'point' => $point,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/points/{id}', name: 'app_point_collecte_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, PointCollecte $point, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete' . $point->getId(), $request->request->get('_token'))) {
            $entityManager->remove($point);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false], 400);
    }

    #[Route('/admin/points/{id}', name: 'app_point_collecte_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function show(PointCollecte $point): Response
    {
        return $this->render('admin/points/show.html.twig', [
            'point' => $point
        ]);
    }
}
