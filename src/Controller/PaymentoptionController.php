<?php

namespace App\Controller;

use App\Entity\Paymentoption;
use App\Form\PaymentoptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/paymentoption')]
class PaymentoptionController extends AbstractController
{
    #[Route('/', name: 'app_paymentoption_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $paymentoptions = $entityManager
            ->getRepository(Paymentoption::class)
            ->findAll();

        return $this->render('paymentoption/index.html.twig', [
            'paymentoptions' => $paymentoptions,
        ]);
    }

    #[Route('/new', name: 'app_paymentoption_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paymentoption = new Paymentoption();
        $form = $this->createForm(PaymentoptionType::class, $paymentoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentoption);
            $entityManager->flush();

            return $this->redirectToRoute('app_paymentoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paymentoption/new.html.twig', [
            'paymentoption' => $paymentoption,
            'form' => $form,
        ]);
    }

    #[Route('/{paymentoptionId}', name: 'app_paymentoption_show', methods: ['GET'])]
    public function show(Paymentoption $paymentoption): Response
    {
        return $this->render('paymentoption/show.html.twig', [
            'paymentoption' => $paymentoption,
        ]);
    }

    #[Route('/{paymentoptionId}/edit', name: 'app_paymentoption_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paymentoption $paymentoption, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentoptionType::class, $paymentoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_paymentoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paymentoption/edit.html.twig', [
            'paymentoption' => $paymentoption,
            'form' => $form,
        ]);
    }

    #[Route('/{paymentoptionId}', name: 'app_paymentoption_delete', methods: ['POST'])]
    public function delete(Request $request, Paymentoption $paymentoption, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentoption->getPaymentoptionId(), $request->request->get('_token'))) {
            $entityManager->remove($paymentoption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_paymentoption_index', [], Response::HTTP_SEE_OTHER);
    }
}
