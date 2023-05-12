<?php

namespace App\Controller;

use App\Entity\Orderstatus;
use App\Form\OrderstatusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/orderstatus')]
class OrderstatusController extends AbstractController
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
    #[Route('/', name: 'app_orderstatus_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $orderstatuses = $entityManager
            ->getRepository(Orderstatus::class)
            ->findAll();

        return $this->render('orderstatus/index.html.twig', [
            'orderstatuses' => $orderstatuses,
        ]);
    }

    #[Route('/new', name: 'app_orderstatus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderstatus = new Orderstatus();
        $form = $this->createForm(OrderstatusType::class, $orderstatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($orderstatus);
            $entityManager->flush();

            return $this->redirectToRoute('app_orderstatus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orderstatus/new.html.twig', [
            'orderstatus' => $orderstatus,
            'form' => $form,
        ]);
    }

    #[Route('/{orderstatusId}', name: 'app_orderstatus_show', methods: ['GET'])]
    public function show(Orderstatus $orderstatus): Response
    {
        return $this->render('orderstatus/show.html.twig', [
            'orderstatus' => $orderstatus,
        ]);
    }

    #[Route('/{orderstatusId}/edit', name: 'app_orderstatus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Orderstatus $orderstatus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderstatusType::class, $orderstatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_orderstatus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orderstatus/edit.html.twig', [
            'orderstatus' => $orderstatus,
            'form' => $form,
        ]);
    }

    #[Route('/{orderstatusId}', name: 'app_orderstatus_delete', methods: ['POST'])]
    public function delete(Request $request, Orderstatus $orderstatus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderstatus->getOrderstatusId(), $request->request->get('_token'))) {
            $entityManager->remove($orderstatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_orderstatus_index', [], Response::HTTP_SEE_OTHER);
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
