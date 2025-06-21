<?php

namespace App\Controller;

use App\Entity\Tournee;
use App\Entity\PointCollecte;
use App\Entity\Collecte;
use App\Form\CollecteType;
use App\Repository\TourneeRepository;
use App\Repository\PointCollecteRepository;
use App\Repository\CollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\SecurityHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AgentController extends AbstractController
{
    #[Route('/agent', name: 'app_agent_dashboard')]
    #[IsGranted('ROLE_AGENT')]
    public function dashboard(
        TourneeRepository $tourneeRepository,
        PointCollecteRepository $pointCollecteRepository,
        CollecteRepository $collecteRepository,
        SecurityHelper $securityHelper
    ): Response {
        $agent = $securityHelper->getUser();
        $tournées = $tourneeRepository->findBy(['agent' => $agent]);
        
        return $this->render('agent/dashboard.html.twig', [
            'tournées' => $tournées
        ]);
    }

    #[Route('/agent/collecte/{id}', name: 'app_agent_collecte')]
    #[IsGranted('ROLE_AGENT')]
    #[Route('/admin/agents', name: 'app_admin_agents')]
    #[IsGranted('ROLE_ADMIN')]
    public function listAgents(UserRepository $userRepository): Response
    {
        $agents = $userRepository->findBy(['roles' => 'ROLE_AGENT']);
        return $this->render('agent/list.html.twig', [
            'agents' => $agents
        ]);
    }

    public function collecte(
        Request $request,
        PointCollecte $point,
        CollecteRepository $collecteRepository
    ): Response {
        $collecte = new Collecte();
        $collecte->setPointCollecte($point);
        $collecte->setAgent($this->getUser());
        $collecte->setDateCollecte(new \DateTime());

        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collecteRepository->save($collecte, true);
            $this->addFlash('success', 'Collecte enregistrée avec succès');
            return $this->redirectToRoute('app_agent_dashboard');
        }

        return $this->render('agent/collecte.html.twig', [
            'form' => $form->createView(),
            'point' => $point
        ]);
    }

    // Autres méthodes pour gérer les collectes et les points de collecte
}
