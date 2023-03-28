<?php

namespace App\Controller;

use App\Entity\Orderupdate;
use App\Form\OrderupdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orderupdate')]
class OrderupdateController extends AbstractController
{
    #[Route('/', name: 'app_orderupdate_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $orderupdates = $entityManager
            ->getRepository(Orderupdate::class)
            ->findAll();

        return $this->render('orderupdate/index.html.twig', [
            'orderupdates' => $orderupdates,
        ]);
    }

    #[Route('/new', name: 'app_orderupdate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderupdate = new Orderupdate();
        $form = $this->createForm(OrderupdateType::class, $orderupdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($orderupdate);
            $entityManager->flush();

            return $this->redirectToRoute('app_orderupdate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orderupdate/new.html.twig', [
            'orderupdate' => $orderupdate,
            'form' => $form,
        ]);
    }

    #[Route('/{orderupdateId}', name: 'app_orderupdate_show', methods: ['GET'])]
    public function show(Orderupdate $orderupdate): Response
    {
        return $this->render('orderupdate/show.html.twig', [
            'orderupdate' => $orderupdate,
        ]);
    }

    #[Route('/{orderupdateId}/edit', name: 'app_orderupdate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Orderupdate $orderupdate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderupdateType::class, $orderupdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_orderupdate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('orderupdate/edit.html.twig', [
            'orderupdate' => $orderupdate,
            'form' => $form,
        ]);
    }

    #[Route('/{orderupdateId}', name: 'app_orderupdate_delete', methods: ['POST'])]
    public function delete(Request $request, Orderupdate $orderupdate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderupdate->getOrderupdateId(), $request->request->get('_token'))) {
            $entityManager->remove($orderupdate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_orderupdate_index', [], Response::HTTP_SEE_OTHER);
    }
}
