<?php

namespace App\Controller;

use App\Entity\Blogcategories;
use App\Form\BlogcategoriesType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blogcategories')]
class BlogcategoriesController extends AbstractController
{
    #[Route('/', name: 'app_blogcategories_index', methods: ['GET'])]
    public function index(EntityManager $entityManager): Response
    {
        $blogcategories = $entityManager
            ->getRepository(Blogcategories::class)
            ->findAll();

        return $this->render('blogcategories/index.html.twig', [
            'blogcategories' => $blogcategories,
        ]);
    }

    #[Route('/new', name: 'app_blogcategories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManager $entityManager): Response
    {
        $blogcategory = new Blogcategories();
        $form = $this->createForm(BlogcategoriesType::class, $blogcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blogcategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_blogcategories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blogcategories/new.html.twig', [
            'blogcategory' => $blogcategory,
            'form' => $form,
        ]);
    }

    #[Route('/{categoriesId}', name: 'app_blogcategories_show', methods: ['GET'])]
    public function show(Blogcategories $blogcategory): Response
    {
        return $this->render('blogcategories/show.html.twig', [
            'blogcategory' => $blogcategory,
        ]);
    }

    #[Route('/{categoriesId}/edit', name: 'app_blogcategories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blogcategories $blogcategory, EntityManager $entityManager): Response
    {
        $form = $this->createForm(BlogcategoriesType::class, $blogcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blogcategories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blogcategories/edit.html.twig', [
            'blogcategory' => $blogcategory,
            'form' => $form,
        ]);
    }

    #[Route('/{categoriesId}', name: 'app_blogcategories_delete', methods: ['POST'])]
    public function delete(Request $request, Blogcategories $blogcategory, EntityManager $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $blogcategory->getCategoriesId(), $request->request->get('_token'))) {
            $entityManager->remove($blogcategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blogcategories_index', [], Response::HTTP_SEE_OTHER);
    }
}
