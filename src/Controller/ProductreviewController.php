<?php

namespace App\Controller;

use App\Entity\Productreview;
use App\Form\ProductreviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/productreview')]
class ProductreviewController extends AbstractController
{
    #[Route('/', name: 'app_productreview_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findAll();

        return $this->render('productreview/index.html.twig', [
            'productreviews' => $productreviews,
        ]);
    }

    #[Route('/new', name: 'app_productreview_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
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

    #[Route('/{reviewId}', name: 'app_productreview_show', methods: ['GET'])]
    public function show(Productreview $productreview): Response
    {
        return $this->render('productreview/show.html.twig', [
            'productreview' => $productreview,
        ]);
    }

    #[Route('/{reviewId}/edit', name: 'app_productreview_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Productreview $productreview, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductreviewType::class, $productreview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_productreview_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('productreview/edit.html.twig', [
            'productreview' => $productreview,
            'form' => $form,
        ]);
    }

    #[Route('/{reviewId}', name: 'app_productreview_delete', methods: ['POST'])]
    public function delete(Request $request, Productreview $productreview, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productreview->getReviewId(), $request->request->get('_token'))) {
            $entityManager->remove($productreview);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_productreview_index', [], Response::HTTP_SEE_OTHER);
    }
}
