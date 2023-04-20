<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customproduct;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{

    private User $connectedUser;


    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }

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
        if ($this->connectedUser->getRole() === "admin") {
            return $this->render('home/admin.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
}
