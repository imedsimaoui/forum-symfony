<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Theme>
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    /**
     * @return array<int, array{theme: Theme, sujetCount: string, lastSujetAt: ?\DateTimeInterface}>
     */
    public function trouverPageAvecStats(int $page, int $limite): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.sujets', 's')
            ->addSelect('COUNT(s.id) AS sujetCount')
            ->addSelect('MAX(s.creeLe) AS lastSujetAt')
            ->groupBy('t.id')
            ->orderBy('t.titre', 'ASC')
            ->setFirstResult(($page - 1) * $limite)
            ->setMaxResults($limite);

        return $qb->getQuery()->getResult();
    }

    public function compterTous(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
