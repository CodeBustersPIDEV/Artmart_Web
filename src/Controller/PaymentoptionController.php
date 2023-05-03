<?php

namespace App\Controller;

use App\Entity\Paymentoption;
use App\Form\PaymentoptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/paymentoption')]
class PaymentoptionController extends AbstractController
{
    private User $connectedUser;

     
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }
    #[Route('/', name: 'app_paymentoption_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $paymentoptions = $entityManager
            ->getRepository(Paymentoption::class)
            ->findAll();

        return $this->render('paymentoption/index.html.twig', [
            'paymentoptions' => $paymentoptions,
        ]);
    }

    #[Route('/new', name: 'app_paymentoption_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paymentoption = new Paymentoption();
        $form = $this->createForm(PaymentoptionType::class, $paymentoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentoption);
            $entityManager->flush();

            return $this->redirectToRoute('app_paymentoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paymentoption/new.html.twig', [
            'paymentoption' => $paymentoption,
            'form' => $form,
        ]);
    }

    #[Route('/{paymentoptionId}', name: 'app_paymentoption_show', methods: ['GET'])]
    public function show(Paymentoption $paymentoption): Response
    {
        return $this->render('paymentoption/show.html.twig', [
            'paymentoption' => $paymentoption,
        ]);
    }

    #[Route('/{paymentoptionId}/edit', name: 'app_paymentoption_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paymentoption $paymentoption, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentoptionType::class, $paymentoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_paymentoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paymentoption/edit.html.twig', [
            'paymentoption' => $paymentoption,
            'form' => $form,
        ]);
    }

    #[Route('/{paymentoptionId}', name: 'app_paymentoption_delete', methods: ['POST'])]
    public function delete(Request $request, Paymentoption $paymentoption, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentoption->getPaymentoptionId(), $request->request->get('_token'))) {
            $entityManager->remove($paymentoption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_paymentoption_index', [], Response::HTTP_SEE_OTHER);
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
