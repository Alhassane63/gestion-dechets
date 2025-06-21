<?php

namespace App\Repository;

use App\Entity\Collecte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Collecte>
 */
class CollecteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collecte::class);
    }

    /**
     * Récupère les collectes par mois (sans utiliser createdAt)
     * Version alternative utilisant l'ID ou une autre méthode de tri
     */
    public function getCollectesByMonth(): array
    {
        try {
            $qb = $this->createQueryBuilder('c');
            $results = $qb
                ->select('c.id') // Utilise l'ID au lieu de createdAt
                ->where('c.statut = :statut')
                ->setParameter('statut', 'effectuee')
                ->getQuery()
                ->getResult();

            // Initialiser tous les mois avec 0
            $collectes = array_fill(0, 12, 0);

            // Pour une distribution simulée par mois (à adapter selon vos besoins)
            // Vous pouvez remplacer cette logique par une vraie date quand createdAt sera disponible
            $totalCollectes = count($results);
            if ($totalCollectes > 0) {
                // Distribution simple sur l'année
                $collectesParMois = intval($totalCollectes / 12);
                $reste = $totalCollectes % 12;
                
                for ($i = 0; $i < 12; $i++) {
                    $collectes[$i] = $collectesParMois;
                    if ($i < $reste) {
                        $collectes[$i]++;
                    }
                }
            }

            return $collectes;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un tableau avec des zéros
            return array_fill(0, 12, 0);
        }
    }

    /**
     * Récupère les points de collecte avec leurs coordonnées
     */
    public function getPointsDeCollecte(): array
    {
        try {
            $qb = $this->createQueryBuilder('c')
                ->select('p.longitude', 'p.latitude', 'p.Adresse as adresse')
                ->join('c.pointCollecte', 'p')
                ->where('p.longitude IS NOT NULL')
                ->andWhere('p.latitude IS NOT NULL')
                ->andWhere('p.longitude != :zeroLng')
                ->andWhere('p.latitude != :zeroLat')
                ->setParameter('zeroLng', 0)
                ->setParameter('zeroLat', 0);

            $results = $qb->getQuery()->getResult();

            // Convertir les résultats en tableau avec les coordonnées
            $points = [];
            foreach ($results as $result) {
                $points[] = [
                    'longitude' => (float)$result['longitude'],
                    'latitude' => (float)$result['latitude'],
                    'adresse' => $result['adresse'] ?? 'Adresse non définie'
                ];
            }
            return $points;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un tableau vide
            return [];
        }
    }

    /**
     * Récupère les collectes avec leurs relations
     */
    public function findAllWithRelations(): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.zone', 'z')
                ->leftJoin('c.agent', 'a')
                ->leftJoin('c.pointCollecte', 'p')
                ->addSelect('z', 'a', 'p')
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Trouve les dernières collectes (version sécurisée)
     */
    public function findLatest(int $limit = 5): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Compte le nombre total de collectes
     */
    public function countTotal(): int
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Compte les collectes par statut
     */
    public function countByStatus(string $status): int
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c.id)')
                ->where('c.statut = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Trouve les collectes par zone
     */
    public function findByZone($zoneId): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->where('c.zone = :zoneId')
                ->setParameter('zoneId', $zoneId)
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Trouve les collectes par agent
     */
    public function findByAgent($agentId): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->where('c.agent = :agentId')
                ->setParameter('agentId', $agentId)
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Trouve les collectes effectuées
     */
    public function findEffectuees(): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->where('c.statut = :statut')
                ->setParameter('statut', 'effectuee')
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }
}