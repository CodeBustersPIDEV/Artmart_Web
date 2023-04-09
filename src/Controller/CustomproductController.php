<?php

namespace App\Controller;

use App\Entity\Customproduct;
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

use App\Entity\User;


#[Route('/customproduct')]
class CustomproductController extends AbstractController
{
    #[Route('/', name: 'app_customproduct_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
        
        $queryBuilder = $entityManager
            ->getRepository(Customproduct::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.product', 'p');
            
        
        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }
        
        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            6  );
        
        $customproducts = $queryBuilder->getQuery()->getResult();
    
        return $this->render('customproduct/index.html.twig', [
            'customproducts' => $pagination,
            'searchTerm' => $searchTerm,
            'order' => $order,
        ]);
        
    }


    #[Route('/admin', name: 'app_customproduct_admin', methods: ['GET'])]
    public function adminindex(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('q');
        $order = $request->query->get('order');
        $categories = $entityManager
        ->getRepository(Categories::class)
        ->findAll();
        $applies = $entityManager
        ->getRepository(Apply::class)
        ->findAll();
        $queryBuilder = $entityManager
            ->getRepository(Customproduct::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.product', 'p');
            
        
        if ($order === 'name') {
            $queryBuilder->orderBy('p.name', 'ASC');
        } elseif ($order === 'weight') {
            $queryBuilder->orderBy('p.weight', 'ASC');
        }
        
        if ($searchTerm) {
            $queryBuilder->where('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        
        $customproducts = $queryBuilder->getQuery()->getResult();
    
        return $this->render('customproduct/admin.html.twig', [
            'customproducts' => $customproducts,
            'searchTerm' => $searchTerm,
            'order' => $order,
            'categories' => $categories,
            'applies' => $applies,
        ]);
        
    }





    #[Route('/customproduct', name: 'app_customproduct_artist', methods: ['GET'])]
    public function artist(EntityManagerInterface $entityManager): Response
    {
        $customproducts = $entityManager
            ->getRepository(Customproduct::class)
            ->findAll();
    
        return $this->render('customproduct/artist.html.twig', [
            'customproducts' => $customproducts,
        ]);
    }

    #[Route('/new', name: 'app_customproduct_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customproduct = new Customproduct();
        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('product')->getData();
            $customproduct->setProduct($product);
            $customproduct->setClient($form->get('client')->getData());
            
            
            $imageFile = $form->get('product')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
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
            $entityManager->persist($customproduct);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('customproduct\new.html.twig', [
            'customproduct' => $customproduct,
            'form' => $form,
        ]);
    }
    


    #[Route('/newad', name: 'app_customproduct_newadmin', methods: ['GET', 'POST'])]
    public function newadmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customproduct = new Customproduct();
        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('product')->getData();
            $customproduct->setProduct($product);
            $customproduct->setClient($form->get('client')->getData());
            
            
            $imageFile = $form->get('product')->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
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
            $entityManager->persist($customproduct);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('customproduct\newadmin.html.twig', [
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


    #[Route('/{customProductId}/edit', name: 'app_customproduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {
   
        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);
        $product = $form->get('product')->getData();
        $imageFile = $form->get('product')->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
      
        if ($this->isCsrfTokenValid('delete'.$customproduct->getCustomProductId(), $request->request->get('_token'))) {
            $entityManager->remove($customproduct);
            $entityManager->flush();
   
        }

        return $this->redirectToRoute('app_customproduct_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{customProductId}/delete', name: 'app_customproduct_deleteadmin', methods: ['POST'])]
    public function deleteadmin(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {
      
        if ($this->isCsrfTokenValid('delete'.$customproduct->getCustomProductId(), $request->request->get('_token'))) {
            $entityManager->remove($customproduct);
            $entityManager->flush();
   
        }

        return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/customproduct/{customProductId}/apply', name: 'app_customproduct_apply', methods: ['GET', 'POST'])]
    public function apply(int $customProductId, RouterInterface $router): Response

    {
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
        $apply->setArtist($entityManager->getRepository(User::class)->find(1));
        $apply->setCustomproduct($customProduct);
    
        $entityManager->persist($apply);
        $entityManager->flush();
    
        $this->addFlash('success', 'Applied successfully.');
    
        // Redirect to the filtered list of applies with status 'pending', 'done', or 'refused'
        return $this->redirectToRoute('app_apply_pending');
    }
    #[Route('/sum', name: 'app_customproduct_sum', methods: ['GET'])]
public function sum(EntityManagerInterface $entityManager): Response
{
    $customproducts = $entityManager
        ->getRepository(Customproduct::class)
        ->findAll();
    
    $total = 0;
    foreach ($customproducts as $customproduct) {
        $total += $customproduct->getPrice();
    }
    
    return $this->render('customproduct/sum.html.twig', [
        'total' => $total,
    ]);
}

}
