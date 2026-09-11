<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }


    public function findAcceptedFriendships(User $user): array
{
    return $this->createQueryBuilder('f')
        ->where('f.statut = :statut')
        ->andWhere('f.demandeur = :user OR f.destinataire = :user')
        ->setParameter('statut', 'accepted')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}
}
