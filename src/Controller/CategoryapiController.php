<?php

namespace App\Controller;
use App\Entity\Categories;
use App\Form\CategoriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;  
#[Route('/api')]
class CategoryapiController extends AbstractController
{
    #[Route('/category', name: 'app_categoryapi', methods:['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager
        ->getRepository(Categories::class)
        ->findAll();
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
    #[Route('/category/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function deletecategory(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Categories::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }
    #[Route('/category/add', name: 'category_add', methods: ['GET', 'POST'])]
    public function addcategory(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = new Categories();
        $category->setName($request->request->get('name'));
        
      
    

        $entityManager->persist($category);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/category/{id}', name: 'category_edit', methods: ['PUT'])]
    public function editcategory(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Categories::class)->find($id);

        if (!$category) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $category->setName($request->request->get('name'));

        $entityManager->persist($category);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }
    

}
