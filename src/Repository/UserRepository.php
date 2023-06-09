<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, User::class);
  }

  public function save(User $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }


  public function remove(User $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  //    /**
  //     * @return User[] Returns an array of User objects
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
  public function countUsersByRole($role)
  {
    return $this->createQueryBuilder('u')
      ->select('COUNT(u.userId)')
      ->where('u.role = :role')
      ->setParameter('role', $role)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function findOneUserByEmail($email): ?User
  {
    return $this->createQueryBuilder('u')
      ->andWhere('u.email = :val')
      ->setParameter('val', $email)
      ->getQuery()
      ->getOneOrNullResult();
  }

  public function findByUserId(int $userId): ?User
  {
    return $this->createQueryBuilder('u')
      ->andWhere('u.user_ID = :userId')
      ->setParameter('userId', $userId)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
