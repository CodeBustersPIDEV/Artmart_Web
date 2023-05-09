<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Media;
use App\Entity\Blogs;
use App\Entity\User;
use App\Repository\BlogsRepository;
use App\Repository\HasBlogCategoryRepository;
use App\Repository\HasTagRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\UserRepository;

#[Route('/api')]
class ApiBlogsController extends AbstractController
{
  #[Route('/AllBlogs', name: 'app_blogs_api', methods: ['GET'])]
  public function index(BlogsRepository $blogsRepository, MediaRepository $mediaRepository, HasBlogCategoryRepository $hasBlogCategoryRepository, HasTagRepository $hasTagRepository): Response
  {
    $blogs = $blogsRepository->findAll();
    $responseArray = array();
    foreach ($blogs as $blog) {
      $blog_media = $mediaRepository->findOneMediaByBlogID($blog->getBlogsId());
      $blog_category = $hasBlogCategoryRepository->findOneByBlogID($blog->getBlogsId());
      $blog_tags = $hasTagRepository->findAllBlogsByBlogID($blog->getBlogsId());
      $tags = [];
      foreach ($blog_tags as $tag) {
        $tags[] = $tag->getTag()->getName();
      }
      $responseArray[] = array(
        'blogID' => $blog->getBlogsId(),
        'title' => $blog->getTitle(),
        'content' => $blog->getContent(),
        'date' => $blog->getDate(),
        'rating' => $blog->getRating(),
        'nbViews' => $blog->getNbViews(),
        'category' => $blog_category->getCategory()->getName(),
        'tags' => $tags,
        'author' => $blog->getAuthor()->getUserId(),
        'image' => $blog_media->getFilePath() . ""
      );
    }

    $responseData = json_encode($responseArray);
    $response = new Response($responseData);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  #[Route('/blog/{id}', name: 'blog_delete', methods: ['DELETE'])]
  public function deletecustomproduct(int $id, BlogsRepository $blogsRepository, MediaRepository $mediaRepository, HasBlogCategoryRepository $hasBlogCategoryRepository, HasTagRepository $hasTagRepository): JsonResponse
  {
    $blog = $blogsRepository->find($id);
    $blog_media = $mediaRepository->findOneMediaByBlogID($id);
    $blog_category = $hasBlogCategoryRepository->findOneByBlogID($id);
    $blog_tags = $hasTagRepository->findAllBlogsByBlogID($id);

    if (!$blog) {
      throw $this->createNotFoundException('This Blog does not exist');
    }
    $mediaRepository->remove($blog_media, true);
    $hasBlogCategoryRepository->remove($blog_category, true);
    foreach ($blog_tags as $tag) {
      $hasTagRepository->remove($tag, true);
    }
    $blogsRepository->remove($blog, true);

    $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    return $response;
  }
}
