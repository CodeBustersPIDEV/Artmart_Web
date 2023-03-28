<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\ReceiptType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/receipt')]
class ReceiptController extends AbstractController
{
    #[Route('/', name: 'app_receipt_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $receipts = $entityManager
            ->getRepository(Receipt::class)
            ->findAll();

        return $this->render('receipt/index.html.twig', [
            'receipts' => $receipts,
        ]);
    }

    #[Route('/new', name: 'app_receipt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $receipt = new Receipt();
        $form = $this->createForm(ReceiptType::class, $receipt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($receipt);
            $entityManager->flush();

            return $this->redirectToRoute('app_receipt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('receipt/new.html.twig', [
            'receipt' => $receipt,
            'form' => $form,
        ]);
    }

    #[Route('/{receiptId}', name: 'app_receipt_show', methods: ['GET'])]
    public function show(Receipt $receipt): Response
    {
        return $this->render('receipt/show.html.twig', [
            'receipt' => $receipt,
        ]);
    }

    #[Route('/{receiptId}/edit', name: 'app_receipt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Receipt $receipt, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReceiptType::class, $receipt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_receipt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('receipt/edit.html.twig', [
            'receipt' => $receipt,
            'form' => $form,
        ]);
    }

    #[Route('/{receiptId}', name: 'app_receipt_delete', methods: ['POST'])]
    public function delete(Request $request, Receipt $receipt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$receipt->getReceiptId(), $request->request->get('_token'))) {
            $entityManager->remove($receipt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_receipt_index', [], Response::HTTP_SEE_OTHER);
    }
}
