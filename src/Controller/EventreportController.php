<?php

namespace App\Controller;

use App\Entity\Eventreport;
use App\Form\EventreportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/eventreport')]
class EventreportController extends AbstractController
{
    #[Route('/admin', name: 'app_eventreport_index_admin', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $eventreports = $entityManager
            ->getRepository(Eventreport::class)
            ->findAll();

        return $this->render('eventreport/admin/index.html.twig', [
            'eventreports' => $eventreports,
        ]);
    }

    #[Route('/newAdmin', name: 'app_eventreport_new_admin', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventreport = new Eventreport();
        $form = $this->createForm(EventreportType::class, $eventreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eventreport);
            $entityManager->flush();

            return $this->redirectToRoute('app_eventreport_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eventreport/admin/new.html.twig', [
            'eventreport' => $eventreport,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{reportid}', name: 'app_eventreport_show_admin', methods: ['GET'])]
    public function show(Eventreport $eventreport): Response
    {
        return $this->render('eventreport/admin/show.html.twig', [
            'eventreport' => $eventreport,
        ]);
    }

    #[Route('/admin/{reportid}/edit', name: 'app_eventreport_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eventreport $eventreport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventreportType::class, $eventreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eventreport_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eventreport/admin/edit.html.twig', [
            'eventreport' => $eventreport,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{reportid}', name: 'app_eventreport_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Eventreport $eventreport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventreport->getReportid(), $request->request->get('_token'))) {
            $entityManager->remove($eventreport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eventreport_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    /************************************************************************************ */

    #[Route('/artist', name: 'app_eventreport_index_artist', methods: ['GET'])]
    public function indexx(EntityManagerInterface $entityManager): Response
    {
        $eventreports = $entityManager
            ->getRepository(Eventreport::class)
            ->findAll();

        return $this->render('eventreport/artist/index.html.twig', [
            'eventreports' => $eventreports,
        ]);
    }

    #[Route('/artist/new', name: 'app_eventreport_new_artist', methods: ['GET', 'POST'])]
    public function neww(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventreport = new Eventreport();
        $form = $this->createForm(EventreportType::class, $eventreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eventreport);
            $entityManager->flush();

            return $this->redirectToRoute('app_eventreport_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eventreport/artist/new.html.twig', [
            'eventreport' => $eventreport,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{reportid}', name: 'app_eventreport_show_artist', methods: ['GET'])]
    public function showw(Eventreport $eventreport): Response
    {
        return $this->render('eventreport/artist/show.html.twig', [
            'eventreport' => $eventreport,
        ]);
    }

    #[Route('/artist/{reportid}/edit', name: 'app_eventreport_edit_artist', methods: ['GET', 'POST'])]
    public function editt(Request $request, Eventreport $eventreport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventreportType::class, $eventreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eventreport_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eventreport/artist/edit.html.twig', [
            'eventreport' => $eventreport,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{reportid}', name: 'app_eventreport_delete_artist', methods: ['POST'])]
    public function deletee(Request $request, Eventreport $eventreport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventreport->getReportid(), $request->request->get('_token'))) {
            $entityManager->remove($eventreport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eventreport_index_artist', [], Response::HTTP_SEE_OTHER);
    }

    /************************************************************************************ */


}
