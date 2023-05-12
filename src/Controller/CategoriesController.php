<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/categories')]
class CategoriesController extends AbstractController
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
    #[Route('/', name: 'app_categories_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager
            ->getRepository(Categories::class)
            ->findAll();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }
//amir
    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{categoriesId}', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{categoriesId}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{categoriesId}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getCategoriesId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
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
