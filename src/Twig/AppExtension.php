<?php

namespace App\Twig;

use App\Entity\Media;
use App\Entity\Participation;
use App\Entity\User;
use App\Repository\BlogsRepository;
use App\Repository\EventRepository;
use App\Repository\HasBlogCategoryRepository;
use App\Repository\HasTagRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
  private MediaRepository $MediaRepository;
  private HasTagRepository $hasTagRepository;
  private HasBlogCategoryRepository $hasBlogCategoryRepository;
  private BlogsRepository $blogsRepository;
  private User $connectedUser;
  private $en;

  public function __construct(EntityManagerInterface $entityManager,SessionInterface $session, BlogsRepository $blogsRepository, UserRepository $userRepository, MediaRepository $MediaRepository, HasTagRepository $hasTagRepository, HasBlogCategoryRepository $hasBlogCategoryRepository)
  {
    $this->en=$entityManager->getRepository(Participation::class);
    $this->MediaRepository = $MediaRepository;
    $this->blogsRepository = $blogsRepository;
    $this->hasTagRepository = $hasTagRepository;
    $this->hasBlogCategoryRepository = $hasBlogCategoryRepository;
    if ($session != null) {
      $connectedUserID = $session->get('user_id');
      if (is_int($connectedUserID)) {
        $this->connectedUser = $userRepository->find((int) $connectedUserID);
      }
    }
  }

  public function getFunctions(): array
  {
    return [
      new TwigFunction('getBlogsMedia', [$this, 'getBlogsMedia']),
      new TwigFunction('getRemoteBlogsMedia', [$this, 'getRemoteBlogsMedia']),
      new TwigFunction('getNbBlogsPerTag', [$this, 'getNbBlogsPerTag']),
      new TwigFunction('getNbBlogsPerCat', [$this, 'getNbBlogsPerCat']),
      new TwigFunction('getTop3Rated', [$this, 'getTop3Rated']),
      new TwigFunction('returnConnectedUser', [$this, 'returnConnectedUser']),
      new TwigFunction('getParticipation', [$this, 'getParticipation']),
    ];
  }

  public function getParticipation($connectedUserID, $eventID) {
    return $this->en->findOneBy([
      'user' => $connectedUserID,
      'event' => $eventID,    
    ]);
  }

  public function getBlogsMedia(int $blog_id)
  {
    return $this->MediaRepository->findOneMediaByBlogID($blog_id);
  }

  public function getRemoteBlogsMedia(string $blogMediaPath)
  {
    $pcIpAddress = getHostByName(getHostName());
    return str_replace('localhost', $pcIpAddress, $blogMediaPath);
  }
  public function getNbBlogsPerTag(int $tag_id)
  {
    $res = $this->hasTagRepository->findAllBlogsByTagID($tag_id);
    return  count($res);
  }

  public function getNbBlogsPerCat(int $cat_id)
  {
    $res = $this->hasBlogCategoryRepository->findAllBlogsByCatID($cat_id);
    return  count($res);
  }

  public function getTop3Rated()
  {
    $res = $this->blogsRepository->findTop3Rated();
    return  $res;
  }

  public function returnConnectedUser()
  {
    return $this->connectedUser ?? null;
  }
}
