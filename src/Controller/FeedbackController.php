<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    private User $connectedUser;

   
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }
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
        $feedbacks = $entityManager
            ->getRepository(Feedback::class)
            ->createQueryBuilder('f')
            ->join('f.event', 'e')
            ->where('e.user = :userId')
            ->setParameter('userId', $this->connectedUser->getUserId())
            ->getQuery()
            ->getResult();


        return $this->render('feedback/artist/index.html.twig', [
            'feedback' => $feedbacks,
        ]);
    }

    #[Route('/artist/rate/{eventid}', name: 'app_feedback_new_artist', methods: ['GET', 'POST'])]
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

        return $this->renderForm('feedback/artist/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);


    
        // // Check if participation already exists
        // $existingParticipation = $entityManager->getRepository(Participation::class)->findOneBy([
        //     'user' => $connectedUserID,
        //     'event' => $eventid,
        // ]);
        
        if ($existingParticipation) {
            // Participation already exists, handle accordingly
            return new Response('Participation already exists', Response::HTTP_CONFLICT);
        }
    
        // Participation does not exist, create new participation
        $participation = new Participation();
        $participation->setUser($entityManager->getReference(User::class, $connectedUserID));
        $participation->setEvent($entityManager->getReference(Event::class, $eventid));
    
        $entityManager->persist($participation);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_participation_index_artist', [], Response::HTTP_SEE_OTHER);


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
    private function AdminAccess()
    {
        if ($this->connectedUser->getRole() == "admin") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
     private function ClientAccess()
    {
        if ($this->connectedUser->getRole() === "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    } 
    private function ArtistAccess()
    {
        if ($this->connectedUser->getRole() === "artist") {
           return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
    private function ArtistClientAccess()
    {
        if ($this->connectedUser->getRole() == "artist" || $this->connectedUser->getRole() == "client") {
            return true; // return a value to indicate that access is allowed
        } else {
            return false; // return a value to indicate that access is not allowed
        }
    }
}
