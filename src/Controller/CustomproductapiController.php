<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customproduct;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api')]
class CustomproductapiController extends AbstractController
{
    #[Route('/customproduct', name: 'app_customproductapi', methods:['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $customprodcut = $entityManager
        ->getRepository(Customproduct::class)
        ->findAll();
        $responseArray = array();
        foreach ($customprodcut as $customprodcut) {
            $responseArray[] = array(
                'customProductId' => $customprodcut->getcustomProductId(),
                'product' => $customprodcut->getproduct()->getName(),
                'product1' => $customprodcut->getproduct()->getDescription(),
                'product2' => $customprodcut->getproduct()->getDimensions(),
                'product3' => $customprodcut->getproduct()->getWeight(),
                'product4' => $customprodcut->getproduct()->getMaterial(),
                'product5' => $customprodcut->getproduct()->getImage(),
                'client' => $customprodcut->getClient()->getUserId(),
                'product6' => $customprodcut->getproduct()->getCategoryId()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    #[Route('/customproduct/add', name: 'customproduct', methods: ['GET', 'POST'])]
    public function addcustmoproduct(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customproduct = new customproduct();
        $product = new product();
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setDimensions($request->request->get('dimensions'));
        $product->setWeight($request->request->get('weight'));
        $product->setImage($request->request->get('image'));
        $product->setMaterial($request->request->get('material'));
        $idCategorie = $request->request->getInt('categoryId');
        $categorie = $entityManager->getRepository(Categories::class)->find($idCategorie);
        $product->setCategory($categorie);
        $idClient = $request->request->getInt('client');
        $client = $entityManager->getRepository(User::class)->find($idClient);
        $entityManager->persist($product);
        $customproduct->setProduct($product);
        $customproduct->setClient($client);

        $entityManager->persist($customproduct);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }
  #[Route('/customproduct/{id}', name: 'customproduct_edit', methods: ['PUT'])]
public function editcustomprodcut(Request $request, $id): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();
    $customproduct = $entityManager->getRepository(customproduct::class)->find($id);
    if (!$customproduct) {
        return new JsonResponse(['status' => 'Faild']);;
    }

    $product = $customproduct->getProduct();
    $product->setName($request->request->get('name'));
    $product->setDescription($request->request->get('description'));
    $product->setDimensions($request->request->get('dimensions'));
    $product->setWeight($request->request->get('weight'));
    $product->setImage($request->request->get('image'));
    $product->setMaterial($request->request->get('material'));
    $idCategorie = $request->request->getInt('categoryId');
    $categorie = $entityManager->getRepository(Categories::class)->find($idCategorie);
    $product->setCategory($categorie);
    $idClient = $request->request->getInt('client');
    $client = $entityManager->getRepository(User::class)->find($idClient);
    $customproduct->setClient($client);
    $entityManager->persist($product);
    $entityManager->flush();

    $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
    return $response;
}


    #[Route('/customproduct/{id}', name: 'customproduct_delete', methods: ['DELETE'])]
    public function deletecustomproduct(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customproduct = $entityManager->getRepository(customproduct::class)->find($id);

        if (!$customproduct) {
            throw $this->createNotFoundException('The customproduct does not exist');
        }

        $entityManager->remove($customproduct);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
