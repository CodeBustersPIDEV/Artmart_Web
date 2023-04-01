<?php

namespace App\Repository;

use App\Entity\Customproduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customproduct>
 *
 * @method Customproduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customproduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customproduct[]    findAll()
 * @method Customproduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomproductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customproduct::class);
    }

    public function save(Customproduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customproduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function search($query)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.product', 'p')
            ->where('p.name LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery();

        return $qb->getResult();
    }

//    /**
//     * @return Customproduct[] Returns an array of Customproduct objects
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

//    public function findOneBySomeField($value): ?Customproduct
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
