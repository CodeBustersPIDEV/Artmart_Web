<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   public function findByUser($userID): array
   {
       return $this->createQueryBuilder('e')
           ->andWhere('e.user = :val')
           ->setParameter('val', $userID)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findAllSortedByName($name, $connectedUserId, $showOtherEvents): array
   {
       $queryBuilder = $this->createQueryBuilder('e')
           ->join('e.user', 'u')
           ->orderBy('e.name', $name);
       
       if ($showOtherEvents) {
           $queryBuilder->where('u.userId != :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       } else {
           $queryBuilder->where('u.userId = :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       }
       
       return $queryBuilder->getQuery()
                           ->getResult();
   }
            

   public function findAllSortedByPrice($sort, $connectedUserId, $showOtherEvents): array
   {
       $queryBuilder = $this->createQueryBuilder('e')
           ->join('e.user', 'u')
           ->orderBy('e.entryfee', $sort);
       
       if ($showOtherEvents) {
           $queryBuilder->where('u.userId != :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       } else {
           $queryBuilder->where('u.userId = :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       }
       
       return $queryBuilder->getQuery()
                           ->getResult();
   }
   
   public function findByType($type, $connectedUserId, $showOtherEvents): array
   {
       $queryBuilder = $this->createQueryBuilder('e')
           ->join('e.user', 'u')
           ->andWhere('e.type = :val')
           ->setParameter('val', $type);
       
       if ($showOtherEvents) {
           $queryBuilder->andWhere('u.userId != :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       } else {
           $queryBuilder->andWhere('u.userId = :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       }
       
       return $queryBuilder->getQuery()
                           ->getResult();
   }
   
   public function findByStatus($status, $connectedUserId, $showOtherEvents): array
   {
       $queryBuilder = $this->createQueryBuilder('e')
           ->join('e.user', 'u')
           ->andWhere('e.status = :val')
           ->setParameter('val', $status);
       
       if ($showOtherEvents) {
           $queryBuilder->andWhere('u.userId != :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       } else {
           $queryBuilder->andWhere('u.userId = :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       }
       
       return $queryBuilder->getQuery()
                           ->getResult();
   }
   
   public function findByTerm($searchTerm, $connectedUserId, $showOtherEvents): array
   {
       $queryBuilder = $this->createQueryBuilder('e')
           ->join('e.user', 'u')
           ->where('e.name LIKE :searchTerm')
           ->setParameter('searchTerm', '%' . $searchTerm . '%');
       
       if ($showOtherEvents) {
           $queryBuilder->andWhere('u.userId != :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       } else {
           $queryBuilder->andWhere('u.userId = :connectedUserId')
                        ->setParameter('connectedUserId', $connectedUserId);
       }
       
       return $queryBuilder->getQuery()
                           ->getResult();
   }
      

    // public function findByTerm($searchTerm, $role): array
    // {
    //     return $this->createQueryBuilder('e')
    //     ->join('e.user', 'u')
    //     ->where('u.role = :role')
    //     ->setParameter('role', $role)
    //     ->andWhere('e.name LIKE :searchTerm')
    //     ->setParameter('searchTerm', '%' . $searchTerm . '%')
    //     ->getQuery()
    //     ->getResult();
    // }

}
//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findAllOtherEvents($userID): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.user != :val')
//            ->setParameter('val', $userID)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
    // public function findByStatus($status, $userID): array
    // {
    //     return $this->createQueryBuilder('e')
    //         ->andWhere('e.status = :status')
    //         ->andWhere('e.user = :userID')
    //         ->setParameter('status', $status)
    //         ->setParameter('userID', $userID)
    //         ->getQuery()
    //         ->getResult();
    // }
//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
