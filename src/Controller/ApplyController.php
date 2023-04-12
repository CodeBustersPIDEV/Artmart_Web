<?php

namespace App\Controller;
use Twilio\Rest\Client;
use App\Entity\Apply;
use App\Form\ApplyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/apply')]
class ApplyController extends AbstractController
{
    #[Route('/', name: 'app_apply_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $applies = $entityManager
            ->getRepository(Apply::class)
            ->findAll();

        return $this->render('apply/index.html.twig', [
            'applies' => $applies,
        ]);
    }

    #[Route('/apply', name: 'app_apply_pending', methods: ['GET'])]
    public function pending(EntityManagerInterface $entityManager): Response
    {
        $applies = $entityManager
            ->getRepository(Apply::class)
            ->findAll();
    
        return $this->render('apply/pending.html.twig', [
            'applies' => $applies,
        ]);
    }
    
    #[Route('/apply1', name: 'app_apply_accepted', methods: ['GET'])]
    public function accepted(EntityManagerInterface $entityManager): Response
    {
        $applies = $entityManager
            ->getRepository(Apply::class)
            ->findBy(['status' => ['in progress']]);
    
        return $this->render('apply/accepted.html.twig', [
            'applies' => $applies,
        ]);
    }


    #[Route('/apply2', name: 'app_apply_clients', methods: ['GET'])]
    public function clients(EntityManagerInterface $entityManager): Response
    {
        $applies = $entityManager
            ->getRepository(Apply::class)
            ->findBy(['status'=> ['Pending','refused']]);
    
        return $this->render('apply/clients.html.twig', [
            'applies' => $applies,
        ]);
    }
    

    #[Route('/apply3', name: 'app_apply_clientdone', methods: ['GET'])]
    public function clientdone(EntityManagerInterface $entityManager): Response
    {
        $applies = $entityManager
            ->getRepository(Apply::class)
            ->findBy(['status' => ['done','in progress']]);
    
        return $this->render('apply/clientdone.html.twig', [
            'applies' => $applies,
        ]);
    }

    #[Route('/new', name: 'app_apply_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $apply = new Apply();
        $form = $this->createForm(ApplyType::class, $apply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($apply);
            $entityManager->flush();

            return $this->redirectToRoute('app_apply_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('apply/new.html.twig', [
            'apply' => $apply,
            'form' => $form,
        ]);
    }

    #[Route('/{applyId}', name: 'app_apply_show', methods: ['GET'])]
    public function show(Apply $apply): Response
    {
        return $this->render('apply/show.html.twig', [
            'apply' => $apply,
        ]);
    }

    #[Route('/{applyId}/edit', name: 'app_apply_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Apply $apply, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApplyType::class, $apply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('apply/edit.html.twig', [
            'apply' => $apply,
            'form' => $form,
        ]);
    }

    #[Route('/{applyId}', name: 'app_apply_delete', methods: ['POST'])]
    public function delete(Request $request, Apply $apply, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apply->getApplyId(), $request->request->get('_token'))) {
            $entityManager->remove($apply);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customproduct_admin', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/apply/done', name: 'app_apply_mark_done', methods: ['POST'])]
    public function markDone(Request $request, EntityManagerInterface $entityManager): Response
    {
        $applyId = $request->request->get('applyId');
        
        $apply = $entityManager
            ->getRepository(Apply::class)
            ->find($applyId);
        
        if (!$apply) {
            throw $this->createNotFoundException('Apply not found');
        }
 
        $sid    = "AC85fdc289caf6aa747109220798d39394";
        $token  = "e100314f392f157e4341263440f1a7bc";
        $twilio = new Client($sid, $token);
    
        $message = $twilio->messages
          ->create("whatsapp:+21698238240", 
            array(
              "from" => "whatsapp:+14155238886",
              "body" => "your custom product is done"
            )
            );
        $apply->setStatus('done');
        $entityManager->flush();
        
        return $this->redirectToRoute('app_apply_accepted');
    }
    
    
    #[Route('/apply/acc', name: 'app_apply_mark_accept', methods: ['POST'])]
    public function markaccept(Request $request, EntityManagerInterface $entityManager): Response
    {
        $applyId = $request->request->get('applyId');
        
        $apply = $entityManager
            ->getRepository(Apply::class)
            ->find($applyId);
        
        if (!$apply) {
            throw $this->createNotFoundException('Apply not found');
        }
        $sid    = "AC85fdc289caf6aa747109220798d39394";
        $token  = "e100314f392f157e4341263440f1a7bc";
        $twilio = new Client($sid, $token);
    
        $message = $twilio->messages
          ->create("whatsapp:+21698238240", 
            array(
              "from" => "whatsapp:+14155238886",
              "body" => "your custom product demand is accepted"
            )
            );
        $apply->setStatus('in progress');
        $entityManager->flush();
        
        return $this->redirectToRoute('app_apply_clients');

    }

    #[Route('/apply/refused', name: 'app_apply_mark_refused', methods: ['POST'])]
    public function markrefused(Request $request, EntityManagerInterface $entityManager): Response
    {
        $applyId = $request->request->get('applyId');
        
        $apply = $entityManager
            ->getRepository(Apply::class)
            ->find($applyId);
        
        if (!$apply) {
            throw $this->createNotFoundException('Apply not found');
        }
 
        $apply->setStatus('refused');
        $entityManager->flush();
        
        return $this->redirectToRoute('app_apply_clients');
    }
}
