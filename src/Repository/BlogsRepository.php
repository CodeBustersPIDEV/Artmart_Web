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
 * @method Blogs[]    findAllDesc()
 * @method Blogs[]    findAllAsc()
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

  public function editViews(Blogs $entity, int $nb, bool $flush = false): void
  {
    $entity->setNbViews($nb);
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function editRating(Blogs $entity, float $rate, bool $flush = false): void
  {
    $entity->setRating($rate);
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

  public function findAllTop4(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.nbViews', 'DESC')
      ->setMaxResults(4)
      ->getQuery()
      ->getResult();
  }
  public function findAllDesc(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.rating', 'DESC')
      ->getQuery()
      ->getResult();
  }

  public function findAllAsc(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.rating', 'ASC')
      ->getQuery()
      ->getResult();
  }

  public function findAllTitleDesc(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.title', 'DESC')
      ->getQuery()
      ->getResult();
  }

  public function findAllTitleAsc(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.title', 'ASC')
      ->getQuery()
      ->getResult();
  }

  public function findAllViewsDesc(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.nbViews', 'DESC')
      ->getQuery()
      ->getResult();
  }

  public function findAllViewsAsc(): array
  {
    return $this->createQueryBuilder('b')
      ->orderBy('b.nbViews', 'ASC')
      ->getQuery()
      ->getResult();
  }


  public function findOneByTitle($value): ?Blogs
  {
    return $this->createQueryBuilder('b')
      ->andWhere('b.title = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult();
  }

  public function findByTerm($searchTerm): array
  {
    return $this->createQueryBuilder('b')
      ->where('b.title LIKE :searchTerm')
      ->setParameter('searchTerm', '%' . $searchTerm . '%')
      ->getQuery()
      ->getResult();
  }

  public function findMyBlogsByTerm($searchTerm, $user): array
  {
    return $this->createQueryBuilder('b')
      ->where('b.title LIKE :searchTerm')
      ->andWhere('b.author = :user')
      ->setParameter('searchTerm', '%' . $searchTerm . '%')
      ->setParameter('user', $user)
      ->getQuery()
      ->getResult();
  }

  public function findAllByUser($author): array
  {
    return $this->createQueryBuilder('b')
      ->andWhere('b.author = :val')
      ->setParameter('val', $author)
      ->getQuery()
      ->getResult();
  }
}
