<?php

namespace App\Repository;

use App\Entity\Blogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Blogs>
 *
 * @method Blogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blogs[]    findAll()
 * @method Blogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Blogs::class);
  }

  public function save(Blogs $entity, bool $flush = false): void
  {
    $entity->setDate(new \DateTime());
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Blogs $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  //    /**
  //     * @return Blogs[] Returns an array of Blogs objects
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

  public function findOneByTitle($value): ?Blogs
  {
    return $this->createQueryBuilder('b')
      ->andWhere('b.title = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
