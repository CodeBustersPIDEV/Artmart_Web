<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Shippingoption;
use App\Form\ShippingoptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/shippingoption')]
class ShippingoptionController extends AbstractController
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
    #[Route('/', name: 'app_shippingoption_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,UserRepository $userRepository,Request $request): Response
    {
        $shippingoptions = $entityManager
            ->getRepository(Shippingoption::class)
            ->findAll();

        return $this->render('shippingoption/index.html.twig', [
            'shippingoptions' => $shippingoptions,
        ]);
      
    }

    #[Route('/new', name: 'app_shippingoption_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shippingoption = new Shippingoption();
        $form = $this->createForm(ShippingoptionType::class, $shippingoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shippingoption);
            $entityManager->flush();

            return $this->redirectToRoute('app_shippingoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('shippingoption/new.html.twig', [
            'shippingoption' => $shippingoption,
            'form' => $form,
        ]);
    }

    #[Route('/{shippingoptionId}', name: 'app_shippingoption_show', methods: ['GET'])]
    public function show(Shippingoption $shippingoption): Response
    {
        return $this->render('shippingoption/show.html.twig', [
            'shippingoption' => $shippingoption,
        ]);
    }

    #[Route('/{shippingoptionId}/edit', name: 'app_shippingoption_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shippingoption $shippingoption, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShippingoptionType::class, $shippingoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shippingoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('shippingoption/edit.html.twig', [
            'shippingoption' => $shippingoption,
            'form' => $form,
        ]);
    }

    #[Route('/{shippingoptionId}', name: 'app_shippingoption_delete', methods: ['POST'])]
    public function delete(Request $request, Shippingoption $shippingoption, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shippingoption->getShippingoptionId(), $request->request->get('_token'))) {
            $entityManager->remove($shippingoption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shippingoption_index', [], Response::HTTP_SEE_OTHER);
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
