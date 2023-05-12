<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Comments>
 *
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findCommentsByBlogID($blog_id)
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Comments::class);
  }

  public function save(Comments $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Comments $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  //    /**
  //     * @return Comments[] Returns an array of Comments objects
  //     */
  public function findCommentsByBlogID($blog_id): array
  {
    return $this->createQueryBuilder('c')
      ->andWhere('c.blog_ID = :val')
      ->setParameter('val', $blog_id)
      ->getQuery()
      ->getResult();
  }

  //    public function findOneBySomeField($value): ?Comments
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
