<?php

namespace App\Controller;

use App\Repository\ZoneRepository;
use App\Repository\DechetRepository;
use App\Repository\CollecteRepository;
use App\Repository\UserRepository;
use App\Repository\PointCollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        ZoneRepository $zoneRepository,
        DechetRepository $dechetRepository,
        CollecteRepository $collecteRepository,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_dashboard_admin');
        } elseif ($this->isGranted('ROLE_AGENT')) {
            return $this->redirectToRoute('app_dashboard_agent');
        } elseif ($this->isGranted('ROLE_CITOYEN')) {
            return $this->redirectToRoute('app_dashboard_citoyen');
        } else {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }
    }

    #[Route('/dashboard/admin', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboardAdmin(
        ZoneRepository $zoneRepository,
        DechetRepository $dechetRepository,
        CollecteRepository $collecteRepository,
        UserRepository $userRepository,
        PointCollecteRepository $pointCollecteRepository
    ): Response {
        $stats = [
            'total_zones' => $zoneRepository->count([]),
            'total_dechets' => $dechetRepository->count([]),
            'total_collectes' => $collecteRepository->count([]),
            'total_agents' => $userRepository->count(['roles' => 'ROLE_AGENT']),
            'total_citoyens' => $userRepository->count(['roles' => 'ROLE_CITOYEN']),
            'last_collectes' => $collecteRepository->findBy([], ['id' => 'DESC'], 5),
            'collectes_par_mois' => $this->getCollectesParMois($collecteRepository),
            'dechets_par_type' => $this->getDechetsParType($dechetRepository),
            'collectes_today' => $collecteRepository->count(['statut' => 'effectuee']),
            'collectes_en_attente' => $collecteRepository->count(['statut' => 'en_attente']),
            'signalements_non_traites' => $this->getSignalementsNonTraites($collecteRepository),
            'collectes_effectuees' => $this->getCollectesEffectueesCeMois($collecteRepository),
            'points' => $pointCollecteRepository->createQueryBuilder('p')
                ->where('p.longitude IS NOT NULL')
                ->andWhere('p.latitude IS NOT NULL')
                ->andWhere('p.longitude != 0')
                ->andWhere('p.latitude != 0')
                ->getQuery()
                ->getResult()
        ];

        return $this->render('dashboard/admin/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    private function getCollectesParMois(CollecteRepository $collecteRepository): array
    {
        return $collecteRepository->getCollectesByMonth();
    }

    private function getDechetsParType(DechetRepository $dechetRepository): array
    {
        return $dechetRepository->createQueryBuilder('d')
            ->select('d.type, COUNT(d.id) as total')
            ->groupBy('d.type')
            ->orderBy('d.type')
            ->getQuery()
            ->getResult();
    }

    private function getSignalementsNonTraites(CollecteRepository $collecteRepository): int
    {
        return $collecteRepository->count(['statut' => 'en_attente']);
    }

    private function getCollectesEffectueesCeMois(CollecteRepository $collecteRepository): int
    {
        return $collecteRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'effectuee')
            ->getQuery()
            ->getSingleScalarResult();
    }

    #[Route('/dashboard/agent', name: 'app_dashboard_agent')]
    #[IsGranted('ROLE_AGENT')]
    public function dashboardAgent(
        ZoneRepository $zoneRepository,
        DechetRepository $dechetRepository,
        CollecteRepository $collecteRepository,
        PointCollecteRepository $pointCollecteRepository
    ): Response {
        $user = $this->getUser();

        $points = $pointCollecteRepository->createQueryBuilder('p')
            ->where('p.longitude IS NOT NULL AND p.latitude IS NOT NULL')
            ->getQuery()
            ->getResult();

        $stats = [
            'total_collectes' => $collecteRepository->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.agent = :agent')
                ->setParameter('agent', $user)
                ->getQuery()
                ->getSingleScalarResult(),
            'total_points' => count($points),
            'collectes_en_attente' => $collecteRepository->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.agent = :agent')
                ->andWhere('c.statut = :statut')
                ->setParameter('agent', $user)
                ->setParameter('statut', 'en_attente')
                ->getQuery()
                ->getSingleScalarResult(),
            'collectes_effectuees' => $collecteRepository->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.agent = :agent')
                ->andWhere('c.statut = :statut')
                ->setParameter('agent', $user)
                ->setParameter('statut', 'effectuee')
                ->getQuery()
                ->getSingleScalarResult(),
            'collectes_en_retard' => $collecteRepository->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.agent = :agent')
                ->andWhere('c.statut = :statut')
                ->setParameter('agent', $user)
                ->setParameter('statut', 'en_attente')
                ->getQuery()
                ->getSingleScalarResult(),
            'points_dans_zone' => $pointCollecteRepository->createQueryBuilder('p')
                ->select('COUNT(DISTINCT p)')
                ->leftJoin('p.tournee', 't')
                ->leftJoin('t.pointCollectes', 'pc')
                ->leftJoin('pc.collectes', 'c')
                ->leftJoin('c.agent', 'a')
                ->where('p.longitude IS NOT NULL')
                ->andWhere('p.latitude IS NOT NULL')
                ->andWhere('p.longitude != 0')
                ->andWhere('p.latitude != 0')
                ->andWhere('a.id = :agent_id')
                ->setParameter('agent_id', $user->getId())
                ->getQuery()
                ->getSingleScalarResult(),
            'collectes' => $collecteRepository->createQueryBuilder('c')
                ->select('c, p')
                ->leftJoin('c.pointCollecte', 'p')
                ->where('c.agent = :agent')
                ->andWhere('c.collecteEffectuee = :effectue')
                ->setParameter('agent', $user)
                ->setParameter('effectue', false)
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult(),
            'points' => $collecteRepository->getPointsDeCollecte()
        ];

        return $this->render('dashboard/agent/index.html.twig', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    #[Route('/dashboard/citoyen', name: 'app_dashboard_citoyen')]
    #[IsGranted('ROLE_CITOYEN')]
    public function dashboardCitoyen(
        DechetRepository $dechetRepository,
        CollecteRepository $collecteRepository,
        PointCollecteRepository $pointCollecteRepository
    ): Response {
        $user = $this->getUser();

        $points = $pointCollecteRepository->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.tournee', 't')
            ->where('p.longitude IS NOT NULL')
            ->andWhere('p.latitude IS NOT NULL')
            ->andWhere('p.longitude != 0')
            ->andWhere('p.latitude != 0')
            ->getQuery()
            ->getResult();

        $types_dechets = $dechetRepository->createQueryBuilder('d')
            ->select('DISTINCT d.type')
            ->getQuery()
            ->getArrayResult();

        $stats = [
            'total_signalements' => $dechetRepository->count(['citoyen' => $user]),
            'total_collectes' => $collecteRepository->count(['agent' => $user]),
            'total_points' => count($points),
            'last_signalements' => $dechetRepository->findBy(['citoyen' => $user], ['date' => 'DESC'], 5),
            'types_dechets' => array_column($types_dechets, 'type'),
            'points' => $points
        ];

        return $this->render('dashboard/citoyen/index.html.twig', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
