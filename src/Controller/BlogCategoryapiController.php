<?php

namespace App\Controller;

use App\Entity\Blogcategories;
use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\BlogcategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api')]
class BlogCategoryapiController extends AbstractController
{
    #[Route('/BlogCategory', name: 'app_BlogCategoryapi', methods: ['GET'])]
    public function index(BlogcategoriesRepository $blogcategoriesRepository): Response
    {
        $categories = $blogcategoriesRepository->findAll();
        $responseArray = array();
        foreach ($categories as $categorie) {
            $responseArray[] = array(
                'categoriesId' => $categorie->getCategoriesId(),
                'name' => $categorie->getName()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/OneBlogCategory/{id}', name: 'app_OneBlogCategoryapi', methods: ['GET'])]
    public function indexOne(BlogcategoriesRepository $blogcategoriesRepository, $id): Response
    {
        $category = $blogcategoriesRepository->find($id);
        $responseArray = array();
        $responseArray[] = array(
            'categoryId' => $category->getCategoriesId(),
            'name' => $category->getName()
        );


        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/BlogCategoryDel/{id}', name: 'BlogCategory_delete', methods: ['DELETE'])]
    public function deletecategory(int $id, BlogcategoriesRepository $blogcategoriesRepository): JsonResponse
    {
        $category = $blogcategoriesRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('This Blog Category does not exist');
        }

        $blogcategoriesRepository->remove($category, true);

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }
    #[Route('/BlogCategory/add', name: 'BlogCategory_add', methods: ['GET', 'POST'])]
    public function addcategory(Request $request, BlogcategoriesRepository $blogcategoriesRepository): JsonResponse
    {
        $category = new Blogcategories();
        $category->setName($request->request->get('name'));




        $blogcategoriesRepository->save($category, true);

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/BlogCategory/{id}', name: 'BlogCategory_edit', methods: ['PUT'])]
    public function editcategory(Request $request, $id, BlogcategoriesRepository $blogcategoriesRepository): JsonResponse
    {
        $category = $blogcategoriesRepository->find($id);

        if (!$category) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $category->setName($request->request->get('name'));

        $blogcategoriesRepository->save($category, true);


        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }
}
