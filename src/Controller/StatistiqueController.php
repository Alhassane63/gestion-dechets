<?php

namespace App\Controller;

use App\Repository\CollecteRepository;
use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class StatistiqueController extends AbstractController
{
    #[Route('/admin/statistiques', name: 'app_admin_statistiques')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CollecteRepository $collecteRepository, SignalementRepository $signalementRepository): Response
    {
        $stats = [
            'total_collectes' => $collecteRepository->count([]),
            'collectes_par_type' => $collecteRepository->countByType(),
            'total_signalements' => $signalementRepository->count([]),
            'signalements_par_type' => $signalementRepository->countByType(),
        ];
        
        return $this->render('admin/statistiques/index.html.twig', [
            'stats' => $stats
        ]);
    }
}
