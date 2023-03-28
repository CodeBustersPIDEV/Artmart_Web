<?php

namespace App\Controller;

use App\Entity\Shippingoption;
use App\Form\ShippingoptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shippingoption')]
class ShippingoptionController extends AbstractController
{
    #[Route('/', name: 'app_shippingoption_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $shippingoptions = $entityManager
            ->getRepository(Shippingoption::class)
            ->findAll();

        return $this->render('shippingoption/index.html.twig', [
            'shippingoptions' => $shippingoptions,
        ]);
    }

    #[Route('/new', name: 'app_shippingoption_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shippingoption = new Shippingoption();
        $form = $this->createForm(ShippingoptionType::class, $shippingoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shippingoption);
            $entityManager->flush();

            return $this->redirectToRoute('app_shippingoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('shippingoption/new.html.twig', [
            'shippingoption' => $shippingoption,
            'form' => $form,
        ]);
    }

    #[Route('/{shippingoptionId}', name: 'app_shippingoption_show', methods: ['GET'])]
    public function show(Shippingoption $shippingoption): Response
    {
        return $this->render('shippingoption/show.html.twig', [
            'shippingoption' => $shippingoption,
        ]);
    }

    #[Route('/{shippingoptionId}/edit', name: 'app_shippingoption_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shippingoption $shippingoption, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShippingoptionType::class, $shippingoption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shippingoption_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('shippingoption/edit.html.twig', [
            'shippingoption' => $shippingoption,
            'form' => $form,
        ]);
    }

    #[Route('/{shippingoptionId}', name: 'app_shippingoption_delete', methods: ['POST'])]
    public function delete(Request $request, Shippingoption $shippingoption, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shippingoption->getShippingoptionId(), $request->request->get('_token'))) {
            $entityManager->remove($shippingoption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shippingoption_index', [], Response::HTTP_SEE_OTHER);
    }
}
