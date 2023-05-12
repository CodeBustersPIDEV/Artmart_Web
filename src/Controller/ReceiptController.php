<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\ReceiptType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/receipt')]
class ReceiptController extends AbstractController
{ private User $connectedUser;

    
   
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }
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
    private function AdminAccess()
    {
        if ($this->connectedUser->getRole() == "admin") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
     private function ClientAccess()
    {
        if ($this->connectedUser->getRole() === "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    } 
    private function ArtistAccess()
    {
        if ($this->connectedUser->getRole() === "artist") {
           return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
    private function ArtistClientAccess()
    {
        if ($this->connectedUser->getRole() == "artist" || $this->connectedUser->getRole() == "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
}
