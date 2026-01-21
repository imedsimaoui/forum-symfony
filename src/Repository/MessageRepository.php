<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Sujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[]
     */
    public function trouverParSujetTrie(Sujet $sujet): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sujet = :sujet')
            ->setParameter('sujet', $sujet)
            ->orderBy('m.creeLe', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
