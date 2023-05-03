<?php

namespace App\Controller;
use ReCaptcha\ReCaptcha;
use App\Entity\Customproduct;
use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Twilio\Rest\Client;
use App\Entity\Categories;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

use App\Form\CustomproductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Apply;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Spatie\Emoji\Emoji;
use App\Repository\UserRepository;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Laminas\Diactoros\Response as DiactorosResponse;

#[Route('/customproduct')]
class CustomproductController extends AbstractController
{
   
    private User $connectedUser; 
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
                
                // Debugging code
                if (!$this->connectedUser instanceof User) {
                    throw new \Exception('Connected user is not a User object');
                }
            }
        }
    }
    #[Route('chat/', name: 'chat', methods: ['GET'])]
    public function chat(): Response
    {
        $customproduct = new Customproduct(); 
    
        return $this->render('customproduct/chat.html.twig', [
            'customproduct' => $customproduct,
        ]);
    }
    #[Route('/customproduct/searchCustomProduct', name: 'app_customproduct_admin_search', methods: ['GET'])]
    public function searchCustomProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $searchValue = $request->query->get('searchValue');
        
        $queryBuilder = $entityManager
            ->getRepository(Customproduct::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.product', 'p')
            ->where('p.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchValue . '%');
        
        $customProducts = $queryBuilder->getQuery()->getResult();
        
        $result = [];
        foreach ($customProducts as $customProduct) {
            $result[] = [
                'customProductId' => $customProduct->getCustomProductId(),
                'clientId' => $customProduct->getClient()->getUserId(),
                'clientName' => $customProduct->getClient()->getFullName(),
                'productName' => $customProduct->getProduct()->getName(),
                'productWeight' => $customProduct->getProduct()->getWeight(),
            ];
        }
        
        return new JsonResponse($result);
    }
    
    
    #[Route('/', name: 'app_customproduct_index', methods: ['GET'])]
    public function index( UserRepository $userRepository,FlashyNotifier $flashy,Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
    
        $queryBuilder = $entityManager
            ->getRepository(Customproduct::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.product', 'p')
            ->andWhere('c.client = :userId') // Filter by connected user's ID
            ->setParameter('userId', $this->connectedUser->getUserId());

         
            $order = $request->query->get('order', 'name');
            $direction = $request->query->get('direction', 'asc');
            
            // Toggle the direction when the order is clicked
            if ($order === $request->get('order')) {
                $direction = ($direction === 'asc') ? 'desc' : 'asc';
            }
            
            if ($order === 'name') {
                $queryBuilder->orderBy('p.name', $direction);
            } elseif ($order === 'weight') {
                $queryBuilder->orderBy('p.weight', $direction);
            }
            
    
        if ($searchTerm) {
            $criteria = $request->query->get('criteria');
            if ($criteria === 'name') {
                $queryBuilder->andWhere('p.name LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');
            } elseif ($criteria === 'weight') {
                $queryBuilder->andWhere('p.weight LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');
            } elseif ($criteria === 'material') {
                $queryBuilder->andWhere('p.material LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
            }
        }
        $flashy->info('Welcome To Your Custom Products List');
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            8
        );
   
        return $this->render('customproduct/index.html.twig', [
            'customproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'order' => $order,
        ]);
   
    }
    
 

    #[Route('/admin', name: 'app_customproduct_admin', methods: ['GET'])]
    public function adminindex(PaginatorInterface $paginator,FlashyNotifier $flashy,Request $request, EntityManagerInterface $entityManager): Response
    {
        $flashy->info('Welcome to Custom Products Admin Panel');
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->createQueryBuilder('e');

            $pagination = $paginator->paginate(
                $categories->getQuery(),
                $request->query->getInt('page', 1),
                4
            );

        $applies = $entityManager
            ->getRepository(Apply::class)
            ->findAll();
        $queryBuilder = $entityManager
            ->getRepository(Customproduct::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.product', 'p');


            $order = $request->query->get('order', 'name');
            $direction = $request->query->get('direction', 'asc');
            
            // Toggle the direction when the order is clicked
            if ($order === $request->get('order')) {
                $direction = ($direction === 'asc') ? 'desc' : 'asc';
            }
            
            if ($order === 'name') {
                $queryBuilder->orderBy('p.name', $direction);
            } elseif ($order === 'weight') {
                $queryBuilder->orderBy('p.weight', $direction);
            }
            
            

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $customproducts = $queryBuilder->getQuery()->getResult();

      
        $sumQueryBuilder = $entityManager
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
    

        return $this->render('customproduct/admin.html.twig', [
            'customproducts' => $customproducts,
            'searchTerm' => $searchTerm,
            'order' => $order,
            'categories' => $pagination,
            'applies' => $applies,
            'weightData' => $weightData,
        ]);
    }


    #[Route('/stat', name: 'app_customproduct_stat', methods: ['GET'])]
    public function statindex(Request $request,EntityManagerInterface $entityManager): Response
    {
     
        $sumQueryBuilder = $entityManager
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
    
        return $this->render('customproduct/stat.html.twig', [
            'weightData' => $weightData,
            'weightSums' => $weightSums,
        ]);
      
    }
    



    #[Route('/customproduct', name: 'app_customproduct_artist', methods: ['GET'])]
    public function artist(FlashyNotifier $flashy,Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
      
        $searchTerm = $request->query->get('q');
        $queryBuilder = $entityManager
            ->getRepository(Customproduct::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.product', 'p');



        $customproducts = $entityManager
            ->getRepository(Customproduct::class)
            ->findAll();


            if ($searchTerm) {
                $criteria = $request->query->get('criteria');
                if ($criteria === 'name') {
                    $queryBuilder->andWhere('p.name LIKE :searchTerm')
                        ->setParameter('searchTerm', '%' . $searchTerm . '%');
                } elseif ($criteria === 'weight') {
                    $queryBuilder->andWhere('p.weight LIKE :searchTerm')
                        ->setParameter('searchTerm', '%' . $searchTerm . '%');
                } elseif ($criteria === 'material') {
                    $queryBuilder->andWhere('p.material LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');
                }
            }
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            8
        );
        $flashy->info('Welcome to Custom Products Application List');
        return $this->render('customproduct/artist.html.twig', [
            'searchTerm' => $searchTerm,
            'customproducts' => $pagination,
        ]);
    }


    #[Route('/newad', name: 'app_customproduct_newadmin', methods: ['GET', 'POST'])]
    public function newadmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customproduct = new Customproduct();
        $product = new Product();
        $product->setImage('http://localhost/PIDEV/BlogUploads/imagec.png');
        $customproduct->setClient($this->connectedUser);
        $customproduct->setProduct($product);
        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('product')->getData();
            $customproduct->setProduct($product);
        

            $imageFile = $form->get('product')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                $imageURL = $this->getParameter('file_base_url')['host'] . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
                $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
            
                try {
                    $imageFile->move(
                        $this->getParameter('destinationPath'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }
            
                $product->setImage($imageURL);
            }

            $entityManager->persist($product);
            $entityManager->persist($customproduct);
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customproduct\newadmin.html.twig', [
            'customproduct' => $customproduct,
            'form' => $form,
        ]);
    }



    #[Route('/newnew', name: 'app_customproduct_newnew', methods: ['GET', 'POST'])]
    public function newnew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customproduct = new Customproduct();
        $product = new Product();
        $product->setImage('http://localhost/PIDEV/BlogUploads/imagec.png');
        $customproduct->setClient($this->connectedUser);
        $customproduct->setProduct($product);
        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('product')->getData();
            $customproduct->setProduct($product);
      
            $imageFile = $form->get('product')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                $imageURL = $this->getParameter('file_base_url')['host'] . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
                $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
            
                try {
                    $imageFile->move(
                        $this->getParameter('destinationPath'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }
            
                $product->setImage($imageURL);
            }
            
            
            $entityManager->persist($product);
            $entityManager->persist($customproduct);
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customproduct\new.html.twig', [
            'customproduct' => $customproduct,
            'form' => $form,
        ]);
    }
    

    #[Route('/{customProductId}', name: 'app_customproduct_show', methods: ['GET'])]
    public function show(Customproduct $customproduct): Response
    {
        $product = $customproduct->getProduct();

        return $this->render('customproduct/show.html.twig', [
            'customproduct' => $customproduct,
            'product' => $product,
        ]);
    }
    #[Route('draw/', name: 'draw', methods: ['GET'])]
    public function draw(): Response
    {
        $customproduct = new Customproduct(); 
    
        return $this->render('customproduct/draw.html.twig', [
            'customproduct' => $customproduct,
        ]);
    }
  
    #[Route('s/{customProductId}', name: 'app_customproduct_showartist', methods: ['GET'])]
    public function showartist(Customproduct $customproduct): Response
    {
        $product = $customproduct->getProduct();

        return $this->render('customproduct/showartist.html.twig', [
            'customproduct' => $customproduct,
            'product' => $product,
        ]);
    }




    #[Route('/{customProductId}/edit', name: 'app_customproduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);
        $product = $form->get('product')->getData();
        $imageFile = $form->get('product')->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
            $imageURL = $this->getParameter('file_base_url')['host'] . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
            $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
        
            try {
                $imageFile->move(
                    $this->getParameter('destinationPath'),
                    $newFilename
                );
            } catch (FileException $e) {
                // handle exception if something happens during file upload
            }
        
            $product->setImage($imageURL);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customproduct/edit.html.twig', [
            'customproduct' => $customproduct,
            'form' => $form,
        ]);
    }


    #[Route('/{customProductId}/editadmin', name: 'app_customproduct_editadmin', methods: ['GET', 'POST'])]
    public function editadmin(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);
        $product = $form->get('product')->getData();
        $imageFile = $form->get('product')->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
            $imageURL = $this->getParameter('file_base_url')['host'] . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
            $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;
        
            try {
                $imageFile->move(
                    $this->getParameter('destinationPath'),
                    $newFilename
                );
            } catch (FileException $e) {
                // handle exception if something happens during file upload
            }
        
            $product->setImage($imageURL);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customproduct/editadmin.html.twig', [
            'customproduct' => $customproduct,
            'form' => $form,
        ]);
    }




    #[Route('/{customProductId}', name: 'app_customproduct_delete', methods: ['POST'])]
    public function delete(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $customproduct->getCustomProductId(), $request->request->get('_token'))) {
            $entityManager->remove($customproduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customproduct_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{customProductId}/delete', name: 'app_customproduct_deleteadmin', methods: ['POST'])]
    public function deleteadmin(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $customproduct->getCustomProductId(), $request->request->get('_token'))) {
            $entityManager->remove($customproduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/customproduct/{customProductId}/apply', name: 'app_customproduct_apply', methods: ['GET', 'POST'])]
    public function apply(FlashyNotifier $flashy,int $customProductId, RouterInterface $router): Response

    {
        $flashy->success('application sent successfully');
        $entityManager = $this->getDoctrine()->getManager();

        $customProduct = $entityManager->getRepository(Customproduct::class)->find($customProductId);

        if (!$customProduct) {
            throw $this->createNotFoundException('Unable to find Customproduct entity.');
        }

        // Check if a previous apply exists for this custom product
        $existingApply = $entityManager->getRepository(Apply::class)->findOneBy(['customproduct' => $customProduct]);

        if ($existingApply) {
            $this->addFlash('warning', 'An apply already exists for this custom product.');
            return $this->redirectToRoute('app_apply_pending');
        }

        $apply = new Apply();
        $apply->setStatus('pending');
        $apply->setArtist($this->connectedUser);
        $apply->setCustomproduct($customProduct);

        $entityManager->persist($apply);
        $entityManager->flush();

        $sid    = "AC85fdc289caf6aa747109220798d39394";
        $token  = "8acba1bd4bfc10782d6dccac2023e541";
        $twilio = new Client($sid, $token);
    
        $message = $twilio->messages
          ->create("whatsapp:+21698238240", 
            array(
              "from" => "whatsapp:+14155238886",
              "body" => "you have a Custom Product apply"
            )
            );
          

        // Redirect to the filtered list of applies with status 'pending', 'done', or 'refused'
        return $this->redirectToRoute('app_apply_pending');
    }
 
 
    
}