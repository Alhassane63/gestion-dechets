<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Form\ZoneType;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/zone')]
class ZoneController extends AbstractController
{
    #[Route('/', name: 'app_zone_index', methods: ['GET'])]
    public function index(ZoneRepository $zoneRepository): Response
    {
        return $this->render('zone/index.html.twig', [
            'zones' => $zoneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_zone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $zone = new Zone();
        $form = $this->createForm(ZoneType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zone);
            $entityManager->flush();
            $this->addFlash('success', 'Zone créée avec succès !');

            return $this->redirectToRoute('app_zone_index');
        }

        return $this->render('zone/new.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_zone_show', methods: ['GET'])]


    #[Route('/{id}/edit', name: 'app_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Zone $zone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ZoneType::class, $zone);
        $deleteForm = $this->createDeleteForm($zone);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Zone mise à jour avec succès !');
            return $this->redirectToRoute('app_zone_index');
        }

        return $this->render('zone/edit.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_zone_delete', methods: ['POST'])]
    public function delete(Request $request, Zone $zone, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zone->getId(), $request->request->get('_token'))) {
            $entityManager->remove($zone);
            $entityManager->flush();
            $this->addFlash('success', 'Zone supprimée avec succès !');
        }

        return $this->redirectToRoute('app_zone_index');
    }

    private function createDeleteForm(Zone $zone)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_zone_delete', ['id' => $zone->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    #[Route('/admin/zones', name: 'app_admin_zones')]
    #[IsGranted('ROLE_ADMIN')]
    public function manageZones(ZoneRepository $zoneRepository): Response
    {
        $zones = $zoneRepository->findAll();
        return $this->render('zone/manage.html.twig', [
            'zones' => $zones
        ]);
    }

    public function show(Zone $zone): Response
    {
        return $this->render('zone/show.html.twig', [
            'zone' => $zone,        ]);
    }




}
