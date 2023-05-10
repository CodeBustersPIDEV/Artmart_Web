<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Readyproduct;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ReadyproductapiController extends AbstractController
{
    #[Route('/readyproduct', name: 'app_readyproductapi')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $readyprodcut = $entityManager
            ->getRepository(Readyproduct::class)
            ->findAll();
        $responseArray = array();
        foreach ($readyprodcut as $readyprodcut) {
            $responseArray[] = array(
                'readyProductId' => $readyprodcut->getreadyProductId(),
                'product' => $readyprodcut->getproductId()->getName(),
                'product1' => $readyprodcut->getproductId()->getDescription(),
                'product2' => $readyprodcut->getproductId()->getDimensions(),
                'product3' => $readyprodcut->getproductId()->getWeight(),
                'product4' => $readyprodcut->getproductId()->getMaterial(),
                'product5' => $readyprodcut->getproductId()->getImage(),
                'user' => $readyprodcut->getUserId(),
                'product6' => $readyprodcut->getPrice(),
                'product7' => $readyprodcut->getproductId()->getCategoryId()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/readyproduct/add', name: 'readyproduct', methods: ['GET', 'POST'])]
    public function addreadyproduct(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $readyproduct = new readyproduct();
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
        $idUser = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($idUser);
        $readyproduct->setPrice($request->request->get('price'));
        $entityManager->persist($product);
        $readyproduct->setProductId($product);
        $readyproduct->setUserId($user);

        $entityManager->persist($readyproduct);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/readyproduct/{id}', name: 'readyproduct_edit', methods: ['PUT'])]
    public function editreadyprodcut(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $readyproduct = $entityManager->getRepository(readyproduct::class)->find($id);
        if (!$readyproduct) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $product = $readyproduct->getProduct();
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setDimensions($request->request->get('dimensions'));
        $product->setWeight($request->request->get('weight'));
        $product->setImage($request->request->get('image'));
        $product->setMaterial($request->request->get('material'));
        $idCategorie = $request->request->getInt('categoryId');
        $categorie = $entityManager->getRepository(Categories::class)->find($idCategorie);
        $product->setCategory($categorie);
        $idUser = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($idUser);
        $readyproduct->setClient($user);
        $readyproduct->setPrice($request->request->get('price'));
        $entityManager->persist($product);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/readyproduct/{id}', name: 'readyproduct_delete', methods: ['DELETE'])]
    public function deletereadyproduct(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $readyproduct = $entityManager->getRepository(readyproduct::class)->find($id);

        if (!$readyproduct) {
            throw $this->createNotFoundException('The ready product does not exist');
        }

        $entityManager->remove($readyproduct);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }
}
