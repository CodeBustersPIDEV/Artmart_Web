<?php

namespace App\Controller;

use App\Entity\Readyproduct;
use App\Form\ReadyproductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/readyproduct')]
class ReadyproductController extends AbstractController
{
    #[Route('/', name: 'app_readyproduct_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $readyproducts = $entityManager
            ->getRepository(Readyproduct::class)
            ->findAll();

        return $this->render('readyproduct/index.html.twig', [
            'readyproducts' => $readyproducts,
        ]);
    }

    #[Route('/new', name: 'app_readyproduct_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $readyproduct = new Readyproduct();
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        return $this->render('readyproduct/show.html.twig', [
            'readyproduct' => $readyproduct,
        ]);
    }

    #[Route('/{readyProductId}/edit', name: 'app_readyproduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Readyproduct $readyproduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReadyproductType::class, $readyproduct);
        $form->handleRequest($request);

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
        if ($this->isCsrfTokenValid('delete'.$readyproduct->getReadyProductId(), $request->request->get('_token'))) {
            $entityManager->remove($readyproduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_readyproduct_index', [], Response::HTTP_SEE_OTHER);
    }
}
