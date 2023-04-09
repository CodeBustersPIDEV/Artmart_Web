<?php

namespace App\Repository;

use App\Entity\HasBlogCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<HasBlogCategory>
 *
 * @method HasBlogCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method HasBlogCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method HasBlogCategory[]    findAll()
 * @method HasBlogCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HasBlogCategoryRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, HasBlogCategory::class);
  }

  public function save(HasBlogCategory $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(HasBlogCategory $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  //    /**
  //     * @return HasBlogCategory[] Returns an array of HasBlogCategory objects
  //     */
  // public function findAllBlogsByBlogID($blog_id): array
  // {
  //   return $this->createQueryBuilder('h')
  //     ->andWhere('h.blog_id = :val')
  //     ->setParameter('val', $blog_id)
  //     ->getQuery()
  //     ->getResult();
  // }

  public function findOneByBlogID($blog_id): ?HasBlogCategory
  {
    return $this->createQueryBuilder('h')
      ->andWhere('h.blog_id = :val')
      ->setParameter('val', $blog_id)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
