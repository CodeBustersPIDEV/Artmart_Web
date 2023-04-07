<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Form\Tags1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tags')]
class TagsController extends AbstractController
{
    #[Route('/', name: 'app_tags_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tags = $entityManager
            ->getRepository(Tags::class)
            ->findAll();

        return $this->render('tags/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/new', name: 'app_tags_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tags();
        $form = $this->createForm(Tags1Type::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tags/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{tagsId}', name: 'app_tags_show', methods: ['GET'])]
    public function show(Tags $tag): Response
    {
        return $this->render('tags/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/{tagsId}/edit', name: 'app_tags_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Tags1Type::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tags/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{tagsId}', name: 'app_tags_delete', methods: ['POST'])]
    public function delete(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getTagsId(), $request->request->get('_token'))) {
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
    }
}
