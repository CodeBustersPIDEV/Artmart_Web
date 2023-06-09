<?php

namespace App\Repository;

use App\Entity\Blogs;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class MediaRepository extends ServiceEntityRepository
{
  private $container;

  public function __construct(ManagerRegistry $registry, ContainerInterface $container)
  {
    parent::__construct($registry, Media::class);
    $this->container = $container;
  }

  public function setMediaInfo(Blogs $blog, UploadedFile $file, $entity, $fileBaseUrl)
  {
    $extension = strtoupper(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
    $entity->setFileName(pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME));
    $entity->setFileType($extension);
    $entity->setFilePath($fileBaseUrl . '/' . pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME));
    $entity->setBlog($blog);
  }

  public function editMediaInfo(UploadedFile $file, $entity, $fileBaseUrl)
  {
    $extension = strtoupper(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
    $entity->setFileName(pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME));
    $entity->setFileType($extension);
    $entity->setFilePath($fileBaseUrl . '/' . pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME));
  }

  public function deleteFile($filename)
  {
    $path = $this->container->getParameter('destinationPath');
    $filePath = $path . $filename;
    if (file_exists($filePath)) {
      unlink($filePath);
      echo 'File deleted successfully!';
    } else {
      echo 'File not found.';
    }
  }

  public function save(Media $entity, UploadedFile $file, Blogs $blog, bool $flush = false): void
  {
    $fileBaseUrl = $this->container->getParameter('file_base_url');
    // $filePath = sprintf('%s%s', $fileBaseUrl['host'], $fileBaseUrl['path']);
    $this->setMediaInfo($blog, $file,  $entity, $fileBaseUrl);
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function edit(Media $entity, UploadedFile $file, bool $flush = false): void
  {
    $fileBaseUrl = $this->container->getParameter('file_base_url');

    $this->editMediaInfo($file,  $entity, $fileBaseUrl);

    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Media $entity, bool $flush = false): void
  {
    $this->deleteFile($entity->getFileName());
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
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

  public function findOneMediaByBlogID($blog_id): ?Media
  {
    return $this->createQueryBuilder('m')
      ->andWhere('m.blog_id = :val')
      ->setParameter('val', $blog_id)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
