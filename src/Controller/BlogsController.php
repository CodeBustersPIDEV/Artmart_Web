<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\Media;
use App\Form\BlogsType;
use App\Repository\BlogsRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blogs')]
class BlogsController extends AbstractController
{
    #[Route('/', name: 'app_blogs_index', methods: ['GET'])]
    public function index(BlogsRepository $blogsRepository): Response
    {
        return $this->render('blogs/index.html.twig', [
            'blogs' => $blogsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_blogs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BlogsRepository $blogsRepository): Response
    {
        $blog = new Blogs();
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogsRepository->save($blog, true);

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blogs/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{blogs_ID}', name: 'app_blogs_show', methods: ['GET'])]
    public function show(Blogs $blog, MediaRepository $MediaRepository): Response
    {
        return $this->render('blogs/show.html.twig', [
            'blog' => $blog,
            'blog_media' => $MediaRepository->findOneMediaByBlogID($blog->getBlogsId()),
        ]);
    }

    #[Route('/{blogs_ID}/edit', name: 'app_blogs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blogs $blog, BlogsRepository $blogsRepository): Response
    {
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogsRepository->save($blog, true);

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blogs/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{blogs_ID}', name: 'app_blogs_delete', methods: ['POST'])]
    public function delete(Request $request, Blogs $blog, BlogsRepository $blogsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $blog->getBlogsID(), $request->request->get('_token'))) {
            $blogsRepository->remove($blog, true);
        }

        return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
    }
}
