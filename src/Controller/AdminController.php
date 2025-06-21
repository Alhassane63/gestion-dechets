<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tournee;
use App\Entity\PointCollecte;
use App\Entity\Collecte;
use App\Form\UserType;
use App\Form\TourneeType;
use App\Form\PointCollecteType;
use App\Form\CollecteType;
use App\Repository\UserRepository;
use App\Repository\TourneeRepository;
use App\Repository\PointCollecteRepository;
use App\Repository\CollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(
        UserRepository $userRepository,
        TourneeRepository $tourneeRepository,
        PointCollecteRepository $pointCollecteRepository,
        CollecteRepository $collecteRepository
    ): Response {
        $stats = [
            'total_users' => $userRepository->count([]),
            'total_tournées' => $tourneeRepository->count([]),
            'total_points' => $pointCollecteRepository->count([]),
            'collectes_today' => $collecteRepository->count(['dateCollecte' => new \DateTime('today')]),
            'total_citoyens' => $userRepository->count(['roles' => ['ROLE_CITOYEN']]),
            'total_agents' => $userRepository->count(['roles' => ['ROLE_AGENT']]),
            'total_dechets' => $collecteRepository->createQueryBuilder('c')
                ->select('COUNT(DISTINCT c.typeDechets)')
                ->getQuery()
                ->getSingleScalarResult(),
            'total_zones' => $pointCollecteRepository->createQueryBuilder('p')
                ->select('COUNT(DISTINCT p.zone)')
                ->getQuery()
                ->getSingleScalarResult(),
            'collectes_en_attente' => $collecteRepository->count(['statut' => 'en_attente']),
            'collectes_effectuees' => $collecteRepository->count(['statut' => 'effectuee']),
            'signalements_non_traites' => $collecteRepository->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.statut = :statut')
                ->setParameter('statut', 'non_traite')
                ->getQuery()
                ->getSingleScalarResult(),
        ];

        // Récupérer les points de collecte avec leurs coordonnées
        $points = $pointCollecteRepository->createQueryBuilder('p')
            ->select('p, z')
            ->leftJoin('p.zone', 'z')
            ->where('p.longitude IS NOT NULL AND p.latitude IS NOT NULL')
            ->getQuery()
            ->getResult();

        return $this->render('dashboard/admin/index.html.twig', [
            'stats' => $stats,
            'points' => $points
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        return $this->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }
}
