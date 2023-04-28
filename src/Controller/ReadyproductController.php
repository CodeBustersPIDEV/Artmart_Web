<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Readyproduct;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Productreview;
use App\Form\ReadyproductType;
use App\Form\ProductreviewType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail as MimeTemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/readyproduct')]
class ReadyproductController extends AbstractController
{
    private $managerRegistry;
    private User $connectedUser;

    public function __construct(ManagerRegistry $managerRegistry, SessionInterface $session, UserRepository $userRepository)
    {
        $this->managerRegistry = $managerRegistry;
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }

    #[Route('/AscPrice', name: 'app_readyproduct__asc_price_index', methods: ['GET'])]
    public function orderByPriceAsc(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->andWhere('r.userId = :userId') 
            ->setParameter('userId', $this->connectedUser->getUserId())
            ->orderBy('r.price', 'ASC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/DescPrice', name: 'app_readyproduct__desc_price_index', methods: ['GET'])]
    public function orderByPriceDesc(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->andWhere('r.userId = :userId')
            ->setParameter('userId', $this->connectedUser->getUserId())
            ->orderBy('r.price', 'DESC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories,
        ]);
    }

    #[Route('/DescCat', name: 'app_readyproduct__desc_cat_index', methods: ['GET'])]
    public function orderByCategoryDesc(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->andWhere('r.userId = :userId')
            ->orderBy('p.category', 'DESC');

        if ($searchTerm) {
            $queryBuilder->andWhere('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->andWhere('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category)
                ->setParameter('userId', $this->connectedUser->getUserId());
        } else {
            $queryBuilder->setParameter('userId', $this->connectedUser->getUserId());
        }

        $queryBuilder->setParameter('userId', $this->connectedUser->getUserId());

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/AscPriceClient', name: 'app_readyproduct__asc_price_index_client', methods: ['GET'])]
    public function orderByPriceAsc_client(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('r.price', 'ASC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

    
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/client.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/DescPriceClient', name: 'app_readyproduct__desc_price_index_client', methods: ['GET'])]
    public function orderByPriceDesc_client(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('r.price', 'DESC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/client.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/client/DescCat', name: 'app_readyproduct__desc_cat_index_client', methods: ['GET'])]
    public function orderByCategoryDescClient(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('p.category', 'DESC');

        if ($searchTerm) {
            $queryBuilder->andWhere('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->andWhere('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/client.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/AscPriceNoUser', name: 'app_readyproduct__asc_price_index_no_user', methods: ['GET'])]
    public function orderByPriceAsc_no_user(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('r.price', 'ASC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/no_user.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories,
        ]);
    }

    #[Route('/DescPriceNoUser', name: 'app_readyproduct__desc_price_index_no_user', methods: ['GET'])]
    public function orderByPriceDesc_no_user(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('r.price', 'DESC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/no_user.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories,
        ]);
    }#[Route('/DescCatNoUser', name: 'app_readyproduct__desc_cat_index_no_user', methods: ['GET'])]
    public function orderByCategoryDescNoUser(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('p.category', 'DESC');

        if ($searchTerm) {
            $queryBuilder->andWhere('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->andWhere('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/client.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/AscPriceAdmin', name: 'app_readyproduct__asc_price_index_admin', methods: ['GET'])]
    public function orderByPriceAsc_admin(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->andWhere('r.userId = :userId') 
            ->setParameter('userId', $this->connectedUser->getUserId())
            ->orderBy('r.price', 'ASC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/admin.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/DescPriceAdmin', name: 'app_readyproduct__desc_price_index_admin', methods: ['GET'])]
    public function orderByPriceDesc_admin(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->andWhere('r.userId = :userId') 
            ->setParameter('userId', $this->connectedUser->getUserId())
            ->orderBy('r.price', 'DESC');

        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $readyproducts,
            $request->query->getInt('page', 1),
            9
        );

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/admin.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/DescCatAdmin', name: 'app_readyproduct__desc_cat_index_admin', methods: ['GET'])]
    public function orderByCategoryDesc_admin(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->orderBy('p.category', 'DESC');

        if ($searchTerm) {
            $queryBuilder->andWhere('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->andWhere('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            9
        );

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/admin.html.twig', [
            'readyproducts' => $pagination,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'categories' => $categories, 
        ]);
    }

    #[Route('/admin', name: 'app_readyproduct_admin', methods: ['GET'])]
    public function indexadmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('s');
        $order = $request->query->get('order');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c');


        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/admin.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'categories' => $categories,
            'searchTerm' => $searchTerm,
            'order' => $order,
        ]);
    }

    #[Route('/artist', name: 'app_readyproduct_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c')
            ->andWhere('r.userId = :userId') 
            ->setParameter('userId', $this->connectedUser->getUserId());

        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->where('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'order' => $order,
            'categories' => $categories, 
        ]);
    }

    #[Route('/client', name: 'app_readyproduct_client_index', methods: ['GET'])]
    public function index_client(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p')
            ->innerJoin('p.category', 'c');


        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }

        if ($searchTerm) {
            $queryBuilder->andWhere('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($category) {
            $queryBuilder->andWhere('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            9
        );

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/client.html.twig', [
            'readyproducts' => $pagination,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'order' => $order,
            'categories' => $categories, 
        ]);
    }

    #[Route('/', name: 'app_readyproduct_no_user_index', methods: ['GET'])]
    public function index_no_user(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
        $category = $request->query->get('category');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p');


        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }

        if ($category) {
            $queryBuilder->andWhere('c.categoriesId = :categoryId')
                ->setParameter('categoryId', $category);
        }
        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/no_user.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'order' => $order,
            'categories' => $categories, 
        ]);
    }



    #[Route('/new', name: 'app_readyproduct_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $readyproduct = new Readyproduct();
        $product = new Product();
        $product->setImage('http://localhost:88/PIDEV/BlogUploads/imagec.png');
        $readyproduct->setProductId($product);
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('productId')->getData();
            $readyproduct->setPrice($form->get('price')->getData());
            $readyproduct->setProductId($product);

           
            $readyproduct->setUserId($this->connectedUser);

            
            $userId = $this->connectedUser->getUserId();

            $imageFile = $form->get('productId')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                $imageURL = $this->getParameter('file_base_url')['host'] . ':88' . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
                $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;

                try {
                    $imageFile->move(
                        $this->getParameter('destinationPath'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }

                $product->setImage($imageURL);
            }

            $entityManager->persist($product);
            $entityManager->persist($readyproduct);
            $entityManager->flush();

            return $this->redirectToRoute('app_readyproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('readyproduct/new.html.twig', [
            'readyproduct' => $readyproduct,
            'form' => $form,
        ]);
    }

    #[Route('/newAdmin', name: 'app_readyproduct_new_admin', methods: ['GET', 'POST'])]
    public function new_admin(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $readyproduct = new Readyproduct();
        $product = new Product();
        $product->setImage('http://localhost:88/PIDEV/BlogUploads/imagec.png');
        $readyproduct->setProductId($product);
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('productId')->getData();
            $readyproduct->setPrice($form->get('price')->getData());
            $readyproduct->setProductId($product);

            
            $readyproduct->setUserId($this->connectedUser);

            
            $userId = $this->connectedUser->getUserId();

            $imageFile = $form->get('productId')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
                $imageURL = $this->getParameter('file_base_url')['host'] . ':88' . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
                $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;

                try {
                    $imageFile->move(
                        $this->getParameter('destinationPath'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }

                $product->setImage($imageURL);
            }

            $entityManager->persist($product);
            $entityManager->persist($readyproduct);
            $entityManager->flush();

            $id = $form->get('userId')->getData();
            
            $user = $entityManager->getRepository(User::class)->find($id);

            
            $userEmail = $user->getEmail();

            
            $email = (new MimeTemplatedEmail())
                ->from($this->connectedUser->getEmail())
                ->to($userEmail)
                ->subject('New product added to your inventory')
                ->htmlTemplate('emails/new-product.html.twig')
                ->context([
                    'username' => $form->get('userId')->getData(),
                    'readyproduct' => $readyproduct,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('app_readyproduct_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('readyproduct/new_admin.html.twig', [
            'readyproduct' => $readyproduct,
            'form' => $form,
        ]);
    }


    #[Route('/{readyProductId}', name: 'app_readyproduct_show', methods: ['GET'])]
    public function show(Readyproduct $readyproduct): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyproduct]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('readyproduct/show.html.twig', [
            'readyproduct' => $readyproduct,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/artist/{readyProductId}', name: 'app_readyproduct_artist_show', methods: ['GET'])]
    public function show_artist(Readyproduct $readyproduct): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyproduct]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('readyproduct/show_artist.html.twig', [
            'readyproduct' => $readyproduct,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/nu/{readyProductId}', name: 'app_readyproduct_no_user_show', methods: ['GET'])]
    public function no_user_show(Readyproduct $readyproduct): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyproduct]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('readyproduct/show.html.twig', [
            'readyproduct' => $readyproduct,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/admin/{readyProductId}', name: 'app_readyproduct_show_admin', methods: ['GET'])]
    public function admin_show(Readyproduct $readyproduct): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyproduct]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('readyproduct/show_admin.html.twig', [
            'readyproduct' => $readyproduct,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/{readyProductId}/edit', name: 'app_readyproduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Readyproduct $readyproduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);
        $product = $form->get('productId')->getData();
        $imageFile = $form->get('productId')->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
            $imageURL = $this->getParameter('file_base_url')['host'] . ':88' . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
            $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;

            try {
                $imageFile->move(
                    $this->getParameter('destinationPath'),
                    $newFilename
                );
            } catch (FileException $e) {
                 
            }

            $product->setImage($imageURL);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_readyproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('readyproduct/edit.html.twig', [
            'readyproduct' => $readyproduct,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{readyProductId}/edit', name: 'app_readyproduct_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, Readyproduct $readyproduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);
        $product = $form->get('productId')->getData();
        $imageFile = $form->get('productId')->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $destinationPath = $this->getParameter('destinationPath') . '/' . $newFilename;
            $imageURL = $this->getParameter('file_base_url')['host'] . ':88' . '/' . $this->getParameter('file_base_url')['path'] . '/' . $newFilename;
            $imagePath = $this->getParameter('destinationPath') . '/' . $newFilename;

            try {
                $imageFile->move(
                    $this->getParameter('destinationPath'),
                    $newFilename
                );
            } catch (FileException $e) {
                
            }

            $product->setImage($imageURL);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_readyproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('readyproduct/edit_admin.html.twig', [
            'readyproduct' => $readyproduct,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{readyProductId}', name: 'app_readyproduct_delete', methods: ['GET'])]
    public function delete(Request $request, Readyproduct $readyproduct, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($readyproduct);
        $entityManager->flush();

        return $this->redirectToRoute('app_readyproduct_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/delete/{readyProductId}', name: 'app_readyproduct_admin_delete', methods: ['GET'])]
    public function deleteAdmin(Request $request, Readyproduct $readyproduct, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($readyproduct);
        $entityManager->flush();

        return $this->redirectToRoute('app_readyproduct_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/artist/reviews/{readyProductId}', name: 'app_review_index', methods: ['GET'])]
    public function showReview(int $readyProductId): Response
    {
        $readyProduct = $this->managerRegistry->getRepository(ReadyProduct::class)->find($readyProductId);
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyProductId]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('productreview/show_reviews_artist.html.twig', [
            'readyproduct' => $readyProduct,
            'averageRating' => $averageRating,
            'productreviews' => $productreviews,
        ]);
    }

    #[Route('/admin/reviews/{readyProductId}', name: 'app_review_index_admin', methods: ['GET'])]
    public function showReviewAdmin(int $readyProductId): Response
    {
        $readyProduct = $this->managerRegistry->getRepository(ReadyProduct::class)->find($readyProductId);
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyProductId]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('readyproduct/show_review_admin.html.twig', [
            'readyproduct' => $readyProduct,
            'averageRating' => $averageRating,
            'productreviews' => $productreviews,
        ]);
    }

    #[Route('/client/reviews/{readyProductId}', name: 'app_review_index_client', methods: ['GET'])]
    public function showReviewClient(int $readyProductId): Response
    {
        $readyProduct = $this->managerRegistry->getRepository(ReadyProduct::class)->find($readyProductId);
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyProductId]);

        
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('productreview/show_reviews.html.twig', [
            'readyproduct' => $readyProduct,
            'averageRating' => $averageRating,
            'productreviews' => $productreviews,
        ]);
    }

    #[Route('/productreview/new/{id}', name: 'app_productreview_new', methods: ['GET', 'POST'])]
    public function newReview($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $productreview = new Productreview();
        $rp = $this->managerRegistry->getRepository(ReadyProduct::class)->find($id);
        $productreview->setReadyProductId($rp);
        $productreview->setUserId($this->connectedUser);
        $form = $this->createForm(ProductreviewType::class, $productreview);
        $form->handleRequest($request);
        $productreview->setDate(new \DateTime('now', new \DateTimeZone('America/New_York')));
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productreview);
            $entityManager->flush();
            return $this->redirect("/readyproduct/client/reviews/" . $id);
        }

        return $this->renderForm('productreview/new.html.twig', [
            'productreview' => $productreview,
            'form' => $form,
        ]);
    }
    private function AdminAccess()
    {
        if ($this->connectedUser->getRole() == "admin") {
            return true; 
        } else {
            return false; 
        }
    }
    private function ClientAccess()
    {
        if ($this->connectedUser->getRole() === "client") {
            return true; 
        } else {
            return false; 
        }
    }
    private function ArtistAccess()
    {
        if ($this->connectedUser->getRole() === "artist") {
            return true; 
        } else {
            return false; 
        }
    }
    private function ArtistClientAccess()
    {
        if ($this->connectedUser->getRole() == "artist" || $this->connectedUser->getRole() == "client") {
            return true; 
        } else {
            return false; 
        }
    }
}
