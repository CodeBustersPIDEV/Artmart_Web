<?php

namespace App\Controller;

use App\Entity\Salesreport;
use App\Form\SalesreportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/salesreport')]
class SalesreportController extends AbstractController
{
    #[Route('/', name: 'app_salesreport_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $salesreports = $entityManager
            ->getRepository(Salesreport::class)
            ->findAll();

        return $this->render('salesreport/index.html.twig', [
            'salesreports' => $salesreports,
        ]);
    }

    #[Route('/new', name: 'app_salesreport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salesreport = new Salesreport();
        $form = $this->createForm(SalesreportType::class, $salesreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($salesreport);
            $entityManager->flush();

            return $this->redirectToRoute('app_salesreport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salesreport/new.html.twig', [
            'salesreport' => $salesreport,
            'form' => $form,
        ]);
    }

    #[Route('/{salesreportId}', name: 'app_salesreport_show', methods: ['GET'])]
    public function show(Salesreport $salesreport): Response
    {
        return $this->render('salesreport/show.html.twig', [
            'salesreport' => $salesreport,
        ]);
    }

    #[Route('/{salesreportId}/edit', name: 'app_salesreport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salesreport $salesreport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalesreportType::class, $salesreport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_salesreport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salesreport/edit.html.twig', [
            'salesreport' => $salesreport,
            'form' => $form,
        ]);
    }

    #[Route('/{salesreportId}', name: 'app_salesreport_delete', methods: ['POST'])]
    public function delete(Request $request, Salesreport $salesreport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salesreport->getSalesreportId(), $request->request->get('_token'))) {
            $entityManager->remove($salesreport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_salesreport_index', [], Response::HTTP_SEE_OTHER);
    }
}
