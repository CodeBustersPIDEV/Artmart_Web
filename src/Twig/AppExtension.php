<?php

namespace App\Twig;

use App\Repository\MediaRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
  public MediaRepository $MediaRepository;

  public function getFunctions(): array
  {
    return [
      new TwigFunction('getBlogsMedia', [$this, 'getBlogsMedia']),
    ];
  }

  public function getBlogsMedia(int $blog_id)
  {
    return $this->MediaRepository->findOneMediaByBlogID($blog_id);
  }
}
