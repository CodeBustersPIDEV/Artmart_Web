<?php

namespace App\Repository;

use App\Entity\Blogs;
use App\Entity\Media;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
//use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
//extends ServiceEntityRepository

class MediaRepository 
{
  //private $container;
/*
  public function __construct(ManagerRegistry $registry, ContainerInterface $container)
  {
    parent::__construct($registry, Media::class);
    $this->container = $container;
  }
*/
  public function setMediaInfo(Blogs $blog, UploadedFile $file, $entity, $fileBaseUrl)
  {/*
    $extension = strtoupper(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
    $entity->setFileName(pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME));
    $entity->setFileType($extension);
    $entity->setFilePath(implode('/', $fileBaseUrl) . '/' . pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME));
    $entity->setBlog($blog);*/
  }

  public function save(Media $entity, UploadedFile $file, Blogs $blog, bool $flush = false): void
  {/*
    $fileBaseUrl = $this->container->getParameter('file_base_url');
    $filePath = sprintf('%s%s', $fileBaseUrl['host'], $fileBaseUrl['path']);
    $this->setMediaInfo($blog, $file,  $entity, $fileBaseUrl);
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }*/
  }

  public function remove(Media $entity, bool $flush = false): void
  {/*
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }*/
  }



  //    /**
  //     * @return Media[] Returns an array of Media objects
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

}

