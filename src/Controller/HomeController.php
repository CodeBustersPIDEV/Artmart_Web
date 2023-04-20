<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Readyproduct;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    { $queryBuilder = $entityManager
        ->getRepository(Readyproduct::class)
        ->createQueryBuilder('c')
        ->innerJoin('c.productId', 'p');
        $customproducts = $queryBuilder->getQuery()->getResult();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'customproducts' => $customproducts,
        ]);
    }
    #[Route('/Panel', name: 'app_admin')]
    public function home(): Response
    {
        return $this->render('home/admin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
