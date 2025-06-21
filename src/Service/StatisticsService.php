<?php

namespace App\Service;

use App\Entity\Collecte;
use App\Entity\Signalement;
use App\Entity\Zone;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class StatisticsService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getGeneralStats(DateTime $startDate, DateTime $endDate): array
    {
        $stats = [];

        // Collectes
        $stats['collectes'] = [
            'total' => $this->getTotalCollectes($startDate, $endDate),
            'effectuees' => $this->getCompletedCollectes($startDate, $endDate),
            'taux' => $this->getCollecteRate($startDate, $endDate)
        ];

        // Signalements
        $stats['signalements'] = [
            'total' => $this->getTotalSignalements($startDate, $endDate),
            'traites' => $this->getTreatedSignalements($startDate, $endDate),
            'taux' => $this->getSignalementTreatmentRate($startDate, $endDate)
        ];

        // Types de déchets
        $stats['typesDechets'] = $this->getStatsByTypeDechets($startDate, $endDate);

        // Zones
        $stats['zones'] = $this->getStatsByZone($startDate, $endDate);

        return $stats;
    }

    public function getAgentStats(DateTime $startDate, DateTime $endDate): array
    {
        $agents = $this->entityManager->getRepository(User::class)
            ->findBy(['roles' => 'ROLE_AGENT']);

        $stats = [];
        foreach ($agents as $agent) {
            $agentStats = [
                'collectes' => $this->getAgentCollectes($agent, $startDate, $endDate),
                'tauxCollecte' => $this->getAgentCollecteRate($agent, $startDate, $endDate),
                'tournées' => $this->getAgentTournées($agent),
                'efficiency' => $this->getAgentEfficiency($agent, $startDate, $endDate)
            ];

            $stats[$agent->getId()] = $agentStats;
        }

        return $stats;
    }

    public function getZoneStats(DateTime $startDate, DateTime $endDate): array
    {
        $zones = $this->entityManager->getRepository(Zone::class)->findAll();
        $stats = [];

        foreach ($zones as $zone) {
            $zoneStats = [
                'collectes' => $this->getZoneCollectes($zone, $startDate, $endDate),
                'tauxCollecte' => $this->getZoneCollecteRate($zone, $startDate, $endDate),
                'points' => $this->getZonePoints($zone),
                'signalements' => $this->getZoneSignalements($zone, $startDate, $endDate)
            ];

            $stats[$zone->getId()] = $zoneStats;
        }

        return $stats;
    }

    // Méthodes privées pour calculer les statistiques
    private function getTotalCollectes(DateTime $startDate, DateTime $endDate): int
    {
        return $this->entityManager->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.dateCollecte BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->select('COUNT(c)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getCompletedCollectes(DateTime $startDate, DateTime $endDate): int
    {
        return $this->entityManager->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.dateCollecte BETWEEN :start AND :end')
            ->andWhere('c.collecteEffectuee = true')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->select('COUNT(c)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getCollecteRate(DateTime $startDate, DateTime $endDate): float
    {
        $total = $this->getTotalCollectes($startDate, $endDate);
        if ($total === 0) {
            return 0;
        }
        return ($this->getCompletedCollectes($startDate, $endDate) / $total) * 100;
    }

    private function getTotalSignalements(DateTime $startDate, DateTime $endDate): int
    {
        return $this->entityManager->getRepository(Signalement::class)
            ->createQueryBuilder('s')
            ->where('s.dateSignalement BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->select('COUNT(s)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getTreatedSignalements(DateTime $startDate, DateTime $endDate): int
    {
        return $this->entityManager->getRepository(Signalement::class)
            ->createQueryBuilder('s')
            ->where('s.dateSignalement BETWEEN :start AND :end')
            ->andWhere('s.etat = :etat')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('etat', 'traite')
            ->select('COUNT(s)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getSignalementTreatmentRate(DateTime $startDate, DateTime $endDate): float
    {
        $total = $this->getTotalSignalements($startDate, $endDate);
        if ($total === 0) {
            return 0;
        }
        return ($this->getTreatedSignalements($startDate, $endDate) / $total) * 100;
    }

    private function getStatsByTypeDechets(DateTime $startDate, DateTime $endDate): array
    {
        $stats = [];
        $collectes = $this->entityManager->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.dateCollecte BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();

        foreach ($collectes as $collecte) {
            $type = $collecte->getPointCollecte()->getTypeDechets();
            if (!isset($stats[$type])) {
                $stats[$type] = [
                    'collectes' => 0,
                    'pourcentage' => 0
                ];
            }
            $stats[$type]['collectes']++;
        }

        $total = count($collectes);
        foreach ($stats as &$stat) {
            $stat['pourcentage'] = ($stat['collectes'] / $total) * 100;
        }

        return $stats;
    }

    private function getStatsByZone(DateTime $startDate, DateTime $endDate): array
    {
        $stats = [];
        $zones = $this->entityManager->getRepository(Zone::class)->findAll();

        foreach ($zones as $zone) {
            $stats[$zone->getId()] = [
                'collectes' => $this->getZoneCollectes($zone, $startDate, $endDate),
                'tauxCollecte' => $this->getZoneCollecteRate($zone, $startDate, $endDate),
                'signalements' => $this->getZoneSignalements($zone, $startDate, $endDate)
            ];
        }

        return $stats;
    }

    private function getAgentCollectes(User $agent, DateTime $startDate, DateTime $endDate): int
    {
        return $this->entityManager->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.dateCollecte BETWEEN :start AND :end')
            ->andWhere('c.agent = :agent')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('agent', $agent)
            ->select('COUNT(c)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getAgentCollecteRate(User $agent, DateTime $startDate, DateTime $endDate): float
    {
        $total = $this->getAgentCollectes($agent, $startDate, $endDate);
        if ($total === 0) {
            return 0;
        }
        return ($this->getAgentCompletedCollectes($agent, $startDate, $endDate) / $total) * 100;
    }

    private function getAgentTournées(User $agent): array
    {
        return $this->entityManager->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.agent = :agent')
            ->setParameter('agent', $agent)
            ->select('DISTINCT c.tournee')
            ->getQuery()
            ->getResult();
    }

    private function getAgentEfficiency(User $agent, DateTime $startDate, DateTime $endDate): float
    {
        $total = $this->getAgentCollectes($agent, $startDate, $endDate);
        if ($total === 0) {
            return 0;
        }
        return ($this->getAgentCompletedCollectes($agent, $startDate, $endDate) / $total) * 100;
    }

    private function getZoneCollectes(Zone $zone, DateTime $startDate, DateTime $endDate): int
    {
        return $this->entityManager->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.dateCollecte BETWEEN :start AND :end')
            ->andWhere('c.pointCollecte.zone = :zone')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('zone', $zone)
            ->select('COUNT(c)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getZoneCollecteRate(Zone $zone, DateTime $startDate, DateTime $endDate): float
    {
        $total = $this->getZoneCollectes($zone, $startDate, $endDate);
        if ($total === 0) {
            return 0;
        }
        return ($this->getZoneCompletedCollectes($zone, $startDate, $endDate) / $total) * 100;
    }

    private function getZonePoints(Zone $zone): int
    {
        return $this->entityManager->getRepository(PointCollecte::class)
            ->createQueryBuilder('p')
            ->where('p.zone = :zone')
            ->setParameter('zone', $zone)
            ->select('COUNT(p)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getZoneSignalements(Zone $zone, DateTime $startDate, DateTime $endDate): array
    {
        $stats = [];
        $signalements = $this->entityManager->getRepository(Signalement::class)
            ->createQueryBuilder('s')
            ->where('s.dateSignalement BETWEEN :start AND :end')
            ->andWhere('s.zone = :zone')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('zone', $zone)
            ->getQuery()
            ->getResult();

        foreach ($signalements as $signalement) {
            $type = $signalement->getType();
            if (!isset($stats[$type])) {
                $stats[$type] = [
                    'nombre' => 0,
                    'taux' => 0
                ];
            }
            $stats[$type]['nombre']++;
        }

        $total = count($signalements);
        foreach ($stats as &$stat) {
            $stat['taux'] = ($stat['nombre'] / $total) * 100;
        }

        return $stats;
    }
}
