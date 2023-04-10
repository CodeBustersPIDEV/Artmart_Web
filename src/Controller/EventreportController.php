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
    #[Route('/', name: 'app_eventreport_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $eventreports = $entityManager
            ->getRepository(Eventreport::class)
            ->findAll();

        return $this->render('eventreport/index.html.twig', [
            'eventreports' => $eventreports,
        ]);
    }

    #[Route('/new', name: 'app_eventreport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventreport = new Eventreport();
        $form = $this->createForm(EventreportType::class, $eventreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eventreport);
            $entityManager->flush();

            return $this->redirectToRoute('app_eventreport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eventreport/new.html.twig', [
            'eventreport' => $eventreport,
            'form' => $form,
        ]);
    }

    #[Route('/{reportid}', name: 'app_eventreport_show', methods: ['GET'])]
    public function show(Eventreport $eventreport): Response
    {
        return $this->render('eventreport/show.html.twig', [
            'eventreport' => $eventreport,
        ]);
    }

    #[Route('/{reportid}/edit', name: 'app_eventreport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eventreport $eventreport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventreportType::class, $eventreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eventreport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eventreport/edit.html.twig', [
            'eventreport' => $eventreport,
            'form' => $form,
        ]);
    }

    #[Route('/{reportid}', name: 'app_eventreport_delete', methods: ['POST'])]
    public function delete(Request $request, Eventreport $eventreport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventreport->getReportid(), $request->request->get('_token'))) {
            $entityManager->remove($eventreport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eventreport_index', [], Response::HTTP_SEE_OTHER);
    }
}
