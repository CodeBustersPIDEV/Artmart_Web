<?php

namespace App\Controller;

use App\Entity\Orderrefund;
use App\Form\OrderrefundType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orderrefund')]
class OrderrefundController extends AbstractController
{
    #[Route('/', name: 'app_orderrefund_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $orderrefunds = $entityManager
            ->getRepository(Orderrefund::class)
            ->findAll();

        return $this->render('orderrefund/index.html.twig', [
            'orderrefunds' => $orderrefunds,
        ]);
    }

    #[Route('/new', name: 'app_orderrefund_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderrefund = new Orderrefund();
        $form = $this->createForm(OrderrefundType::class, $orderrefund);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($orderrefund);
            $entityManager->flush();

            return $this->redirectToRoute('app_orderrefund_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orderrefund/new.html.twig', [
            'orderrefund' => $orderrefund,
            'form' => $form,
        ]);
    }

    #[Route('/{orderrefundId}', name: 'app_orderrefund_show', methods: ['GET'])]
    public function show(Orderrefund $orderrefund): Response
    {
        return $this->render('orderrefund/show.html.twig', [
            'orderrefund' => $orderrefund,
        ]);
    }

    #[Route('/{orderrefundId}/edit', name: 'app_orderrefund_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Orderrefund $orderrefund, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderrefundType::class, $orderrefund);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_orderrefund_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orderrefund/edit.html.twig', [
            'orderrefund' => $orderrefund,
            'form' => $form,
        ]);
    }

    #[Route('/{orderrefundId}', name: 'app_orderrefund_delete', methods: ['POST'])]
    public function delete(Request $request, Orderrefund $orderrefund, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderrefund->getOrderrefundId(), $request->request->get('_token'))) {
            $entityManager->remove($orderrefund);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_orderrefund_index', [], Response::HTTP_SEE_OTHER);
    }
}
