<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Form\ParticipationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
    #[Route('/admin', name: 'app_participation_index_admin', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $participations = $entityManager
            ->getRepository(Participation::class)
            ->findAll();

        return $this->render('participation/admin/index.html.twig', [
            'participations' => $participations,
        ]);
    }

    #[Route('/admin/new', name: 'app_participation_new_admin', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/admin/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{participationid}', name: 'app_participation_show_admin', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/admin/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/admin/{participationid}/edit', name: 'app_participation_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/admin/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{participationid}', name: 'app_participation_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getParticipationid(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index_admin', [], Response::HTTP_SEE_OTHER);
    }

// ****************************************************************************************
    
    #[Route('/artist', name: 'app_participation_index_artist', methods: ['GET'])]
    public function indexx(EntityManagerInterface $entityManager): Response
    {
        $participations = $entityManager
            ->getRepository(Participation::class)
            ->findAll();

        return $this->render('participation/artist/index.html.twig', [
            'participations' => $participations,
        ]);
    }

    #[Route('/artist/new', name: 'app_participation_new_artist', methods: ['GET', 'POST'])]
    public function neww(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/artist/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{participationid}', name: 'app_participation_show_artist', methods: ['GET'])]
    public function showw(Participation $participation): Response
    {
        return $this->render('participation/artist/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/artist/{participationid}/edit', name: 'app_participation_edit_artist', methods: ['GET', 'POST'])]
    public function editt(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/artist/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{participationid}', name: 'app_participation_delete_artist', methods: ['POST'])]
    public function deletee(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getParticipationid(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index_artist', [], Response::HTTP_SEE_OTHER);
    }

// ****************************************************************************************
    
}
