<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    #[Route('/admin', name: 'app_feedback_index_admin', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $feedback = $entityManager
            ->getRepository(Feedback::class)
            ->findAll();

        return $this->render('feedback/admin/index.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/admin/new', name: 'app_feedback_new_admin', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($feedback);
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('feedback/admin/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{feedbackid}', name: 'app_feedback_show_admin', methods: ['GET'])]
    public function show(Feedback $feedback): Response
    {
        return $this->render('feedback/admin/show.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/admin/{feedbackid}/edit', name: 'app_feedback_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('feedback/admin/edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{feedbackid}', name: 'app_feedback_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getFeedbackid(), $request->request->get('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feedback_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    /*****************************************************************************************/
    
    #[Route('/artist', name: 'app_feedback_index_artist', methods: ['GET'])]
    public function indexx(EntityManagerInterface $entityManager): Response
    {
        $feedback = $entityManager
            ->getRepository(Feedback::class)
            ->findAll();

        return $this->render('feedback/artist/index.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/artist/new', name: 'app_feedback_new_artist', methods: ['GET', 'POST'])]
    public function neww(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($feedback);
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('feedback/admin/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{feedbackid}', name: 'app_feedback_show_artist', methods: ['GET'])]
    public function showw(Feedback $feedback): Response
    {
        return $this->render('feedback/artist/show.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/artist/{feedbackid}/edit', name: 'app_feedback_edit_artist', methods: ['GET', 'POST'])]
    public function editt(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('feedback/artist/edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{feedbackid}', name: 'app_feedback_delete_artist', methods: ['POST'])]
    public function deletee(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getFeedbackid(), $request->request->get('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feedback_index_artist', [], Response::HTTP_SEE_OTHER);
    }

    /*****************************************************************************************/

}
