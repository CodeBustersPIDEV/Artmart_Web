<?php
namespace App\Repository;

use App\Entity\Apply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ApplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apply::class);
    }

    public function findAllByArtistId(int $artistId): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.artist = :artistId')
            ->setParameter('artistId', $artistId);

        return $qb->getQuery()->getResult();
    }

    public function findAllByCustomProductId(int $customProductId): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.customproduct = :customProductId')
            ->setParameter('customProductId', $customProductId);

        return $qb->getQuery()->getResult();
    }
}
