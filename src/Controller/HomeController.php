<?php

namespace App\Controller;

use App\Entity\Blogs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customproduct;
use App\Entity\Event;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\Readyproduct;
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
    { 
        $queryBuilder = $entityManager
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
    public function home(EntityManagerInterface $entityManager): Response
    { $sumQueryBuilder = $entityManager
        ->createQueryBuilder()
        ->select('p.material as material, SUM(p.weight) as weight_sum')
        ->from(Product::class, 'p')
        ->innerJoin(CustomProduct::class, 'cp', 'WITH', 'p.productId = cp.product')
        ->groupBy('p.material');
    
        $weightSums = $sumQueryBuilder->getQuery()->getResult();
    
        // Transform the results into a format that Chart.js can use
        $weightData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Product Weights',
                    'data' => [],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    
        foreach ($weightSums as $weightSum) {
            $weightData['labels'][] = $weightSum['material'];
            $weightData['datasets'][0]['data'][] = $weightSum['weight_sum'];
        }


        $sumsQueryBuilder = $entityManager
        ->createQueryBuilder()
        ->select('p.material as material, SUM(p.weight) as weight_sum')
        ->from(Product::class, 'p')
        ->innerJoin(ReadyProduct::class, 'cp', 'WITH', 'p.productId = cp.productId')
        ->groupBy('p.material');
    
        $weightSumsss = $sumsQueryBuilder->getQuery()->getResult();
    
        // Transform the results into a format that Chart.js can use
        $weightDataq = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Product Weights',
                    'data' => [],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    
        foreach ($weightSumsss as $weightSum) {
            $weightDataq['labels'][] = $weightSum['material'];
            $weightDataq['datasets'][0]['data'][] = $weightSum['weight_sum'];
        }




        $x = $entityManager
        ->createQueryBuilder()
        ->select('p.shippingaddress as material, SUM(p.totalcost) as weight_sum')
        ->from(Order::class, 'p')
        ->groupBy('p.shippingaddress');
        
    $ShippingAddressSums = $x->getQuery()->getResult();
        
    // Transform the results into a format that Chart.js can use
    $weightDatas = [    'labels' => [],
    'datasets' => [
        [
            'label' => 'Orders',
            'data' => [],
            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
            'borderColor' => 'rgba(54, 162, 235, 1)',
            'borderWidth' => 1
        ]
    ]
    ];
        
    foreach ($ShippingAddressSums as $weightSum) {
        $weightDatas['labels'][] = $weightSum['material'];
        $weightDatas['datasets'][0]['data'][] = $weightSum['weight_sum'];
    }






    $y = $entityManager
    ->createQueryBuilder()
    ->select('p.type as material, SUM(p.entryfee) as weight_sum')
    ->from(Event::class, 'p')
    ->groupBy('p.type');
    
$typesums = $y->getQuery()->getResult();
    

$weightDatass = [    'labels' => [],
'datasets' => [
    [
        'label' => 'Events',
        'data' => [],
        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
        'borderColor' => 'rgba(54, 162, 235, 1)',
        'borderWidth' => 1
    ]
]
];
    
foreach ($typesums as $weightSum) {
    $weightDatass['labels'][] = $weightSum['material'];
    $weightDatass['datasets'][0]['data'][] = $weightSum['weight_sum'];
}
    




$b = $entityManager
->createQueryBuilder()
->select('p.title as material, SUM(p.nbViews) as weight_sum')
->from(Blogs::class, 'p')
->groupBy('p.title');

$blogsum = $b->getQuery()->getResult();

// Transform the results into a format that Chart.js can use
$weightDatasb = [    'labels' => [],
'datasets' => [
[
    'label' => 'Orders',
    'data' => [],
    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
    'borderColor' => 'rgba(54, 162, 235, 1)',
    'borderWidth' => 1
]
]
];

foreach ($blogsum as $weightSum) {
$weightDatasb['labels'][] = $weightSum['material'];
$weightDatasb['datasets'][0]['data'][] = $weightSum['weight_sum'];
}


$u = $entityManager
->createQueryBuilder()
->select('p.role as material, count(p.role) as weight_sum')
->from(User::class, 'p')
->groupBy('p.role');

$usersums = $u->getQuery()->getResult();


$userDatas = [    'labels' => [],
'datasets' => [
[
    'label' => 'User',
    'data' => [],
    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
    'borderColor' => 'rgba(54, 162, 235, 1)',
    'borderWidth' => 1
]
]
];

foreach ($usersums as $weightSum) {
$userDatas['labels'][] = $weightSum['material'];
$userDatas['datasets'][0]['data'][] = $weightSum['weight_sum'];
}

        if ($this->connectedUser->getRole() === "admin") {
            return $this->render('home/admin.html.twig', [
                'controller_name' => 'HomeController',
                'rx' => $weightDataq,
                'ry' => $weightSumsss,
                'weightData' => $weightData,
                'weightSums' => $weightSums,
                'x' => $weightDatas,
                'y' => $ShippingAddressSums,
                'blogx' => $weightDatasb,
                'blogy' => $blogsum,
                'eventx' => $weightDatass,
                'eventy' => $typesums,
                'userx' => $userDatas,
                'usery' => $usersums,
            ]);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
}

