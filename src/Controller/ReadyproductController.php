<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Readyproduct;
use App\Entity\Product;
use App\Entity\Productreview;

use App\Form\ReadyproductType;
use App\Form\ProductreviewType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/readyproduct')]
class ReadyproductController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }


    #[Route('/', name: 'app_readyproduct_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
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

        return $this->render('readyproduct/index.html.twig', [
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
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = new Product();
            $product->setName($form->get('productId')->get('name')->getData());
            $product->setDescription($form->get('productId')->get('description')->getData());

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

            $entityManager->persist($product);
            $readyproduct->setProductId($product);
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
