<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api')]
class TagsAPIController extends AbstractController
{
  #[Route('/Tags', name: 'app_Tagsapi', methods: ['GET'])]
  public function index(TagsRepository $tagsRepository): Response
  {
    $tags = $tagsRepository->findAll();
    $responseArray = array();
    foreach ($tags as $tag) {
      $responseArray[] = array(
        'tagId' => $tag->getTagsId(),
        'name' => $tag->getName()
      );
    }

    $responseData = json_encode($responseArray);
    $response = new Response($responseData);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  #[Route('/OneTag/{id}', name: 'app_OneTagapi', methods: ['GET'])]
  public function indexOne(TagsRepository $tagsRepository, $id): Response
  {
    $tag = $tagsRepository->find($id);
    $responseArray = array();
    $responseArray[] = array(
      'tagId' => $tag->getTagsId(),
      'name' => $tag->getName()
    );


    $responseData = json_encode($responseArray);
    $response = new Response($responseData);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  #[Route('/TagDel/{id}', name: 'Tag_delete', methods: ['DELETE'])]
  public function deletecategory(int $id, TagsRepository $tagsRepository): JsonResponse
  {
    $category = $tagsRepository->find($id);

    if (!$category) {
      throw $this->createNotFoundException('This Blog Category does not exist');
    }

    $tagsRepository->remove($category, true);

    $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    return $response;
  }
  #[Route('/Tag/add', name: 'Tag_add', methods: ['GET', 'POST'])]
  public function addcategory(Request $request, TagsRepository $tagsRepository): JsonResponse
  {
    $category = new Tags();
    $category->setName($request->request->get('name'));




    $tagsRepository->save($category, true);

    $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
    return $response;
  }

  #[Route('/Tag/{id}', name: 'Tag_edit', methods: ['PUT'])]
  public function editcategory(Request $request, $id, TagsRepository $tagsRepository): JsonResponse
  {
    $category = $tagsRepository->find($id);

    if (!$category) {
      return new JsonResponse(['status' => 'Faild']);;
    }

    $category->setName($request->request->get('name'));

    $tagsRepository->save($category, true);


    $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
    return $response;
  }
}
