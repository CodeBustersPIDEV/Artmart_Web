<?php

namespace App\Repository;

use App\Entity\Tags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Tags>
 *
 * @method Tags|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tags|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tags[]    findAll()
 * @method Tags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Tags::class);
  }

  public function save(Tags $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Tags $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  //    /**
  //     * @return Tags[] Returns an array of Tags objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('c.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Tags
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
  public function findByTerm($searchTerm): array
  {
    return $this->createQueryBuilder('t')
      ->where('t.name LIKE :searchTerm')
      ->setParameter('searchTerm', '%' . $searchTerm . '%')
      ->getQuery()
      ->getResult();
  }
}
