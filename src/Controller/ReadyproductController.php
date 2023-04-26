<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Readyproduct;
use App\Entity\Product;
use App\Entity\User;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
        
        // Fetch categories from the database
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, // Add categories to the view
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

        // Fetch categories from the database
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, // Add categories to the view
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
            ->innerJoin('p.category', 'c');

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
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            9
        );

        // Fetch categories from the database
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'categories' => $categories, // Add categories to the view
        ]);
    }

    #[Route('/rp', name: 'app_readyproduct_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
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

        // Fetch categories from the database
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $pagination,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'order' => $order,
            'categories' => $categories, // Add categories to the view
        ]);
    }

    #[Route('/client', name: 'app_readyproduct_client_index', methods: ['GET'])]
    public function index_client(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p');


        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        return $this->render('readyproduct/client.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'order' => $order,
        ]);
    }

    #[Route('/', name: 'app_readyproduct_no_user_index', methods: ['GET'])]
    public function index_no_user(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p');


        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }

        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $readyproducts = $queryBuilder->getQuery()->getResult();

        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        return $this->render('readyproduct/no_user.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'searchTerm' => $searchTerm,
            'order' => $order,
        ]);
    }

    #[Route('/admin', name: 'app_readyproduct_admin', methods: ['GET'])]
    public function indexadmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');

        $queryBuilder = $entityManager
            ->getRepository(Readyproduct::class)
            ->createQueryBuilder('r')
            ->innerJoin('r.productId', 'p');


        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
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

        return $this->render('readyproduct/admin.html.twig', [
            'readyproducts' => $readyproducts,
            'productreviews' => $productreviews,
            'categories' => $categories,
            'searchTerm' => $searchTerm,
            'order' => $order,
        ]);
    }

    #[Route('/new', name: 'app_readyproduct_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $readyproduct = new Readyproduct();
        $product = new Product();
        $product->setImage('imagec.png');
        $readyproduct->setProductId($product);
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('productId')->getData();
            $readyproduct->setPrice($form->get('price')->getData());
            $readyproduct->setProductId($product);

            // Set the user ID on the readyproduct object
            $readyproduct->setUserId($this->connectedUser);

            // Get the user ID of the currently logged-in user
            $userId = $this->connectedUser->getUserId();

            $imageFile = $form->get('productId')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $product->setImage($newFilename);
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


    #[Route('/{readyProductId}', name: 'app_readyproduct_show', methods: ['GET'])]
    public function show(Readyproduct $readyproduct): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyproduct]);

        // Calculate the average rating
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

    #[Route('/nu/{readyProductId}', name: 'app_readyproduct_no_user_show', methods: ['GET'])]
    public function no_user_show(Readyproduct $readyproduct): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyproduct]);

        // Calculate the average rating
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

            try {
                $imageFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // handle exception if something happens during file upload
            }

            $product->setImage($newFilename);
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

    #[Route('/{readyProductId}', name: 'app_readyproduct_delete', methods: ['POST'])]
    public function delete(Request $request, Readyproduct $readyproduct, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $readyproduct->getReadyProductId(), $request->request->get('_token'))) {
            $entityManager->remove($readyproduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_readyproduct_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reviews/{readyProductId}', name: 'app_review_index', methods: ['GET'])]
    public function showReview(int $readyProductId): Response
    {
        $productreviews = $this->managerRegistry->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $readyProductId]);

        // Calculate the average rating
        $totalRating = 0;
        $count = count($productreviews);
        foreach ($productreviews as $productreview) {
            $totalRating += $productreview->getRating();
        }
        $averageRating = $count > 0 ? $totalRating / $count : 0;

        return $this->render('productreview/show_reviews.html.twig', [
            'productreviews' => $productreviews,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/productreview/new', name: 'app_productreview_new', methods: ['GET', 'POST'])]
    public function newReview(Request $request, EntityManagerInterface $entityManager): Response
    {
        $productreview = new Productreview();
        $form = $this->createForm(ProductreviewType::class, $productreview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productreview);
            $entityManager->flush();

            return $this->redirectToRoute('app_productreview_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('productreview/new.html.twig', [
            'productreview' => $productreview,
            'form' => $form,
        ]);
    }
}
