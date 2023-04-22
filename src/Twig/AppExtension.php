<?php

namespace App\Twig;

use App\Repository\HasBlogCategoryRepository;
use App\Repository\HasTagRepository;
use App\Repository\MediaRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
  private MediaRepository $MediaRepository;
  private HasTagRepository $hasTagRepository;
  private HasBlogCategoryRepository $hasBlogCategoryRepository;



  public function __construct(MediaRepository $MediaRepository, HasTagRepository $hasTagRepository, HasBlogCategoryRepository $hasBlogCategoryRepository)
  {
    $this->MediaRepository = $MediaRepository;
    $this->hasTagRepository = $hasTagRepository;
    $this->hasBlogCategoryRepository = $hasBlogCategoryRepository;
  }

  public function getFunctions(): array
  {
    return [
      new TwigFunction('getBlogsMedia', [$this, 'getBlogsMedia']),
      new TwigFunction('getNbBlogsPerTag', [$this, 'getNbBlogsPerTag']),
      new TwigFunction('getNbBlogsPerCat', [$this, 'getNbBlogsPerCat']),
    ];
  }

  public function getBlogsMedia(int $blog_id)
  {
    return $this->MediaRepository->findOneMediaByBlogID($blog_id);
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
}
