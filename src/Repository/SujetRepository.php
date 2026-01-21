<?php

namespace App\Repository;

use App\Entity\Sujet;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sujet>
 */
class SujetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sujet::class);
    }

    /**
     * @return Sujet[]
     */
    public function trouverPageParTheme(Theme $theme, int $page, int $limite): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.theme = :theme')
            ->setParameter('theme', $theme)
            ->orderBy('s.creeLe', 'ASC')
            ->setFirstResult(($page - 1) * $limite)
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }

    public function compterParTheme(Theme $theme): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.theme = :theme')
            ->setParameter('theme', $theme)
            ->getQuery()
            ->getSingleScalarResult();
    }


}
