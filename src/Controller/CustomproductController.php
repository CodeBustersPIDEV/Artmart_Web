<?php

namespace App\Controller;

use App\Entity\Customproduct;
use App\Entity\Product;
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
    public function index(EntityManagerInterface $entityManager): Response
    {
        $customproducts = $entityManager
            ->getRepository(Customproduct::class)
            ->findAll();

        return $this->render('customproduct/index.html.twig', [
            'customproducts' => $customproducts,
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


    #[Route('/{customProductId}/edit', name: 'app_customproduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customproduct $customproduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomproductType::class, $customproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customproduct/edit.html.twig', [
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
    
}
