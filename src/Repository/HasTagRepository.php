<?php

namespace App\Repository;

use App\Entity\BlogTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<BlogTags>
 *
 * @method BlogTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogTags[]    findAll()
 * @method BlogTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HasTagRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, BlogTags::class);
  }

  public function save(BlogTags $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(BlogTags $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  //    /**
  //     * @return BlogTags[] Returns an array of BlogTags objects
  //     */
  public function findAllBlogsByBlogID($blog_id): array
  {
    return $this->createQueryBuilder('h')
      ->andWhere('h.blog_id = :val')
      ->setParameter('val', $blog_id)
      ->getQuery()
      ->getResult();
  }

  public function findOneByBlogID($blog_id): ?BlogTags
  {
    return $this->createQueryBuilder('h')
      ->andWhere('h.blog_id = :val')
      ->setParameter('val', $blog_id)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
